<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Models\Shop;
use App\Models\System;
use App\Models\Ticket;
use App\Helpers\Authorize;
use App\Events\Profile\PasswordUpdated;
use App\Events\Profile\ProfileUpdated;
use App\Events\Ticket\TicketCreated;
use App\Events\Ticket\TicketReplied;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateTicketRequest;
use App\Http\Requests\Validations\DeletePhotoRequest;
use App\Http\Requests\Validations\ReplyTicketRequest;
use App\Http\Requests\Validations\UpdatePasswordRequest;
use App\Http\Requests\Validations\UpdatePhotoRequest;
use App\Http\Requests\Validations\UpdateProfileRequest;
use App\Notifications\SuperAdmin\TicketCreated as TicketCreatedNotification;
use App\Repositories\Account\AccountRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    private $profile;

    /**
     * construct
     */
    public function __construct(AccountRepository $profile)
    {
        parent::__construct();

        $this->profile = $profile;
    }

    /**
     * Show the profile.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        $profile = $this->profile->profile();

        return view('admin.account.index', compact('profile'));
    }

    /**
     * Display the specified resource.
     *
     * @param int id
     * @return \Illuminate\View\View
     */
    public function showTicket(Ticket $ticket)
    {
        if (!(new Authorize(Auth::user(), 'view_ticket', $ticket))->check()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.account.show_ticket', compact('ticket'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function createTicket()
    {
        return view('admin.account._create_ticket');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTicket(CreateTicketRequest $request)
    {
        $ticket = Ticket::create($request->all());

        if ($request->hasFile('attachments')) {
            $ticket->saveAttachments($request->file('attachments'));
        }

        // Send notification to Admin
        if (config('system_settings.notify_new_ticket')) {
            $system = System::orderBy('id', 'asc')->first();

            $system->superAdmin()->notify(new TicketCreatedNotification($ticket));
        }

        event(new TicketCreated($ticket));

        return redirect()->route('admin.account.ticket')
            ->with('success', trans('messages.created', ['model' => trans('app.model.ticket')]));
    }

    /**
     * Display the reply form.
     *
     * @param int id
     * @return \Illuminate\View\View
     */
    public function replyTicket(Ticket $ticket)
    {
        return view('admin.account._reply_ticket', compact('ticket'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Ticket id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTicketReply(ReplyTicketRequest $request, Ticket $ticket)
    {
        if (!(new Authorize(Auth::user(), 'reply_ticket', $ticket))->check()) {
            abort(403, 'Unauthorized action.');
        }

        $ticket->update($request->except('user_id'));

        $reply = $ticket->replies()->create($request->all());

        if ($request->hasFile('attachments')) {
            $reply->saveAttachments($request->file('attachments'));
        }

        event(new TicketReplied($reply));

        return redirect()->route('admin.account.ticket')
            ->with('success', trans('messages.updated', ['model' => trans('app.model.ticket')]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archiveTicket(Request $request, Ticket $ticket)
    {
        if (Auth::id() != $ticket->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $ticket->delete();

        return redirect()->route('admin.account.ticket')
            ->with('success', trans('messages.deleted', ['model' => trans('app.model.ticket')]));
    }

    /**
     * Show the form for editing the specified resource.
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        return view('admin.account._change_password');
    }

    /**
     * Update profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProfileRequest $request)
    {
        if (config('app.demo') == true && Auth::user()->id <= config('system.demo.users', 3)) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        $this->profile->updateProfile($request);

        if (!customer_can_register()) {
            $buyer_profile = Customer::where('email', Auth::user()->email)->first();
            if ($buyer_profile) {
                $buyer_profile->update($request->all());
            }
        }

        event(new ProfileUpdated(Auth::user()));

        return redirect()->route('admin.account.profile')->with('success', trans('messages.profile_updated'));
    }

    /**
     * Update login password only.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        if (config('app.demo') == true && Auth::user()->id <= config('system.demo.users', 3)) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        $this->profile->updatePassword($request);

        if (!customer_can_register()) {
            $buyer_profile = Customer::where('email', Auth::user()->email)->first();
            if ($buyer_profile) {
                $buyer_profile->fill([
                    'password' => $request->input('password'),
                ])->save();
            }
        }

        event(new PasswordUpdated(Auth::user()));

        return redirect()->route('admin.account.profile')->with('success', trans('messages.password_updated'));
    }

    /**
     * Update Photo only.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePhoto(UpdatePhotoRequest $request)
    {
        $this->profile->updatePhoto($request);

        return redirect()->route('admin.account.profile')->with('success', trans('messages.profile_updated'));
    }

    /**
     * Remove photo from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePhoto(DeletePhotoRequest $request)
    {
        $this->profile->deletePhoto($request);

        return redirect()->route('admin.account.profile')->with('success', trans('messages.profile_updated'));
    }

    /**
     * Form for get Bank account number of shop
     */
    public function editPayoutInstruction()
    {
        $shop = Shop::where('owner_id', Auth::user()->id)->first();

        $payout_instruction = $shop->pay_to;

        return view('admin.account.pay_to._edit', compact('payout_instruction'));
    }

    /**
     * @param Request $request
     * update shop Bank account number to database
     */
    public function updatePayoutInstruction(Request $request)
    {
        Shop::where('owner_id', Auth::user()->id)
            ->update(['pay_to' => $request->input('payout_instruction')]);

        return back()->with('success', trans('messages.profile_updated'));
    }
}
