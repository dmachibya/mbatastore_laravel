<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Attribute;
use App\Models\Inventory;
use App\Helpers\ListHelper;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Inventory\InventoryRepository;
use App\Http\Requests\Validations\AddInventoryRequest;
use App\Http\Requests\Validations\CreateInventoryRequest;
use App\Http\Requests\Validations\UpdateInventoryRequest;
use App\Http\Requests\Validations\CreateInventoryWithVariantRequest;

class InventoryController extends Controller
{
    private $model;

    private $inventory;

    /**
     * construct
     */
    public function __construct(InventoryRepository $inventory)
    {
        parent::__construct();

        $this->model = trans('app.model.inventory');

        $this->inventory = $inventory;
    }


    /**
     * Display a listing of the resource.
     *
     * @param  string|null  $type
     * @return \Illuminate\View\View
     */
    public function index($type = null)
    {
        $trashes = Inventory::withCount('variants')
            ->onlyTrashed()
            ->where('parent_id', null)
            ->latest();

        if (!Auth::user()->isFromPlatform()) {
            $trashes = $trashes->mine();
        }

        // Get auction items only
        if ($type == 'auction') {
            $trashes = $trashes->auction();
        }

        $trashes = $trashes->get();

        if ($type == 'digital') {
            return view('admin.inventory.index_digital', compact('trashes'));
        }

        if ($type == 'auction') {
            return view('auction::admin.index', compact('trashes'));
        }

        return view('admin.inventory.index', compact('trashes'));
    }

    /**
     * Get inventory with options to filter by type and status.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $status
     * @param string|null $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInventory(Request $request, $status = 'active', $type = null)
    {
        $inventory = Inventory::with('product', 'image')
            ->withCount('variants')
            ->where('parent_id', null)
            ->latest();

        if (!Auth::user()->isFromPlatform()) {
            $inventory = $inventory->mine();
        }

        if ($type == 'auction') {        // Get only auction items
            $inventory = $inventory->withCount('bids')->auction();
        }

        if ($status == 'active') {
            $inventory = $inventory->active();
        } elseif ($status == 'inactive') {
            $inventory = $inventory->inActive();
        } elseif ($status == 'outOfStock') {
            $inventory = $inventory->stockOut();
        }

        $inventory = $inventory->get();

        // Separate products by type when catalog mode is enabled
        $inventory = $inventory->filter(function ($item) use ($type) {
            if ($type === 'digital') {
                return  $item->product->downloadable;      // Include only digital products
            }

            if ($type === 'physical') {
                return  !$item->product->downloadable;      // Include only physical products
            }

            return true;
        });

        $data = Datatables::of($inventory)
            ->editColumn('checkbox', function ($inventory) {
                return view('admin.inventory.partials.checkbox', compact('inventory'));
            })
            ->editColumn('image', function ($inventory) {
                return view('admin.inventory.partials.image', compact('inventory'));
            })
            ->editColumn('quantity', function ($inventory) {
                return view('admin.inventory.partials.quantity', compact('inventory'));
            })
            ->editColumn('sku', function ($inventory) {
                return view('admin.inventory.partials.sku', compact('inventory'));
            })
            ->editColumn('title', function ($inventory) use ($type) {
                return view('admin.inventory.partials.title', compact('inventory', 'type'));
            })
            ->editColumn('condition', function ($inventory) {
                return view('admin.inventory.partials.condition', compact('inventory'));
            })
            ->editColumn('download_limit', function ($inventory) {
                return view('admin.inventory.partials.download_limit', compact('inventory'));
            });

        $rawColumns = ['image', 'sku', 'title', 'checkbox', 'option', 'download_limit'];

        if (is_incevio_package_loaded('pharmacy')) {
            $data = $data->editColumn('pharmacy', function ($inventory) {
                return view('pharmacy::admin._expiry_date', compact('inventory'));
            });

            $rawColumns[] = 'expiry_date';
        }

        if ($type == 'auction') {
            $data = $data->editColumn('base_price', function ($inventory) {
                return view('auction::admin._price', compact('inventory'));
            })->addColumn('option', function ($inventory) {
                return view('auction::admin._options', compact('inventory'));
            });

            $rawColumns[] = 'base_price';
        } else {
            $data = $data->editColumn('sale_price', function ($inventory) {
                return view('admin.inventory.partials.price', compact('inventory'));
            })->addColumn('option', function ($inventory) {
                return view('admin.inventory.partials.options', compact('inventory'));
            });

            $rawColumns[] = 'sale_price';
        }

        $rawColumns[] = 'option';

        if (config('system_settings.show_item_conditions')) {
            $data = $data->editColumn('condition', function ($inventory) {
                return view('admin.inventory.partials.condition', compact('inventory'));
            });

            $rawColumns[] = 'condition';
        }

        return $data->rawColumns($rawColumns)->make(true);
    }

    /**
     * Set variant for a product in the inventory.
     *
     * @param AddInventoryRequest $request The request object containing the inventory data.
     * @param Product $product The product for which the variant needs to be set.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse a modal view for setting variant attributes or redirect to edit page if inventory exists.
     */
    public function setVariant(AddInventoryRequest $request, Product $product)
    {
        $inStock = $this->inventory->checkInventoryExist($product->id);

        if ($inStock) {
            return redirect()->route('admin.stock.inventory.edit', $inStock->id)
                ->with('warning', trans('messages.inventory_exist'));
        }

        $product->load('categories.attrsList.attributeValues');

        $attributes = ListHelper::getAttributesBy($product);

        return view('admin.inventory._set_variant', compact('product', 'attributes'));
    }


