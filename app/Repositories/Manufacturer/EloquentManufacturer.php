<?php

namespace App\Repositories\Manufacturer;

use App\Models\Manufacturer;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use App\Repositories\EloquentRepository;

class EloquentManufacturer extends EloquentRepository implements BaseRepository, ManufacturerRepository
{
    protected $model;

    public function __construct(Manufacturer $manufacturer)
    {
        $this->model = $manufacturer;
    }

    public function all()
    {
        $query = $this->model->with('logoImage', 'country:id,name');
        // ->withCount('products');

        if (Auth::user()->isFromPlatform()) {
            return $query->get();
        }

        return $query->mine()->get();
    }

    public function trashOnly()
    {
        $query = $this->model->onlyTrashed();

        if (Auth::user()->isFromPlatform()) {
            return $query->get();
        }

        return $query->mine()->get();
    }

    public function store(Request $request)
    {
        $manufacturer = parent::store($request);

        return $manufacturer;
    }

    public function update(Request $request, $id)
    {
        $manufacturer = parent::update($request, $id);

        return $manufacturer;
    }

    public function destroy($id)
    {
        $manufacturer = parent::findTrash($id);

        $manufacturer->flushImages();

        return $manufacturer->forceDelete();
    }

    public function massDestroy($ids)
    {
        $manufacturers = $this->model->withTrashed()->whereIn('id', $ids)->get();

        foreach ($manufacturers as $manufacturer) {
            $manufacturer->flushImages();
        }

        return parent::massDestroy($ids);
    }

    public function emptyTrash()
    {
        $manufacturers = $this->model->onlyTrashed()->get();

        foreach ($manufacturers as $manufacturer) {
            $manufacturer->flushImages();
        }

        return parent::emptyTrash();
    }
}
