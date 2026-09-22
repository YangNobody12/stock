<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StockOut;
use App\Models\Category;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'product_code',
        'cost_price',
        'selling_price',
        'quantity',
        'minimum_stock',
        'image',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }
}