<?php

namespace App\Repositories\Supplier;

use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EloquentSupplier extends EloquentRepository implements BaseRepository, SupplierRepository
{
    protected $model;

    public function __construct(Supplier $supplier)
    {
        $this->model = $supplier;
    }

    public function all()
    {
        if (!Auth::user()->isFromPlatform()) {
            return Supplier::with('logoImage', 'primaryAddress', 'image')->mine()->get();
        }

        return Supplier::with('logoImage', 'primaryAddress', 'image')->get();
    }

    public function trashOnly()
    {
        if (!Auth::user()->isFromPlatform()) {
            return Supplier::with('image')->onlyTrashed()->mine()->get();
        }

        return Supplier::with('image')->onlyTrashed()->get();
    }

    public function store(Request $request)
    {
        $supplier = parent::store($request);

        $this->saveAdrress($request->all(), $supplier);

        return $supplier;
    }

    public function destroy($id)
    {
        $supplier = parent::findTrash($id);

        $supplier->flushAddresses();

        $supplier->flushImages();

        return $supplier->forceDelete();
    }

    public function massDestroy($ids)
    {
        $suppliers = Supplier::withTrashed()->whereIn('id', $ids)->get();

        foreach ($suppliers as $supplier) {
            $supplier->flushAddresses();
            $supplier->flushImages();
        }

        return parent::massDestroy($ids);
    }

    public function emptyTrash()
    {
        $suppliers = Supplier::onlyTrashed()->get();

        foreach ($suppliers as $supplier) {
            $supplier->flushAddresses();
            $supplier->flushImages();
        }

        return parent::emptyTrash();
    }

    public function saveAdrress(array $address, $supplier)
    {
        $supplier->addresses()->create($address);
    }
}
