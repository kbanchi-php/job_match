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
        $params = [
            'job_offer_id' => $jobOffer->id,
            'user_id' => $user->id,
        ];
        $messages = Message::search($params)
            ->oldest()->get();

        $partner = '';
        $send_by = '';
        if (Auth::guard(UserConst::GUARD)->check()) {
            $partner = $jobOffer->company;
            $send_by = MessageConst::SEND_BY_USER;
        }
        if (Auth::guard(CompanyConst::GUARD)->check()) {
            $partner = $user;
            $send_by = MessageConst::SEND_BY_COMPANY;
        }

        return view('messages.index', compact('jobOffer', 'messages', 'partner', 'send_by'));
    }

    public function store(Request $request, JobOffer $jobOffer, User $user)
    {
        $message = new Message();
        $message->message = $request->message;
        $message->job_offer_id = $jobOffer->id;
        $message->user_id = $user->id;
        if (Auth::guard(UserConst::GUARD)->check()) {
            $message->send_by = MessageConst::SEND_BY_USER;
        }
        if (Auth::guard(CompanyConst::GUARD)->check()) {
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

        return redirect()
            ->route('job_offers.users.messages.index', [$jobOffer, $user])
            ->with('notice', 'Send Message');
    }
}
