<?php

namespace Nrm\ActivityTracker\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{

    protected $table="kyc_logs";
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'country',
        'city',
        'url',
        'method',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
