<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'wa_message_id',
        'phone_number',
        'contact_name',
        'direction',
        'type',
        'body',
        'template_name',
        'media_url',
        'status',
    ];
}