    /**
     * Adds a new inventory item to the system.
     *
     * @param AddInventoryRequest $request The request object containing the inventory data.
     * @param int $id The ID of the product to add inventory for.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View A redirect or a view for setting variant attributes.
     */
    public function add(AddInventoryRequest $request, $id)
    {
        if (!$request->user()->shop->canAddMoreInventory()) {
            return redirect()->route('admin.stock.inventory.index')
                ->with('error', trans('messages.cant_add_more_inventory'));
        }

        $inStock = $this->inventory->checkInventoryExist($id);

        if ($inStock) {
            return redirect()->route('admin.stock.inventory.edit', $inStock->id)
                ->with('warning', trans('messages.inventory_exist'));
        }

        $product = Product::with('categories.attrsList.attributeValues')->findOrFail($id);

        $attributes = ListHelper::getAttributesBy($product);

        $linkable_items = ListHelper::inventories();

        $suppliers = ListHelper::suppliers();

        // When packaging module available
        $packagings = is_incevio_package_loaded('packaging') ? ListHelper::packagings() : null;

        return view('admin.inventory.create', compact('product', 'attributes', 'linkable_items', 'suppliers', 'packagings'));
    }


    /**
     * Adds a new inventory item with variants to the system.
     *
     * @param AddInventoryRequest $request The request object containing the inventory data.
     * @param int $id The ID of the product to add inventory for.
     * @return \Illuminate\View\View A view for setting variant attributes.
     */
    public function addWithVariant(AddInventoryRequest $request, $id)
    {
        if (!$request->user()->shop->canAddMoreInventory()) {
            return redirect()->route('admin.stock.inventory.index')
                ->with('error', trans('messages.cant_add_more_inventory'));
        }

        $variants = $this->inventory->confirmAttributes($request->except('_token'));

        $combinations = generate_combinations($variants);

        $attributes = Attribute::find(array_keys($variants))->pluck('name', 'id');

        $product = Product::with('categories.attrsList.attributeValues')->findOrFail($id);

        $linkable_items = ListHelper::inventories();

        $suppliers = ListHelper::suppliers();

        if (is_incevio_package_loaded('packaging')) {
            $packagings = ListHelper::packagings();

            return view('admin.inventory.createWithVariant', compact('combinations', 'attributes', 'linkable_items', 'suppliers', 'product', 'packagings'));
        }

        return view('admin.inventory.createWithVariant', compact('combinations', 'attributes', 'linkable_items', 'suppliers', 'product'));
    }

