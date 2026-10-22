<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaMessageLog extends Model
{
    use HasFactory;

    // Menentukan bahwa model ini terhubung ke database WA Gateway
    protected $connection = 'mysql_wa';

    protected $table = 'wa_message_logs';

    protected $fillable = [
        'recipient_phone',
        'recipient_name',
        'message',
        'type',
        'status',
        'response_gateway',
        'sent_at',
    ];
}