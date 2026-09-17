<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'mobile_number', 'whatsapp_number'];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function address()
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getFormattedWhatsappNumberAttribute(): string
    {
        return preg_replace('/[^0-9]/', '', $this->whatsapp_number);
    }

    public function getWhatsappUrlAttribute(): string
    {
        return "https://wa.me/{$this->formatted_whatsapp_number}";
    }

    public function getWhatsappMessageUrl(string $message): string
    {
        $encodedMessage = urlencode($message);
        return "https://wa.me/{$this->formatted_whatsapp_number}?text={$encodedMessage}";
    }
}
