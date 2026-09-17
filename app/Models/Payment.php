<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BankAccount;
use App\Models\Order;


class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];

    // protected $fillable = ['order_id', 'bank_account_id', 'payment_received_date'];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function order()
    {
        return $this->hasOne(Order::class,'order_id');
    }
}
