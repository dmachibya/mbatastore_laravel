<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Models\Image;
use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductLightResource;
use App\Http\Resources\CategoryLightResource;
use App\Http\Requests\Validations\CreateProductRequest;
use App\Http\Requests\Validations\UpdateProductRequest;
use App\Repositories\Product\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $product;

    public function __construct(ProductRepository $product)
    {
        parent::__construct();
        $this->product = $product;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter');

        if ($filter == 'trash') {
            $products = $this->product->trashonly();
        } else {
            $products = Product::mine()->with('featureImage', 'image', 'categories')->paginate();
        }

        return ProductLightResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductRequest $request)
    {
        try {
            $this->product->store($request);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.product_created_successfully')], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new ProductResource($this->product->find($id));
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
        try {
            $this->product->update($request, $id);

            // Delete images for app
            if ($request->input('delete_images')) {
                $models = Image::whereIn('id', $request->input('delete_images'))->get();

                foreach ($models as $model) {
                    $model->delete();
                }
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.product_updated_successfully')], 200);
    }

    /**
     * trash product
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function trash($id)
    {
        try {
            $this->product->trash($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.product_trashed_successfully')], 200);
    }

    /**
     * restore product
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        try {
            $this->product->restore($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.product_restored_successfully')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->product->destroy($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.product_deleted_successfully')], 200);
    }

    /**
     * Display the translation of a product in the specified language.
     *
     * @param  Product  $product  The product instance.
     * @param  string  $language  The language code for the translation.
     * @return \Illuminate\Http\Response
     */
    public function showTranslation(Product $product, string $language)
    {
        $product_translation = $product->translations()->where('lang', $language)->firstOrNew([
            'product_id' => $product->id,
            'lang' => $language,
        ]);

        $translation = $product_translation->translation;

        return response([
            'name' => $translation['name'] ?? null,
            'description' => $translation['description'] ?? null,
            'brand' => $translation['brand'] ?? null,
            'lang' => $language,
        ]);
    }

    /**
     * Store the translation for a product in the specified language.
     *
     * @param Product $product The product for which to store the translation.
     * @param string $language The language in which to store the translation.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of the translation storage.
     */
    public function storeTranslation(Product $product, string $language)
    {
        $product_translation = $product->translations()->where('lang', $language)->firstOrNew([
            'product_id' => $product->id,
            'lang' => $language,
        ]);

        $product_translation->translation = [
            'name' => request('name'),
            'brand' => request('brand'),
            'description' => request('description'),
        ];

        $product_translation->save();

        return response()->json(['message' => trans('api.model_translation_saved_successfully', ['model' => 'Product'])]);
    }
}
