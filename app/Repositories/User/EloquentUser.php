<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EloquentUser extends EloquentRepository implements BaseRepository, UserRepository
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    // public function all()
    // {
    //     if (!Auth::user()->isFromPlatform()) {
    //         return $this->model->level()->mine()->with('role', 'avatarImage:path,imageable_id,imageable_type', 'primaryAddress')->get();
    //     }

    //     return $this->model->level()->fromPlatform()->with('role', 'avatarImage:path,imageable_id,imageable_type', 'primaryAddress')->get();
    // }

    // public function trashOnly()
    // {
    //     if (!Auth::user()->isFromPlatform()) {
    //         return $this->model->level()->mine()->onlyTrashed()->with('avatarImage:path,imageable_id,imageable_type')->get();
    //     }

    //     return $this->model->level()->fromPlatform()->onlyTrashed()->with('avatarImage:path,imageable_id,imageable_type')->get();
    // }

    public function addresses($user)
    {
        return $user->addresses()->get();
    }

    public function store(Request $request)
    {
        $temp = $request['phone'];
        unset($request['phone']);
        $user = parent::store($request);

        $request->merge(['phone' => $temp]);

        $this->saveAdrress($request->all(), $user);

        if ($request->hasFile('image')) {
            $user->saveImage($request->file('image'));
        }

        return $user;
    }

    public function update(Request $request, $id)
    {
        $user = parent::update($request, $id);

        if ($request->hasFile('image') || ($request->input('delete_image') == 1)) {
            $user->deleteImage();
        }

        if ($request->hasFile('image')) {
            $user->saveImage($request->file('image'));
        }

        return $user;
    }

    public function destroy($id)
    {
        $user = parent::findTrash($id);

        $user->flushAddresses();

        $user->flushImages();

        return $user->forceDelete();
    }

    public function saveAdrress(array $address, $user)
    {
        $user->addresses()->create($address);
    }

    public function massDestroy($ids)
    {
        $users = $this->model->withTrashed()->whereIn('id', $ids)->get();

        foreach ($users as $user) {
            $user->flushAddresses();
            $user->flushImages();
        }

        return parent::massDestroy($ids);
    }

    public function emptyTrash()
    {
        $users = $this->model->onlyTrashed()->get();

        foreach ($users as $user) {
            $user->flushAddresses();
            $user->flushImages();
        }

        return parent::emptyTrash();
    }
}