    /**
     * Add a product to inventory.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateInventoryRequest $request)
    {
        $this->authorize('create', Inventory::class); // Check permission

        $inventory = $this->inventory->store($request);

        $request->session()->flash('success', trans('messages.created', ['model' => $this->model]));

        return response()->json($this->getJsonParams($inventory));
    }

    /**
     * Add inventory with variants.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeWithVariant(CreateInventoryWithVariantRequest $request)
    {
        if (json_decode($request->input('product')) == null) { //If the json string is invalid
            $request->merge([
                'product' => $this->makeStringJsonCompatible($request->product)
            ]);
        }

        $this->inventory->storeWithVariant($request);

        return redirect()->route('admin.stock.inventory.index')
            ->with('success', trans('messages.created', ['model' => $this->model]));
    }

    /**
     * Display the specified inventory resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $inventory = $this->inventory->find($id);

        return view('admin.inventory._show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified inventory resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $inventory = $this->inventory->find($id);

        $inventoryVariant = Inventory::where('parent_id', $inventory->id)->get();

        $preview = $inventory->previewImages();

        $product = Product::with('categories.attrsList.attributeValues')->findOrFail($inventory->product_id);

        $attributes = ListHelper::getAttributesBy($product);

        $linkable_items = ListHelper::inventories();

        $suppliers = ListHelper::suppliers();

        if (is_incevio_package_loaded('wholesale')) {
            $inventory->wholesale = get_wholesale_item_prices($inventory->id);
        }

        $packagings = is_incevio_package_loaded('packaging') ? ListHelper::packagings() : null;

        return view('admin.inventory.edit', compact('inventory', 'inventoryVariant', 'product', 'preview', 'attributes', 'linkable_items', 'suppliers', 'packagings'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function editQtt($id)
    {
        $inventory = $this->inventory->find($id);

        $this->authorize('update', $inventory); // Check permission

        return view('admin.inventory._editQtt', compact('inventory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInventoryRequest $request, $id)
    {
        $inventory = Inventory::find($id);

        // Skip the permission checking for platform users when for inspectable item update
        if (!Auth::user()->isFromPlatform()) {
            $this->authorize('update', $inventory); // Check permission
        }

        if ($request->hasFile('digital_file')) {
            $inventory->flushAttachments();
            $inventory->saveAttachments($request->file('digital_file'));
        }

        if ($request->input('delete_image')) {
            if (is_array($request->delete_image)) {
                foreach ($request->delete_image as $type => $value) {
                    $inventory->deleteImageTypeOf($type);
                }
            } else {
                $inventory->deleteImage();
            }
        }

        // Can have multiple images
        if ($request->hasFile('images')) {
            foreach ($request->images as $type => $file) {
                $inventory->updateImage($file, $type);
            }
        }

        // When got a single image
        if ($request->hasFile('image')) {
            $inventory->updateImage($request->image);
        }

        $inventory = $this->inventory->update($request, $id);

        $commonInfo = [
            'title' => $request->title ?? $request->name,
            'warehouse_id' => $request->warehouse_id,
            'brand' => $request->brand,
            'condition' => $request->condition,
            'condition_note' => $request->condition_note,
            'key_features' => $request->key_features,
            'description' => $request->description,
            'purchase_price' => $request->purchase_price,
            'available_form' => $request->available_form,
            'offer_price' => $request->offer_price,
            'offer_start' => $request->offer_start,
            'offer_end' => $request->offer_end,
            'shipping_weight' => $request->shipping_weight,
            'free_shipping' => $request->free_shipping,
            'available_from' => $request->available_from,
            'expiry_date' => $request->expiry_date,
            'min_order_quantity' => $request->min_order_quantity,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'download_limit' => $request->download_limit,
            'supplier_id' => $request->supplier_id,
        ];

        $variant_skus = $request->get('variant_skus');
        $variant_quantities = $request->get('variant_quantities');
        $variant_prices = $request->get('variant_prices');
        $variant_images = $request->file('variant_images');

        $oldVariants = Inventory::where('parent_id', $id)->get();

        if (isset($oldVariants)) {
            foreach ($oldVariants as $oldVariant) {
                if (!in_array($oldVariant->sku, $variant_skus)) {
                    $oldVariant->delete();
                }
            }
        }

        if (isset($variant_skus)) {
            foreach ($variant_skus as $key => $variant_sku) {
                $dynamicInfo = [
                    'sku' => $variant_sku,
                    'stock_quantity' => $variant_quantities[$key],
                    'sale_price' => $variant_prices[$key],
                ];

                // Merge the common info and dynamic info to data array
                $data = array_merge($dynamicInfo, $commonInfo);

                // Insert the record
                $inventory = Inventory::find($key);
                $inventory->update($data);

                // Save Images
                if (isset($variant_images[$key])) {
                    $inventory->saveImage($variant_images[$key]);
                }
            }
        }

        $request->session()->flash('success', trans('messages.updated', ['model' => $this->model]));

        return response()->json($this->getJsonParams($inventory));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateQtt(Request $request, $id)
    {
        $inventory = $this->inventory->updateQtt($request, $id);

        return response('success', 200);
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function trash(Request $request, $id)
    {
        $childInventoryIds = Inventory::where('parent_id', $id)->pluck('id')->toArray();

        if (isset($childInventoryIds)) {
            foreach ($childInventoryIds as $inventoryId) {
                $this->inventory->trash($inventoryId);
            }
        }

        $this->inventory->trash($id);

        return back()->with('success', trans('messages.trashed', ['model' => $this->model]));
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
        $childInventoryIds = Inventory::where('parent_id', $id)->withTrashed()->pluck('id')->toArray();

        if (isset($childInventoryIds)) {
            foreach ($childInventoryIds as $inventoryId) {
                $this->inventory->restore($inventoryId);
            }
        }

        $this->inventory->restore($id);

        return back()->with('success', trans('messages.restored', ['model' => $this->model]));
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
        $childInventoryIds = Inventory::where('parent_id', $id)->withTrashed()->pluck('id')->toArray();

        if (isset($childInventoryIds)) {
            foreach ($childInventoryIds as $inventoryId) {
                $this->inventory->destroy($inventoryId);
            }
        }

        $this->inventory->destroy($id);

        return back()->with('success', trans('messages.deleted', ['model' => $this->model]));
    }

    /**
     * Trash the mass resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function massTrash(Request $request)
    {
        $parentIds = $request->ids;

        foreach ($parentIds as $parentId) {
            $childInventoryIds = Inventory::where('parent_id', $parentId)->pluck('id')->toArray();
            foreach ($childInventoryIds as $inventoryId) {
                array_push($parentIds, $inventoryId);
            }
        }

        $this->inventory->massTrash($parentIds);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.trashed', ['model' => $this->model])]);
        }

        return back()->with('success', trans('messages.trashed', ['model' => $this->model]));
    }

    /**
     * Trash the mass resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function massDestroy(Request $request)
    {
        $parentIds = $request->ids;

        foreach ($parentIds as $parentId) {
            $childInventoryIds = Inventory::where('parent_id', $parentId)->pluck('id')->toArray();
            foreach ($childInventoryIds as $inventoryId) {
                array_push($parentIds, $inventoryId);
            }
        }

        $this->inventory->massDestroy($parentIds);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model]));
    }

    /**
     * Empty the Trash the mass resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function emptyTrash(Request $request)
    {
        $this->inventory->emptyTrash($request);

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model]));
    }

    /**
     * Add single variant
     *
     * @param \Illuminate\Http\Request $request
     * @param Inventory $inventory
     * @return \Illuminate\View\View
     */
    public function singleVariantForm(Request $request, Inventory $inventory)
    {
        $product = $inventory->product;

        $attributes = ListHelper::getAttributesBy($product);

        $productAttributeIds = $attributes->pluck('id');

        return view('admin.inventory.add_variant', compact('product', 'inventory', 'attributes', 'productAttributeIds'));
    }

