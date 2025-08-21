<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Validations\CreateCategorySubGroupRequest;
use App\Http\Requests\Validations\UpdateCategorySubGroupRequest;
use App\Repositories\CategorySubGroup\CategorySubGroupRepository;

class CategorySubGroupController extends Controller
{
    use Authorizable;

    private $model_name;

    private $categorySubGroup;

    /**
     * construct
     */
    public function __construct(CategorySubGroupRepository $categorySubGroup)
    {
        parent::__construct();
        $this->model_name = trans('app.model.category_group');
        $this->categorySubGroup = $categorySubGroup;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categorySubGrps = $this->categorySubGroup->all();

        $trashes = $this->categorySubGroup->trashOnly();

        return view('admin.category.categorySubGroup', compact('categorySubGrps', 'trashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.category._createSubGrp');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCategorySubGroupRequest $request)
    {
        $subGroup = $this->categorySubGroup->store($request);

        Cache::forget('all_categories');

        // Put the grp id for convenience
        $request->session()->put('convenient_group_id', $subGroup->category_group_id);

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $categorySubGroup
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categorySubGroup = $this->categorySubGroup->find($id);

        return view('admin.category._editSubGrp', compact('categorySubGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategorySubGroupRequest $request, $id)
    {
        $this->categorySubGroup->update($request, $id);

        Cache::forget('all_categories');

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, $id)
    {
        $this->categorySubGroup->trash($id);

        Cache::forget('all_categories');

        return back()->with('success', trans('messages.trashed', ['model' => $this->model_name]));
    }

    /**
     * Restore the specified resource from soft delete.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, $id)
    {
        $this->categorySubGroup->restore($id);

        Cache::forget('all_categories');

        return back()->with('success', trans('messages.restored', ['model' => $this->model_name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->categorySubGroup->destroy($id);

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }

    /**
     * Trash the mass resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massTrash(Request $request)
    {
        $this->categorySubGroup->massTrash($request->ids);

        Cache::forget('all_categories');

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.trashed', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.trashed', ['model' => $this->model_name]));
    }

    /**
     * Trash the mass resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {
        $this->categorySubGroup->massDestroy($request->ids);

        Cache::forget('all_categories');

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }

    /**
     * Empty the Trash the mass resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function emptyTrash(Request $request)
    {
        $this->categorySubGroup->emptyTrash($request);

        Cache::forget('all_categories');

        if ($request->ajax()) {
            return response()->json(['success' => trans('messages.deleted', ['model' => $this->model_name])]);
        }

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }

    public function showParentCategories($id)
    {
        $categorySubGroup = $this->categorySubGroup->find($id);
        $trashes = $categorySubGroup->categories()->onlyTrashed()->get();
        $categories = $categorySubGroup->categories()->get();

        return view('admin.category.subGroup_parentCategories', compact('categories','categorySubGroup','trashes'));
    }
}
