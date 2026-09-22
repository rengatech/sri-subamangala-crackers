<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Outofstock;
use App\Models\Tag;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'tamil_name',
        'name',
        'url_slug',
        'seo_title',
        'description',
        'category_id',
        'price',
        'image',
        'video_url',
        'discount',
        'limited_stock',
        'out_of_stock',
        'unit'

    ];

    protected $casts = [
        'property' => 'array',
        'quantity' => 'integer',
        'out_of_stock' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }



    // public function outofstocks()
    // {
    //     return $this->hasMany(Outofstock::class);
    // }


    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

}
