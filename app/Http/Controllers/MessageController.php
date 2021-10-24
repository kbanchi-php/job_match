<?php

namespace App\Http\Controllers;

use App\Consts\CompanyConst;
use App\Consts\MessageConst;
use App\Consts\UserConst;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(JobOffer $jobOffer, User $user)
    {

        $query = Message::query();
        $messages = $query
            ->where('job_offer_id', $jobOffer->id)
            ->where('user_id', $user->id)
            ->get()->sortBy('created_at');

        $sender = '';
        $partner = '';
        $send_by = '';
        if (Auth::guard(UserConst::GUARD)->check()) {
            $sender = $user;
            $partner = $jobOffer->company;
            $send_by = MessageConst::SEND_BY_USER;
        }
        if (Auth::guard(CompanyConst::GUARD)->check()) {
            $sender = $jobOffer->company;
            $partner = $user;
            $send_by = MessageConst::SEND_BY_COMPANY;
        }

        return view('messages.index', compact('jobOffer', 'messages', 'sender', 'partner', 'send_by'));
    }

    public function store(Request $request, JobOffer $jobOffer, User $user)
    {
        $message = new Message();
        $message->message = $request->message;
        $message->job_offer_id = $jobOffer->id;
        if (Auth::guard(UserConst::GUARD)->check()) {
            $message->user_id = $user->id;
            $message->send_by = MessageConst::SEND_BY_USER;
        }
        if (Auth::guard(CompanyConst::GUARD)->check()) {
            $message->user_id = $request->partner_id;
            $message->send_by = MessageConst::SEND_BY_COMPANY;
        }

        DB::beginTransaction();
        try {
            $message->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->withErrors('エラーが発生しました');
        }

        if (Auth::guard(UserConst::GUARD)->check()) {
            return redirect()
                ->route('job_offers.users.messages.index', [$jobOffer, $user])
                ->with('notice', 'Send Message');
        }
        if (Auth::guard(CompanyConst::GUARD)->check()) {
            return redirect()
                ->route('job_offers.users.messages.index', [$jobOffer, User::find($request->partner_id)])
                ->with('notice', 'Send Message');
        }
    }
}
