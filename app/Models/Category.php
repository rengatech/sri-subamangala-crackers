<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $guarded = [];

    protected $casts = [
        'has_discount' => 'boolean',
    ];


    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
