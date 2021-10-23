<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\JobOffer;

class CompanyController extends Controller
{
    public function dashboard(Request $request)
    {
        $params = $request->query();

        $jobOffers = JobOffer::latest()
            ->with('entries')
            ->MyJobOffer()
            ->searchStatus($params)
            ->paginate(5);

        return view('auth.company.dashboard', compact('jobOffers'));
    }
}
