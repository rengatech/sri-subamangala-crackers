<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $fillable = [
        'customer_name',
        'mobile_number',
        'address',
        'sub_total',
        'discount',
        'net_amount',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(BillingItem::class);
    }
}
