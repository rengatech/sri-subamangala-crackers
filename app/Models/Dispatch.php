<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Order;

class Dispatch extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'LR_number', 'transport', 'book_date', 'delivery_date', 'status', 'lr_screenshot_path'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->order->items();
    }

    public function customer()
    {
        return $this->order->customer();
    }

    public function address()
    {
        return $this->order->address();
    }
}