    // public function saveSingleVariant(CreateProductVariantRequest $request, Product $product)
    public function saveSingleVariant(Request $request, Inventory $inventory)
    {
        $request->validate([
            'sku' => 'bail|required|composite_unique:inventories,sku,shop_id:' .  auth()->user()->merchantId()
        ]);

        $attributes = $request->get('attributes');

        // Create the variant
        $product = $inventory->product;

        $data = [
            'title' => $request->get('title'),
            'product_id' => $product->id,
            'condition' => $inventory->condition,
            'sku' => $request->get('sku'),
            'parent_id' => $inventory->id,
            'shop_id' => $inventory->shop_id,
            'warehouse_id' => $inventory->warehouse_id,
            'brand' => $inventory->brand,
            'supplier_id' => $inventory->supplier_id,
            'condition_note' => $inventory->condition_note,
            'stock_quantity' => $request->get('stock_quantity'),
            'shipping_weight' => $inventory->shipping_weight,
            'free_shipping' => $inventory->free_shipping,
            'available_from' => $inventory->available_from->format('Y-m-d h:i a'),
            'slug' => $inventory->slug . '-' . $request->get('sku'),
            'min_order_quantity' => $inventory->min_order_quantity,
            'user_id' => $request->user()->id,
            'sale_price' => $request->get('sale_price'),
        ];

        $variant = Inventory::create($data);

        $this->setAttributes($variant, $attributes);

        if ($request->hasFile('image')) {
            $variant->saveImage($request->file('image')); // Save image
            // Link the variant to this image
            // $variant->image_id = $image->id;
            $variant->save();
        }

        $request->session()->flash('success', trans('messages.created', ['model' => trans('app.variant')]));

        return redirect()->route('admin.stock.inventory.edit', $inventory);
    }

