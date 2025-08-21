<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Requests\Validations\CreateInventoryRequest;
use App\Http\Requests\Validations\CreateInventoryWithVariantRequest;
use App\Http\Resources\ProductLightResource;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\InventoryResource;
use App\Http\Resources\InventoryLightResource;
use App\Repositories\Inventory\InventoryRepository;
use App\Http\Requests\Validations\UpdateInventoryRequest;
use App\Http\Requests\Validations\QuickInventoryUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class InventoryController extends Controller
{
    /**
     * construct
     */
    public function __construct(InventoryRepository $inventory)
    {
        parent::__construct();

        $this->inventory = $inventory;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check permission

        $filter = $request->get('filter');

        $inventories = Inventory::mine();

        // When the inventories need to filter
        switch ($filter) {
            case 'active':
            case 'actives':
                $inventories = $inventories->active();
                break;

            case 'inactive':
            case 'inactives':
                $inventories = $inventories->inActive();
                break;

            case 'new_arraival':
            case 'new_arraivals':
                $inventories = $inventories->newArraivals();
                break;

            case 'low_quantity':
            case 'low_quantities':
                $inventories = $inventories->lowQtt();
                break;

            case 'out_of_stock':
            case 'out_of_stocks':
                $inventories = $inventories->stockOut();
                break;

            case 'has_offer':
            case 'has_offers':
                $inventories = $inventories->hasOffer();
                break;

            case 'free_shipping':
            case 'free_shippings':
                $inventories = $inventories->freeShipping();
                break;

            case 'trash':
            case 'trashes':
            case 'trashed':
                $inventories = $inventories->onlyTrashed();
                break;
        }

        $inventories = $inventories->with('image:path,imageable_id,imageable_type')
            ->paginate(config('mobile_app.view_listing_per_page', 8));

        return InventoryLightResource::collection($inventories);
    }

    /**
     * Add a product to inventory.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateInventoryRequest $request)
    {
        try {
            $this->inventory->store($request);
        } catch (\Excention $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.inventory_created_successfully')], 200);
    }

    /**
     * Add inventory with variants.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeWithVariant(CreateInventoryWithVariantRequest $request)
    {
        try {
            $this->inventory->storeWithVariant($request);
        } catch (\Excention $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.inventory_created_successfully')], 200);
    }

    /**
     * Display a listing of the resource.
     * @param Inventory $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Inventory $inventory)
    {
        //  $this->authorize('view', $inventory); // Check permission

        return new InventoryResource($inventory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Inventory $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        //$this->authorize('update', $inventory); // Check permission

        try {
            $this->inventory->update($request, $inventory);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.data_updated_successfully')], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Inventory $inventory
     * @return \Illuminate\Http\Response
     */
    public function quick_update(QuickInventoryUpdateRequest $request, Inventory $inventory)
    {
        // $this->authorize('update', $inventory); // Check permission

        try {
            $data = !is_incevio_package_loaded('pharmacy') ? $request->except('expiry_date') : $request->all();

            $inventory->update($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.item_updated_successfully')], 200);
    }

    /**
     * Trash the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param Inventory $inventory
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, Inventory $inventory)
    {
        // $this->authorize('delete', $inventory); // Check permission

        try {
            $inventory->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.data_trashed_successfully')], 200);
    }

    /**
     * Restore the specified resource from soft delete.
     *
     * @param \Illuminate\Http\Request $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, $id)
    {
        // Check permission

        try {
            $this->inventory->restore($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.data_restored_successfully')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id)
    {
        // Check permission

        try {
            $this->inventory->destroy($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.data_deleted_successfully')], 200);
    }

    /**
     * Display the translation of an inventory item.
     *
     * @param Inventory $inventory The inventory item.
     * @param string $language The language code for the translation.
     * @return \Illuminate\Http\Response The JSON response containing the translation.
     */
    public function showTranslation(Inventory $inventory, string $language)
    {
        $inventory_translation = $inventory->translations()->where('lang', $language)->firstOrNew([
            'inventory_id' => $inventory->id,
            'lang' => $language,
        ]);

        $translation = $inventory_translation->translation;

        return response([
            'title' => $translation['title'] ?? null,
            'description' => $translation['description'] ?? null,
            'condition_note' => $translation['condition_note'] ?? null,
            'key_features' => $translation['key_features'] ?? null,
            'lang' => $language,
        ]);
    }

    /**
     * Store the translation for an inventory item.
     *
     * @param  Inventory  $inventory  The inventory item to store the translation for.
     * @param  string  $language  The language of the translation.
     * @param  Request  $request  The HTTP request object.
     * @return \Illuminate\Http\JsonResponse  The JSON response indicating the success of the translation storage.
     */
    public function storeTranslation(Inventory $inventory, string $language, Request $request)
    {
        $inventory_translation = $inventory->translations()->where('lang', $request->lang)->firstOrNew([
            'inventory_id' => $inventory->id,
            'lang' => $request->lang,
        ]);

        $inventory_translation->translation = [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'condition_note' => $request->input('condition_note'),
            'key_features' => $request->input('key_features')
        ];

        $inventory_translation->save();

        return response()->json(['message' => trans('api.model_translation_saved_successfully',['model' => 'Inventory'])]);
    }
}
