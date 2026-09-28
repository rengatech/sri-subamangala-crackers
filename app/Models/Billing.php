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

    /**
     * Re-calculate item totals, sub total and net amount from the items,
     * and fill missing product names.
     */
    public function recalculate(): void
    {
        $this->load('items');

        foreach ($this->items as $item) {
            $data = ['total' => round($item->quantity * $item->price, 2)];

            if (blank($item->product_name) && $item->product_id) {
                $data['product_name'] = Product::withTrashed()->find($item->product_id)?->name;
            }

            $item->updateQuietly($data);
        }

        $subTotal = round($this->items->sum('total'), 2);
        $discountAmount = $subTotal * ((float) $this->discount / 100);

        $this->updateQuietly([
            'sub_total' => $subTotal,
            'net_amount' => round(max(0, $subTotal - $discountAmount), 2),
        ]);
    }
}