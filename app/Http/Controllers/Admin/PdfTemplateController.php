<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Helpers\ListHelper;
use App\Models\PdfTemplate;
use Illuminate\Http\Request;
use App\Services\PdfGenerator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Database\Factories\OrderFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Validations\PdfTemplateUpateRequest;
use App\Http\Requests\Validations\PdfTemplateCreateRequest;

class PdfTemplateController extends Controller
{
    /**
     * Display a listing of custom invoice templates.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $templates = PdfTemplate::all();

        return view('admin.pdf_template.index', compact('templates'));
    }

    /**
     * Create custom invoice template.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $types = ListHelper::getTempletTypes();

        return view('admin.pdf_template._create', compact('types'));
    }

    /**
     * Upload a custom invoice template to the server.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PdfTemplateCreateRequest $request)
    {
        if (!str_ends_with($request->file('template')->getClientOriginalName(), '.blade.php')) {
            return redirect()->back()->withErrors(trans('messages.uploaded_file_not_blade_file'));
        }

        if (config('app.demo') == true) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        try {
            $file = $request->file('template');
            // $file_name_without_extension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $file_name = $file->getClientOriginalName();

            // $filename =  $file_name_without_extension . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $storage_location = $file->storeAs('public/pdf_templates', $file_name);

            PdfTemplate::create([
                'name' => $request->input('name'),
                'shop_id' => $request->shop_id,
                'description' => $request->input('description'),
                'file_name' => $file_name,
                'path' => $storage_location,
                'type' => $request->input('type'),
                'active' => $request->input('active'),
                'is_default' => $request->input('is_default'),
                // 'shop_id' => Auth::user()->isMerchant(),
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', trans('messages.created', ['model' => trans('app.invoice_template')]));
    }

    /**
     * Edit a custom invoice template.
     *
     * @return \Illuminate\View\View
     */
    public function edit(PdfTemplate $pdfTemplate)
    {
        $types = ListHelper::getTempletTypes();

        return view('admin.pdf_template._edit', compact('pdfTemplate', 'types'));
    }

    /**
     * Update a custom invoice template.
     *
     * @param PdfTemplateCreateRequest $request
     * @param PdfTemplate $pdfTemplate
     * @return \Illuminate\View\View
     */
    public function update(PdfTemplateUpateRequest $request, PdfTemplate $pdfTemplate)
    {
        if ($request->hasFile('template') && !str_ends_with($request->file('template')->getClientOriginalName(), '.blade.php')) {
            return redirect()->back()->withErrors(trans('messages.uploaded_file_not_blade_file'));
        }

        if (config('app.demo') == true) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        try {
            $file_name = $pdfTemplate->file_name;
            $storage_location = $pdfTemplate->storage_location;

            if ($request->hasFile('template')) {
                Storage::delete($pdfTemplate->path); // Delete old file

                $file = $request->file('template');
                $file_name = $file->getClientOriginalName();
                $storage_location = $file->storeAs('pdf_templates', $file_name);
            }

            $pdfTemplate->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'file_name' => $file_name,
                'path' => $storage_location,
                'type' => $request->input('type'),
                'active' => $request->input('active'),
                'is_default' => $request->input('is_default'),
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', trans('messages.updated', ['model' => trans('app.invoice_template')]));
    }

    /**
     * Show the PDF of a given invoice template.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param PdfTemplate $pdfTemplate
     * @return \Illuminate\Http\Response a streamed response for streaming PDF
     */
    public function show(Request $request, PdfTemplate $pdfTemplate)
    {
        $pdfGenerator = new PdfGenerator();
        $data = $this->getOrderData();

        return $pdfGenerator->generatePdfFromTemplate($data, $pdfTemplate, 'a4', 'download');
    }

    /**
     * Download the blade template for the pdfTemplate.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request, PdfTemplate $pdfTemplate)
    {
        $headers = [
            'Content-Type: application/force-download',
            'Content-Disposition: attachment; filename="' . $pdfTemplate->name . '"',
        ];

        $name = $pdfTemplate->name . '.blade.php';

        return response()->download($pdfTemplate->path, $name, $headers);
    }

    /**
     * Destroy the template
     *
     * @param Request $request
     * @param PdfTemplate $pdfTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, PdfTemplate $pdfTemplate)
    {
        try {
            if ($pdfTemplate->path) {
                Storage::delete($pdfTemplate->path);
            }

            $pdfTemplate->delete();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', trans('messages.failed'));
        }

        return redirect()->back()->with('success', trans('messages.deleted', ['model' => trans('app.invoice_template')]));
    }

    private function getOrderData()
    {
        $data = Auth::user()->isMerchant() ? Order::mine()->first() : Order::first();

        if (!$data) { // Create a dummy order if there is no order
            $orderFactory = new OrderFactory();
            $data = $orderFactory->create();
        }

        return $data;
    }
}
