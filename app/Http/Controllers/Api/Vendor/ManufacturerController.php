<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Models\Manufacturer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ManufacturerResource;
use App\Http\Resources\ManufacturerLightResource;
use App\Repositories\Manufacturer\ManufacturerRepository;
use App\Http\Requests\Validations\CreateManufacturerRequest;
use App\Http\Requests\Validations\UpdateManufacturerRequest;

class ManufacturerController extends Controller
{
    private $manufacturer;

    /**
     * construct
     */
    public function __construct(ManufacturerRepository $manufacturer)
    {
        parent::__construct();
        $this->manufacturer = $manufacturer;
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
            $manufacturers = $this->manufacturer->trashOnly();
        } else {
            $manufacturers = Manufacturer::mine()->with('logoImage')->paginate();
        }

        return ManufacturerLightResource::collection($manufacturers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateManufacturerRequest $request)
    {
        try {
            $this->manufacturer->store($request);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.manufacturer_created_successfully')], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  Manufacturer $manufacturer
     * @return \Illuminate\Http\Response
     */
    public function show(Manufacturer $manufacturer)
    {
        return new ManufacturerResource($manufacturer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  id $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateManufacturerRequest $request, $id)
    {
        try {
            $this->manufacturer->update($request, $id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.manufacturer_updated_successfully')], 200);
    }

    /**
     * Soft delete model to trash
     *
     * @param  Manufacturer $manufacturer
     * @return \Illuminate\Http\Response
     */
    public function trash(Manufacturer $manufacturer)
    {
        try {
            $manufacturer->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.manufacturer_trashed_successfully')], 200);
    }

    /**
     * restore manufacturer
     *
     * @param  id $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        try {
            $this->manufacturer->restore($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.manufacturer_restored_successfully')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  id $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->manufacturer->destroy($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => trans('api.manufacturer_deleted_successfully')], 200);
    }

    /**
     * Display the translation of a manufacturer in the specified language.
     *
     * @param Manufacturer $manufacturer The manufacturer instance.
     * @param string $language The language code for the translation.
     * @return \Illuminate\Http\Response The response containing the translation details.
     */
    public function showTranslation(Manufacturer $manufacturer, string $language)
    {
        $manufacturer_translation = $manufacturer->translations()->where('lang', $language)->first();
        $translation = $manufacturer_translation ? $manufacturer_translation->translation : [];

        return response([
            'name' => $translation['name'] ?? null,
            'description' => $translation['description'] ?? null,
            'lang' => $language,
        ]);
    }

    /**
     * Store the translation for a Manufacturer.
     *
     * @param Manufacturer $manufacturer The Manufacturer instance.
     * @param string $language The language code for the translation.
     * @return \Illuminate\Http\JsonResponse The JSON response with a success message.
     */
    public function storeTranslation(Manufacturer $manufacturer, string $language)
    {
        $manufacturer_translation = $manufacturer->translations()->where('lang', $language)->firstOrNew([
            'manufacturer_id' => $manufacturer->id,
            'lang' => $language,
        ]);

        $manufacturer_translation->translation = [
            'name' => request('name'),
            'description' => request('description'),
        ];

        $manufacturer_translation->save();

        return response()->json(['message' => trans('api.model_translation_saved_successfully', ['model' => 'Manufacturer'])]);
    }
}
