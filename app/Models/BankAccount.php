<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['bank_name', 'branch', 'name', 'account_number', 'ifsc_code', 'upi_id', 'g_pay', 'image'];

    
    public function order()
    {
        return $this->hasMany(Order::class);
    } 
}
