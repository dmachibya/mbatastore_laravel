<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Attribute;
use App\Models\Inventory;
use App\Helpers\ListHelper;
use Illuminate\Support\Str;
use App\Common\Authorizable;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Inventory\InventoryRepository;
use App\Http\Requests\Validations\CreateProductRequest;
use App\Http\Requests\Validations\UpdateProductRequest;

class ProductController extends Controller
{
    use Authorizable;

    private $model;

    private $inventory;

    /**
     * construct
     */
    public function __construct(InventoryRepository $inventory)
    {
        parent::__construct();

        $this->model = trans('app.model.product');

        $this->inventory = $inventory;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trashes = $this->inventory->trashOnly();

        return view('admin.product.inventory.index', compact('trashes'));
    }

    /**
     * Get the list of products as a Data table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(Request $request)
    {
        $products = Product::with('categories', 'shop.logo', 'featureImage', 'image')
            ->withCount('inventories')->latest();

        // When accessing by a merchant user
        if (Auth::user()->isFromMerchant()) {
            $products->mine();
        }

        return Datatables::of($products)
            ->editColumn('checkbox', function ($product) {
                return view('admin.partials.actions.product.checkbox', compact('product'));
            })
            ->addColumn('option', function ($product) {
                return view('admin.partials.actions.product.options', compact('product'));
            })
            ->editColumn('image', function ($product) {
                return view('admin.partials.actions.product.image', compact('product'));
            })
            ->editColumn('name', function ($product) {
                return view('admin.partials.actions.product.name', compact('product'));
            })
            ->editColumn('type', function ($product) {
                return $product->type;
            })
            ->editColumn('quantity', function ($product) {
                return $product->type;
            })
            ->editColumn('gtin', function ($product) {
                return view('admin.partials.actions.product.gtin', compact('product'));
            })
            ->editColumn('category', function ($product) {
                return view('admin.partials.actions.product.category', compact('product'));
            })
            ->editColumn('inventories_count', function ($product) {
                return view('admin.partials.actions.product.inventories_count', compact('product'));
            })
            ->editColumn('added_by', function ($product) {
                return view('admin.partials.actions.product.added_by', compact('product'));
            })
            ->rawColumns(['image', 'name', 'type', 'gtin', 'category', 'inventories_count', 'added_by', 'status', 'checkbox', 'option'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.product.inventory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductRequest $request)
    {
        $this->authorize('create', Product::class); // Check permission

        $storedProduct = Product::create($request->all());

        // Can have multiple images
        if ($request->hasFile('images')) {
            foreach ($request->images as $type => $file) {
                $storedProduct->saveImage($file, $type);
            }
        }

        // When got a single image
        if ($request->hasFile('image')) {
            $storedProduct->saveImage($request->image);
        }

        if ($request->has('category_list')) {
            $storedProduct->categories()->sync($request->input('category_list'));
        }

        if ($request->has('tag_list')) {
            $storedProduct->syncTags($storedProduct, $request->input('tag_list'));
        }

        $inventoryData = [
            'title' => $request->name,
            'warehouse_id' => $request->warehouse_id,
            'brand' => $request->brand,
            'sku' => $request->sku,
            'condition' => $request->condition,
            'condition_note' => $request->condition_note,
            'key_features' => $request->key_features,
            'description' => $request->description,
            'stock_quantity' => $request->stock_quantity,
            'purchase_price' => $request->purchase_price,
            'sale_price' => $request->sale_price,
            'available_form' => $request->available_form,
            'offer_price' => $request->offer_price,
            'offer_start' => $request->offer_start,
            'offer_end' => $request->offer_end,
            'shipping_weight' => $request->shipping_weight,
            'free_shipping' => $request->free_shipping,
            'available_from' => $request->available_from,
            'expiry_date' => $request->expiry_date,
            'min_order_quantity' => $request->min_order_quantity,
            'linked_items' => $request->linked_items,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'active' => $request->active,
            'slug' => $request->slug,
            'download_limit' => $request->download_limit,
            'supplier_id' => $request->supplier_id,
            'user_id' => $request->user_id,
            'shop_id' => $request->shop_id,
            'product_id' => $storedProduct->id,
        ];

        $inventoryRequest = new Request($inventoryData);

        $product = $this->inventory->store($inventoryRequest);

        if ($request->filled('variants') && $request->filled('skus')) {
            // Common information
            $commonInfo = [
                'parent_id' => $product->id,
                'user_id' => $request->user_id,
                'shop_id' => $request->shop_id,
                'title' => $request->name,
                'product_id' => $storedProduct->id,
                'brand' => $product->brand,
                'condition' => $request->condition,
                'condition_note' => $request->condition_note,
                'warehouse_id' => $request->input('warehouse_id'),
                'supplier_id' => $request->input('supplier_id'),
                'purchase_price' => $request->input('purchase_price'),
                'offer_price' => $request->input('offer_price'),
                'offer_start' => $request->offer_start,
                'offer_end' => $request->offer_end,
                'shipping_weight' => $request->input('shipping_weight'),
                'available_from' => $request->input('available_from'),
                'active' => $request->input('active'),
                'tax_id' => $request->input('tax_id'),
                'min_order_quantity' => $request->input('min_order_quantity'),
                'alert_quantity' => $request->input('alert_quantity'),
                'description' => $request->input('description'),
                'key_features' => $request->input('key_features'),
                'linked_items' => $request->input('linked_items'),
                'meta_title' => $request->input('meta_title'),
                'meta_description' => $request->input('meta_description'),
            ];

            // Arrays
            $skus = $request->input('skus');

            $stock_quantities = $request->input('stock_quantities');

            $sale_prices = $request->input('sale_prices');

            $images = $request->file('variant_images');

            // Relations
            if (is_incevio_package_loaded('packaging')) {
                $packaging_lists = $request->input('packaging_list');
            }

            $tag_lists = $request->input('tag_list');

            $variants = $request->input('variants');

            // Preparing data and insert records.
            $dynamicInfo = [];
            foreach ($skus as $key => $sku) {
                $dynamicInfo = [
                    'sku' => $skus[$key],
                    'stock_quantity' => $stock_quantities[$key],
                    'sale_price' => $sale_prices[$key],
                    'slug' => Str::slug($request->input('slug') . ' ' . $sku, '-'),
                ];

                // Merge the common info and dynamic info to data array
                $data = array_merge($dynamicInfo, $commonInfo);

                // Insert the record
                $inventory = Inventory::create($data);

                // Sync Attributes
                if ($variants[$key]) {
                    $this->setAttributes($inventory, $variants[$key]);
                }

                // Sync packaging
                if (is_incevio_package_loaded('packaging') && $packaging_lists) {
                    $inventory->packagings()->sync($packaging_lists);
                }

                // Sync tags
                if ($tag_lists) {
                    $inventory->syncTags($inventory, $tag_lists);
                }

                // Save Images
                if (isset($images[$key])) {
                    $inventory->saveImage($images[$key]);
                }
            }
        }

        $request->session()->flash('success', trans('messages.created', ['model' => $this->model]));

        return response()->json($this->getJsonParams($product));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with('inventories.shop')->find($id);

        $this->authorize('view', $product); // Check permission

        return view('admin.product.inventory._show', compact('product'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);

        $this->authorize('update', $product); // Check permission

        $attributes = collect([]);

        $product->load('inventories');

        $inventory = $product->inventories->whereNull('parent_id')->first();

        if (is_incevio_package_loaded('wholesale')) {
            $inventory->wholesale = get_wholesale_item_prices($inventory->id);
        }

        $preview = $inventory->previewImages();

        return view('admin.product.inventory.edit', compact('inventory', 'product', 'preview'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::find($id);

        if ($request->hasFile('digital_file')) {
            $product->flushAttachments();
            $product->saveAttachments($request->file('digital_file'));
        }

        $this->authorize('update', $product); // Check permission

        $product->update($request->all());

        if ($request->input('delete_image')) {
            if (is_array($request->delete_image)) {
                foreach ($request->delete_image as $type => $value) {
                    $product->deleteImageTypeOf($type);
                }
            } else {
                $product->deleteImage();
            }
        }

        // Can have multiple images
        if ($request->hasFile('images')) {
            foreach ($request->images as $type => $file) {
                $product->updateImage($file, $type);
            }
        }

        // When got a single image
        if ($request->hasFile('image')) {
            $product->updateImage($request->image);
        }

        $this->authorize('update', $product); // Check permission

        $inventoryId = Inventory::where('product_id', $id)->whereNull('parent_id')->pluck('id')->first();

        $product = $this->inventory->update($request, $inventoryId);

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

        $oldVariants = Inventory::where('parent_id', $inventoryId)->get();

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

        return response()->json($this->getJsonParams($product));
    }

    /**
     * Add single variant
     *
     * @param \Illuminate\Http\Request $request
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function singleVariantForm(Request $request, Product $product)
    {
        $inventory = $product->inventories->whereNull('parent_id')->first();

        $attributes = ListHelper::getAttributesBy($product);

        $productAttributeIds = $attributes->pluck('id');

        return view('admin.product.inventory.add_variant', compact('product', 'inventory', 'attributes', 'productAttributeIds'));
    }

    public function saveSingleVariant(Request $request, Product $product)
    {
        $request->validate([
            'sku' => 'bail|required|composite_unique:inventories,sku,shop_id:' .  auth()->user()->merchantId()
        ]);

        $attributes = $request->get('attributes');

        // Create the variant
        $parent = $product->inventories->whereNull('parent_id')->first();

        $data = [
            'title' => $request->get('title'),
            'condition' => $parent->condition,
            'sku' => $request->get('sku'),
            'parent_id' => $parent->id,
            'shop_id' => $parent->shop_id,
            'warehouse_id' => $parent->warehouse_id,
            'brand' => $parent->brand,
            'supplier_id' => $parent->supplier_id,
            'condition_note' => $parent->condition_note,
            'stock_quantity' => $request->get('stock_quantity'),
            'shipping_weight' => $parent->shipping_weight,
            'free_shipping' => $parent->free_shipping,
            'available_from' => $parent->available_from->format('Y-m-d h:i a'),
            'slug' => $parent->slug . '-' . $request->get('sku'),
            'min_order_quantity' => $parent->min_order_quantity,
            'user_id' => $request->user()->id,
            'sale_price' => $request->get('sale_price'),
        ];

        $variant = $product->inventories()->create($data);

        $this->setAttributes($variant, $attributes);

        if ($request->hasFile('image')) {
            $image = $product->saveImage($request->file('image')); // Save image
            // Link the variant to this image
            // $variant->image_id = $image->id;
            $variant->save();
        }

        $request->session()->flash('success', trans('messages.created', ['model' => trans('app.variant')]));

        return redirect()->route('admin.stock.product.edit', $product);
    }


    /**
     * get attributes by categories
     */
    public function getAttributesByCategories(Request $request)
    {
        $categoryIds = $request->input('category_ids');

        if (isset($categoryIds)) {
            $attributes = Attribute::whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })->get();
        } else {
            $attributes = collect();
        }

        return view('admin.product.inventory._attribute_dropdown', ['attributes' => $attributes])->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCombinations(Request $request)
    {
        $variants = $this->confirmAttributes($request->except('_token', '_'));

        $combinations = generate_combinations($variants);

        return view('admin.product.inventory._combinations', compact('combinations'));
    }

    /**
     * Check the list of attribute values and add new if need
     *
     * @param  [type] $attribute
     * @return array
     */
    public function confirmAttributes($attributeWithValues)
    {
        $results = array();

        foreach ($attributeWithValues as $attribute => $values) {
            foreach ($values as $value) {
                $oldValueId = AttributeValue::find($value);

                $oldValueName = AttributeValue::where('value', $value)->where('attribute_id', $attribute)->first();

                if ($oldValueId || $oldValueName) {
                    $results[$attribute][($oldValueId) ? $oldValueId->id : $oldValueName->id] = ($oldValueId) ? $oldValueId->value : $oldValueName->value;
                } else {
                    // if the value not numeric thats meaning that its new value and we need to create it
                    $newID = AttributeValue::insertGetId(['attribute_id' => $attribute, 'value' => $value]);

                    $newAttrValue = AttributeValue::find($newID);

                    $results[$attribute][$newAttrValue->id] = $newAttrValue->value;
                }
            }
        }

        return $results;
    }

    /**
     * Set attribute pivot table for the product variants like color, size and more
     * @param obj $inventory
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
     * return json params to proceed the form
     *
     * @param Product $product
     *
     * @return array
     */
    private function getJsonParams($product)
    {
        return [
            'id' => $product->id,
            'model' => 'inventory',
            'redirect' => route('admin.stock.product.index'),
        ];
    }

    /**
     * Verify variant uniqueness
     *
     * @param  Product $product
     * @param  array $attributes
     *
     * @return bool
     */
    public function verifyVariantUniqueness(Product $product, $attributes = [])
    {
        foreach ($product->inventories->pluck('attributes') as $value) {
            $tempAttrs = $value->pluck('pivot.attribute_value_id', 'id')->toArray();

            if ($tempAttrs == $attributes) {
                return FALSE;
            }
        }

        return TRUE;
    }
}
