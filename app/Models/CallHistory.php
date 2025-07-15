<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallHistory extends Model
{
    use HasFactory;

    protected $table = 'call_history';

    protected $fillable = [
        'phone_number',
        'email',
        'lead_client_id',
        'call_timestamp',
        'call_status',
        'call_response',
        'call_sid',
    ];

    protected $casts = [
        'call_timestamp' => 'datetime',
    ];

    /**
     * Get the lead client associated with this call.
     */
    public function leadClient()
    {
        return $this->belongsTo(LeadClient::class, 'lead_client_id');
    }
}
