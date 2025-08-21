<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Models\CategoryGroup;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryGroupLightResource;
use App\Http\Resources\CategoryGroupResource;
use App\Http\Requests\Validations\CreateCategoryGroupRequest;
use App\Http\Requests\Validations\UpdateCategoryGroupRequest;
use App\Repositories\CategoryGroup\CategoryGroupRepository;
use Illuminate\Http\Request;

class CategoryGroupController extends Controller
{
    private $categoryGroup;

    /**
     * construct
     */
    public function __construct(CategoryGroupRepository $category_groups)
    {
        parent::__construct();

        $this->categoryGroup = $category_groups;
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
            $categoryGroups = $this->categoryGroup->trashOnly();
        } else {
            $categoryGroups = CategoryGroup::withCount('subGroups')->get();
        }

        return CategoryGroupLightResource::collection($categoryGroups);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCategoryGroupRequest $request)
    {
        // Need to do CreateCategoryGroupRequest validation
        // Need to check role permission

        try {
            $this->categoryGroup->store($request);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.category_group_created_successfully')], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category_group = CategoryGroup::withCount('subGroups')->find($id);

        return new CategoryGroupResource($category_group);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Need updateRequest Validation
        // Need permission check

        try {
            $this->categoryGroup->update($request, $id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.category_group_updated_successfully')], 200);
    }

    /**
     * category group item move trash
     */
    public function trash($id)
    {
        try {
            $this->categoryGroup->trash($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.category_group_trashed_successfully')], 200);
    }

    /**
     * category group item restore
     */
    public function restore($id)
    {
        try {
            $this->categoryGroup->restore($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.category_group_restore_successfully')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Need to check permission role
        // Need to do request validation

        try {
            $this->categoryGroup->destroy($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.category_group_deleted_successfully')], 200);
    }

    /**
     * Display the translation of a category group.
     *
     * @param CategoryGroup $category_group The category group instance.
     * @param string $language The language code for the translation.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the translation details.
     */
    public function showTranslation(CategoryGroup $category_group, string $language)
    {
        $category_group_translation = $category_group->translations()->where('lang', )->first();
        $translation = $category_group_translation ? $category_group_translation->translation : [];

        return response()->json([
            'name' => $translation['name'] ?? null,
            'description' => $translation['description'] ?? null,
            'lang' => $language,
        ]);
    }

    /**
     * Store the translation for a category group.
     *
     * @param CategoryGroup $category_group The category group to store the translation for.
     * @param string $language The language of the translation.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of the translation storage.
     */
    public function storeTranslation(CategoryGroup $category_group, string $language)
    {
        $category_group_translation = $category_group->translations()->where('lang',$language)->firstOrNew([
            'category_group_id' => $category_group->id,
            'lang' => $language,
        ]);

        $category_group_translation->translation = [
            'name' => request('name'),
            'description' => request('description'),
        ];

        $category_group_translation->save();

        return response()->json(['message' => trans('api.model_translation_saved_successfully',['model' => 'CategoryGroup'])]);
    }
}
