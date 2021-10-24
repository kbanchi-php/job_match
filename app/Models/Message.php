<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'message'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function job_offer()
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function scopeSearch(Builder $query, $params)
    {
        if (!empty($params['job_offer_id']) && !empty($params['user_id'])) {
            $query
                ->where('job_offer_id', $params['job_offer_id'])
                ->where('user_id', $params['user_id']);
        }

        return $query;
    }
}
