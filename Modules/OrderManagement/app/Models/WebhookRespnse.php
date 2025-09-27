<?php

namespace Modules\OrderManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\OrderManagement\Database\Factories\WebhookRespnseFactory;

class WebhookRespnse extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'type',
        'raw_payload',
        'payload',
        'signature',
        'received_at',
        'processed_at',
        'status',
        'http_status',
        'error_message',
        'attempts',
    ];

    protected $casts = [
        'payload' => 'array',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];
}
