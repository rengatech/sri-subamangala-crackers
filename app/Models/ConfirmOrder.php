<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmOrder extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'net_total', 'discount_total', 'sub_total', 'address_id', 'status'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function bank_account()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class);
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
