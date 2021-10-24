<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Consts\JobOfferConst;
use App\Consts\CompanyConst;
use App\Consts\EntryConst;
use Illuminate\Support\Facades\Auth;
use App\Consts\UserConst;

class JobOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'occupation_id',
        'due_date',
        'description',
        'status',
    ];

    public function scopeOpenData(Builder $query)
    {
        $query->where('status', true)
            ->where('due_date', '>=', now());
        return $query;
    }

    public function scopeSearch(Builder $query, $params)
    {
        if (!empty($params['occupation'])) {
            $query->where('occupation_id', $params['occupation']);
        }

        return $query;
    }

    public function scopeOrder(Builder $query, $params)
    {
        if ((empty($params['sort'])) ||
            (!empty($params['sort']) && $params['sort'] == JobOfferConst::SORT_NEW_ARRIVALS)
        ) {
            $query->latest();
        } elseif (!empty($params['sort']) && $params['sort'] == JobOfferConst::SORT_VIEW_RANK) {
            $query->withCount('jobOfferViews')
                ->orderBy('job_offer_views_count', 'desc');
        }

        return $query;
    }

    public function scopeMyJobOffer(Builder $query)
    {
        $query->where(
            'company_id',
            Auth::guard(CompanyConst::GUARD)->user()->id
        );

        return $query;
    }

    public function scopeSearchStatus(Builder $query, $params)
    {
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        return $query;
    }

    public function scopeSearchEntry(Builder $query, $params)
    {
        $query->whereHas('entries', function ($query) use ($params) {
            $query->where('user_id', Auth::guard(UserConst::GUARD)->user()->id);
            if ((empty($params['status'])) ||
                ($params['status'] == EntryConst::STATUS_ENTRY)
            ) {
                $query->where('status', EntryConst::STATUS_ENTRY);
            } elseif ($params['status'] == EntryConst::STATUS_APPROVAL) {
                $query->where('status', EntryConst::STATUS_APPROVAL);
            } elseif ($params['status'] == EntryConst::STATUS_REJECT) {
                $query->where('status', EntryConst::STATUS_REJECT);
            }
        });

        return $query;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class);
    }

    public function jobOfferViews()
    {
        return $this->hasMany(JobOfferView::class);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
