<?php

namespace App\Http\Controllers;

use App\Http\Requests\Validations\ContactUsRequest;
use App\Jobs\SendContactFromMessageToAdmin;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactUsController extends Controller
{
    private $model;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = trans('app.model.message');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function send(ContactUsRequest $request)
    {
        if (is_incevio_package_loaded('smartForm')) {
            $request = store_files_from_request_for_message($request);
        }

        $message = Message::create($request->all());

        try {
            SendContactFromMessageToAdmin::dispatch($message);
        } catch (\Exception $exception) {
            Log::error('Mail Sending Error');
            Log::info(get_exception_message($exception));
        }

        if ($request->ajax()) {
            return response(trans('messages.sent', ['model' => $this->model]), 200);
        }

        return back()->with('success', trans('messages.sent', ['model' => $this->model]));
    }
}
