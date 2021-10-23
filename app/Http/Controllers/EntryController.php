<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Consts\UserConst;
use App\Models\JobOffer;
use App\Models\Entry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Consts\EntryConst;

class EntryController extends Controller
{
    public function store(JobOffer $jobOffer)
    {
        $entry = new Entry([
            'job_offer_id' => $jobOffer->id,
            'user_id' => Auth::guard(UserConst::GUARD)->user()->id,
        ]);

        // トランザクション開始
        DB::beginTransaction();
        try {
            // 登録
            $entry->save();

            // トランザクション終了(成功)
            DB::commit();
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            DB::rollback();
            return back()->withInput()
                ->withErrors('エントリーでエラーが発生しました');
        }

        return redirect()
            ->route('job_offers.show', $jobOffer)
            ->with('notice', 'エントリーしました');
    }

    public function destroy(JobOffer $jobOffer, Entry $entry)
    {
        $entry->delete();

        return redirect()->route('job_offers.show', $jobOffer)
            ->with('notice', 'エントリーを取り消しました');
    }

    public function approval(JobOffer $jobOffer, Entry $entry)
    {
        $entry->status = EntryConst::STATUS_APPROVAL;
        $entry->save();

        return redirect()->route('job_offers.show', $jobOffer)
            ->with('notice', 'エントリーを承認しました');
    }

    public function reject(JobOffer $jobOffer, Entry $entry)
    {
        $entry->status = EntryConst::STATUS_REJECT;
        $entry->save();

        return redirect()->route('job_offers.show', $jobOffer)
            ->with('notice', 'エントリーを却下しました');
    }
}
