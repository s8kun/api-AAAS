<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'original_price',
        'price',
        'discount_percentage',
        'total_discount_percentage',
        'savings',
        'size',
        'exterior_image',
        'interior_images',
        'categories',
        'category',
        'tags',
        'in_stock',
        'featured',
        'description',
        'benefits',
        'ingredients',
        'how_to_use',
        'warnings',
        'rating',
        'reviews',
        'sold_count'
    ];

    protected $casts = [
        'interior_images' => 'array',
        'categories' => 'array',
        'tags' => 'array',
        'benefits' => 'array',
        'ingredients' => 'array',
        'warnings' => 'array',
        'in_stock' => 'boolean',
        'featured' => 'boolean',
    ];
}
