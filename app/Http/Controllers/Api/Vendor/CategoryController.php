<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Models\Category;
use App\Common\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use phpDocumentor\Reflection\Types\Parent_;
use App\Http\Resources\CategoryLightResource;
use App\Http\Resources\CategoryDetailResource;
use App\Repositories\Category\CategoryRepository;
use App\Http\Requests\Validations\CreateCategoryRequest;
use App\Http\Requests\Validations\UpdateCategoryRequest;

class CategoryController extends Controller
{
    /**
     *  Authorizable must be implement
     */
    //    use Authorizable;

    private $category;

    /**
     * construct
     */
    public function __construct(CategoryRepository $category)
    {
        Parent::__construct();
        $this->category = $category;
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
            $categories = $this->category->trashOnly();
        } else {
            $categories = Category::with(['coverImage', 'featureImage'])
                ->orderBy('id', 'asc');
        }

        if ($request->has('sub_group_id')) {
            $categories = $categories->where('category_sub_group_id', $request->get('sub_group_id'));
        }


        $categories = $categories->paginate();

        return CategoryLightResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCategoryRequest $request)
    {
        try {
            $category = $this->category->store($request);

            DB::transaction(function () use ($category, $request) {
                $category->attrsList()->sync($request->get('attribute_ids'));
            });
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.category_created_successfully')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return new CategoryDetailResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $this->category->update($request, $id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.category_updated_successfully')]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, $id)
    {
        try {
            $this->category->trash($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.category_trashed_successfully')], 200);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        try {
            $this->category->restore($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.category_restored_successfully')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->category->destroy($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.category_deleted_successfully')], 200);
    }

    /**
     * Display the translation of a category in the specified language.
     *
     * @param Category $category The category to retrieve the translation for.
     * @param string $language The language code of the translation.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the translation details.
     */
    public function showTranslation(Category $category, string $language)
    {
        $category_translation = $category->translations()->where('lang', $language)->firstOrNew([
            'category_id' => $category->id,
            'lang' => $language
        ]);

        $translation = $category_translation->translation;

        return response()->json([
            'name' => $translation['name'] ?? null,
            'description' => $translation['description'] ?? null,
            'lang' => $language
        ]);
    }

    /**
     * Store the translation for a category in the specified language.
     *
     * @param Category $category The category to store the translation for.
     * @param string $language The language code for the translation.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of the translation storage.
     */
    public function storeTranslation(Category $category, string $language)
    {
        $category_translation = $category->translations()->where('lang', $language)->firstOrNew([
            'category_id' => $category->id,
            'lang' => $language
        ]);

        $category_translation->translation = [
            'name' => request('name'),
            'description' => request('description')
        ];

        $category_translation->save();

        return response()->json(['message' => trans('api.model_translation_saved_successfully',['model' => 'Category'])]);
    }
}
