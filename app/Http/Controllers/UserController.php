<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobOffer;
use Illuminate\Support\Facades\Auth;
use App\Consts\UserConst;

class UserController extends Controller
{
    public function dashboard(Request $request)
    {

        $param = $request->query();
        $status = $request->status;

        $jobOffers = JobOffer::searchEntry()
            ->searchEntryStatus($param)
            ->latest()->paginate(5);

        $jobOffers->appends(compact('status'));
        $status = empty($request->status) ? [] : ['status' => $request->status];

        return view('auth.user.dashboard', compact('jobOffers', 'status'));
    }
}
