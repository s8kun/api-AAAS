<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products,slug',
            'original_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'total_discount_percentage' => 'nullable|integer|min:0|max:200',
            'savings' => 'nullable|numeric',

            'size' => 'nullable|string',
            'exterior_image' => 'nullable|string',
            'interior_images' => 'nullable|array',

            'categories' => 'nullable|array',
            'category' => 'required|string',
            'tags' => 'nullable|array',

            'in_stock' => 'boolean',
            'featured' => 'boolean',

            'description' => 'nullable|string',
            'benefits' => 'nullable|array',
            'ingredients' => 'nullable|array',
            'how_to_use' => 'nullable|string',
            'warnings' => 'nullable|array',

            'rating' => 'nullable|numeric|min:0|max:5',
            'reviews' => 'nullable|integer|min:0',
            'sold_count' => 'nullable|integer|min:0',
        ];
    }
}
