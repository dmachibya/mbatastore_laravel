<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Events\Order\OrderCreated;
use App\Events\Order\OrderFulfilled;
use App\Events\Order\OrderUpdated;
use App\Helpers\ListHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateOrderRequest;
use App\Http\Requests\Validations\FulfillOrderRequest;
use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Yajra\Datatables\Datatables;
use ZipArchive;

class OrderController extends Controller
{
    use Authorizable;

    private $model_name;

    private $order;

    /**
     * construct
     */
    public function __construct(OrderRepository $order)
    {
        parent::__construct();
        $this->model_name = trans('app.model.order');
        $this->order = $order;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $archives = $this->order->trashOnly();

        return view('admin.order.index', compact('archives'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function searchCustomer()
    {
        return view('admin.order._search_customer');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $data['customer'] = $this->order->getCustomer($request->input('customer_id'));

        $data['cart_lists'] = $this->order->getCartList($request->input('customer_id'));

        if ($request->has('cart_id')) {
            $data['cart'] = $this->order->getCart($request->input('cart_id'));
        }

        return view('admin.order.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateOrderRequest $request)
    {
        if (is_null($request->input('cart'))) {
            return back()->with('warning', trans('theme.notify.cart_empty'));
        }

        $order = $this->order->store($request);

        event(new OrderCreated($order));

        return redirect()->route('admin.order.order.index')
            ->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $order = $this->order->find($id);

        $order->load('inventories.images', 'activities.causer');

        $this->authorize('view', $order); // Check permission

        $address = $order->customer->primaryAddress();

        if (is_incevio_package_loaded('affiliate')) {
            $commissions = $order->affiliateCommissions()->get();

            return view('admin.order.show', compact('order', 'address', 'commissions'));
        }

        return view('admin.order.show', compact('order', 'address'));
    }

    /**
     * Display a page to process bulk order processing.
     * @param Request
     * @param int For filtering by payment status
     * @param int For filtering by order status
     * @return \Illuminate\Http\JsonResponse
     */
    public function showBulkProcess(Request $request, $paymentStatus = 0, $orderStatus = 0, $fulfilmentStatus = 0)
    {
        $orders = Order::query();

        if (Auth::user()->isFromMerchant()) {
            $orders->where('shop_id', Auth::user()->merchantId()); // Merchants must only see their own orders
        }

        if ($fulfilmentStatus != 0) {
            $orders->where('fulfilment_type', $fulfilmentStatus);
        }

        if ($paymentStatus == Order::PAYMENT_STATUS_PAID) {
            $orders->paid();
        } elseif ($paymentStatus == Order::PAYMENT_STATUS_UNPAID) {
            $orders->unpaid();
        }

        if ($orderStatus != 0) {
            $orders->where('order_status_id', $orderStatus);
        }

        $orders = $orders->orderBy('created_at', 'desc')->get();

        return Datatables::of($orders)
            ->editColumn('checkbox', function ($order) {
                return view('admin.partials.actions.order.checkbox', compact('order'));
            })
            ->addColumn('order', function ($order) {
                return view('admin.partials.actions.order.order', compact('order'));
            })
            ->addColumn('order_date', function ($order) {
                return view('admin.partials.actions.order.order_date', compact('order'));
            })
            ->editColumn('delivery_boy', function ($order) {
                return view('admin.partials.actions.order.delivery_boy', compact('order'));
            })
            ->editColumn('shop', function ($order) {
                return view('admin.partials.actions.order.shop', compact('order'));
            })
            ->editColumn('customer_name', function ($order) {
                return view('admin.partials.actions.order.customer_name', compact('order'));
            })
            ->editColumn('grand_total', function ($order) {
                return view('admin.partials.actions.order.grand_total', compact('order'));
            })
            ->editColumn('payment_status', function ($order) {
                return view('admin.partials.actions.order.payment_status', compact('order'));
            })
            ->editColumn('order_status', function ($order) {
                $order_statuses = ListHelper::order_statuses();
                return view('admin.partials.actions.order.order_status', compact('order', 'order_statuses'));
            })
            ->editColumn('option', function ($order) {
                return view('admin.partials.actions.order.option', compact('order'));
            })
            ->rawColumns(['checkbox', 'order', 'order_date', 'delivery_boy', 'shop', 'customer_name', 'grand_total', 'payment_status', 'option'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoice($id)
    {
        $order = $this->order->find($id);

        $this->authorize('view', $order); // Check permission

        return $order->invoice('download'); // Download the invoice
    }

    /**
     * Download invoices of all selected orders
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function downloadSelected(Request $request)
    {
        $filePaths = [];
        $folder_name = $this->getUniqueFolderNameForInvoice();
        $platform_title = get_platform_title();

        foreach ($request->ids as $id) {
            $order = Order::find($id);
            $this->authorize('view', $order); // Check permission

            $file_name = "{$platform_title}_{$order->order_number}.pdf";
            $file_path = public_path("invoice_tmp/{$folder_name}/{$file_name}");
            $folder_path = public_path("invoice_tmp/{$folder_name}");

            if (!file_exists($folder_path)) {
                mkdir($folder_path, 0777, true);
            }

            $order->invoice('save', $file_path); // Generate PDF

            // Store generated file paths for zipping and deletion
            array_push($filePaths, $file_path);
        }

        // Create ZIP archive
        $zip = new ZipArchive();
        $zipFileName = 'Invoices.zip';
        $zipFilePath = public_path("invoice_tmp/$folder_name/$zipFileName");

        // If a file at zipFilePath exists delete the existing file
        if (file_exists($zipFilePath)) {
            unlink($zipFilePath);
        }

        if ($zip->open($zipFilePath, ZipArchive::CREATE)) {
            foreach ($filePaths as $filePath) {
                $relativeName = basename($filePath);
                $zip->addFile($filePath, $relativeName);
            }
        }

        $zip->close();

        // Delete the files used to create the zip file
        foreach ($filePaths as $filePath) {
            \File::delete($filePath);
        }

        $zipFilePath = URL::to("/invoice_tmp/$folder_name/$zipFileName");

        $response = [
            'download' => trans('messages.created', ['model' => $this->model_name]),
            'download_url' => URL::to($zipFilePath),
            'download_file_name' => 'Invoices.zip',
        ];

        // Prepare response data
        if ($request->ajax()) {
            return response()->json($response);
        }

        return response()->json(['error' => trans('messages.failed')]);
    }

    /**
     * Show the fulfillment form for the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function fulfillment($id)
    {
        $order = $this->order->find($id);

        $this->authorize('fulfill', $order); // Check permission

        $carriers = ListHelper::carriers($order->shop_id);

        return view('admin.order._fulfill', compact('order', 'carriers'));
    }

    /**
     * Get list of delivery boys
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function deliveryBoys($id)
    {
        $order = $this->order->find($id);

        $deliveryboys = ListHelper::deliveryBoys($order->shop_id);

        return view('admin.order._assign_delivery_boy', compact('deliveryboys', 'order'));
    }

    /**
     * Assign a delivery boy to an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignDeliveryBoy(Request $request, $id)
    {
        $order = $this->order->find($id);

        $order->delivery_boy_id = $request->delivery_boy_id;
        $order->save();

        $deliveryBoy_token = optional($order->deliveryBoy)->fcm_token;

        if (!is_null($deliveryBoy_token)) {
            FCMService::send($deliveryBoy_token, [
                'title' => trans('notifications.order_assigned.subject', ['order' => $order->order_number]),
                'body' => trans('notifications.order_assigned.message'),
            ]);
        }

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $orderId
     * @return \Illuminate\View\View
     */
    public function edit(int $orderId)
    {
        $order = $this->order->find($orderId);

        $this->authorize('fulfill', $order);

        return view('admin.order._edit', compact('order'));
    }

    /**
     * Fulfill the order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fulfill(FulfillOrderRequest $request, $id)
    {
        $order = $this->order->find($id);

        $this->authorize('fulfill', $order); // Check permission

        $this->order->fulfill($request, $order);

        event(new OrderFulfilled($order, $request->filled('notify_customer')));

        if (config('shop_settings.auto_archive_order') && $order->isPaid()) {
            $this->order->trash($id);

            return redirect()->route('admin.order.order.index')
                ->with('success', trans('messages.fulfilled_and_archived'));
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Update Order Status of the selected orders
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $status
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function massAssignOrderStatus(Request $request)
    {
        $orders = Order::whereIn('id', $request->ids)->get();

        foreach ($orders as $order) {
            $this->authorize('fulfill', $order);

            $order->order_status_id = $request->status;
            $order->save();

            event(new OrderUpdated($order, $request->filled('notify_customer')));
        }

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.updated', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * updateOrderStatus the order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $order = $this->order->find($id);

        $this->authorize('fulfill', $order); // Check permission

        $this->order->updateOrderStatus($request, $order);

        event(new OrderUpdated($order, $request->filled('notify_customer')));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    public function adminNote($id)
    {
        $order = $this->order->find($id);

        $this->authorize('fulfill', $order); // Check permission

        return view('admin.order._edit_admin_note', compact('order'));
    }

    public function saveAdminNote(Request $request, $id)
    {
        $order = $this->order->find($id);

        // $this->authorize('fulfill', $order); // Check permission

        $this->order->updateAdminNote($request, $order);

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archive(Request $request, $id)
    {
        $this->order->trash($id);

        return redirect()->route('admin.order.order.index')
            ->with('success', trans('messages.archived', ['model' => $this->model_name]));
    }

    /**
     * Restore the specified resource from soft delete.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Request $request, $id)
    {
        $this->order->restore($id);

        return back()->with('success', trans('messages.restored', ['model' => $this->model_name]));
    }

    /**
     * Assign Payment Status of the given orders, Its uses the ajax middleware
     *
     * @param  \Illuminate\Http\Request  $request Request contains ids of checked/selected orders
     * @param  string|null  $assign  The payment status to assign (paid, unpaid, refunded)
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function massAssignPaymentStatus(Request $request, $assign = null)
    {
        $orders = Order::whereIn('id', $request->ids)->get();

        foreach ($orders as $order) {
            $this->authorize('fulfill', $order);

            switch ($assign) {
                case 'paid':
                    $order->markAsPaid();
                    break;
                case 'unpaid':
                    $order->markAsUnpaid();
                    break;
                case 'refunded':
                    $order->markAsRefunded();
                    break;
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.updated', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Toggle Payment Status of the given order, Its uses the ajax middleware
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function togglePaymentStatus(Request $request, $id)
    {
        if (Auth::user()->isFromMerchant() && !vendor_get_paid_directly()) {
            return back()->with('warning', trans('messages.failed', ['model' => $this->model_name]));
        }

        $order = $this->order->find($id);

        $this->authorize('fulfill', $order); // Check permission

        if ($order->isPaid()) {
            $order->markAsUnpaid();
        } else {
            $order->markAsPaid();
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $this->order->destroy($id);

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }

    /**
     * Empty the Trash the mass resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function emptyTrash(Request $request)
    {
        $this->order->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }

    /**
     * Get the unique folder name for invoice
     *
     * @return string
     */
    private function getUniqueFolderNameForInvoice()
    {
        return Auth::user()->isFromMerchant() ? 'merchant' . Auth::user()->merchantId() . 'shop' . Auth::user()->shop->id : 'admin';
    }
}
