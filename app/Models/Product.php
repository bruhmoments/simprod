<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = ['name', 'category_id', 'purchase_price', 'selling_price', 'stock', 'image_path'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
