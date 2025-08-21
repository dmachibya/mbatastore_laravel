<?php

namespace App\Repositories\Warehouse;

use App\Models\Warehouse;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EloquentWarehouse extends EloquentRepository implements BaseRepository, WarehouseRepository
{
    protected $model;

    public function __construct(Warehouse $warehouse)
    {
        $this->model = $warehouse;
    }

    public function all()
    {
        $query = $this->model->with('manager', 'image', 'primaryAddress', 'logoImage');

        if (!Auth::user()->isFromPlatform()) {
            return $query->mine()->get();
        }

        return $query->get();
    }

    public function trashOnly()
    {
        if (!Auth::user()->isFromPlatform()) {
            return $this->model->mine()->with('logoImage', 'manager')->onlyTrashed()->get();
        }

        return $this->model->with('logoImage', 'manager')->onlyTrashed()->get();
    }

    public function store(Request $request)
    {
        $warehouse = parent::store($request);

        $this->saveAddress($request->all(), $warehouse);

        return $warehouse;
    }

    public function destroy($id)
    {
        $warehouse = parent::findTrash($id);

        $warehouse->flushAddresses();

        $warehouse->flushImages();

        return $warehouse->forceDelete();
    }

    public function massDestroy($ids)
    {
        $warehouses = $this->model->withTrashed()->whereIn('id', $ids)->get();

        foreach ($warehouses as $warehouse) {
            $warehouse->flushAddresses();
            $warehouse->flushImages();
        }

        return parent::massDestroy($ids);
    }

    public function emptyTrash()
    {
        $warehouses = $this->model->onlyTrashed()->get();

        foreach ($warehouses as $warehouse) {
            $warehouse->flushAddresses();
            $warehouse->flushImages();
        }

        return parent::emptyTrash();
    }

    public function saveAddress(array $address, $warehouse)
    {
        $warehouse->addresses()->create($address);
    }
}
