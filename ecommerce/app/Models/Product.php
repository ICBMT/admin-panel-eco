<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'stock',
        'category_id',
        'image_path',
        'description',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
