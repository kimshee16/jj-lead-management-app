<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadClient extends Model
{
    use HasFactory;

    protected $table = 'lead_clients';

    protected $fillable = [
        'client_id',
        'ads_id',
        'name',
        'email',
        'mobile_number',
        'note',
        'status',
        'lead_type',
        'source_type_id',
        'follow_up_date_time',
        'is_send_discord',
        'is_verified',
        'user_status',
        'registration_no',
        'added_by_id',
        'user_type',
        'delete_by_type',
        'delete_by_id',
        'deleted_at',
        'admin_status',
        'is_admin_spam',
        'client_email',
    ];
}
