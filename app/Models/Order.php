<?php

namespace App\Models;

use App\Models\Address;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Dispatch;
use App\Models\Payment;
use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;

    use SoftDeletes;
 
    protected $fillable = ['customer_id', 'net_total', 'discount_total', 'sub_total', 'address_id', 'status', 'lr_screenshot'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address(){
        return $this->belongsTo(Address::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function bank_account(){
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