    /**
     * Set attribute pivot table for the product variants like color, size and more
     * @param Inventory $inventory
     * @param array $attributes
     */
    public function setAttributes($inventory, $attributes)
    {
        $attributes = array_filter($attributes ?? []);        // remove empty elements

        $temp = [];
        foreach ($attributes as $attribute_id => $attribute_value_id) {
            $temp[$attribute_id] = ['attribute_value_id' => $attribute_value_id];
        }

        if (!empty($temp)) {
            $inventory->attributes()->sync($temp);
        }

        return true;
    }

    /**
     * return json params to process the form
     *
     * @param  Product $product
     *
     * @return array
     */
    private function getJsonParams($inventory)
    {
        if (Auth::user()->isFromPlatform()) {
            $route = route('admin.inspector.inspectables');
        } elseif ($inventory->product->downloadable) {
            $route = route('admin.stock.inventory.index', 'digital');
        } elseif ($inventory->auctionable) {
            $route = route('admin.stock.inventory.index', 'auction');
        } else {
            $route = route('admin.stock.inventory.index', 'physical');
        }

        return [
            'id' => $inventory->id,
            'model' => 'inventory',
            'redirect' => $route,
        ];
    }

    /**
     * Prepare wholesale data.
     *
     * This function takes an array of wholesale data and prepares it for processing.
     * It creates a collection of wholesale prices and minimum quantities, based on the input array.
     *
     * @param array $wholesale The array of wholesale data.
     * @return \Illuminate\Support\Collection The prepared wholesale data.
     */
    private function prepareWholeSaleData($wholesale)
    {
        return collect($wholesale['min_quantity'])
            ->map(function ($minQuantity, $index) use ($wholesale) {
                return [
                    'wholesale_price' => $wholesale['wholesale_price'][$index],
                    'min_quantity' => $minQuantity
                ];
            });
    }

    /**
     * Make string json compatible by removing any unescaped double quotes in the passed json string.
     * As the string contains font-family property, where the font-family value may contain double quotes.
     * Without escaping the double quotes, the json string will be invalid due to encoding issues.
     * @param $string
     * @return string
     */
    private function makeStringJsonCompatible($string)
    {
        $regex = "/font-family: (.*?);/"; // regex to get only font-family property value

        // Replace font-family property value with escaped double quotes
        $filtered_string = preg_replace_callback($regex, function ($matches) {
            $font_family = $matches[1];
            $font_family_escaped = str_replace('"', '"', $font_family); // Escape unescaped quotation marks

            return "font-family: $font_family_escaped;";
        }, $string);

        return $filtered_string;
    }
}
