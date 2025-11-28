<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'slug' => 'string|unique:products,slug,' . $this->product,
            'original_price' => 'numeric|min:0',
            'price' => 'numeric|min:0',

            'discount_percentage' => 'integer|min:0|max:100',
            'total_discount_percentage' => 'integer|min:0|max:200',
            'savings' => 'numeric|min:0',

            'categories' => 'array',
            'interior_images' => 'array',
            'tags' => 'array',
            'benefits' => 'array',
            'ingredients' => 'array',
            'warnings' => 'array',

            'description' => 'string|nullable',
            'how_to_use' => 'string|nullable',

            'in_stock' => 'boolean',
            'featured' => 'boolean',

            'rating' => 'numeric|min:0|max:5',
            'reviews' => 'integer|min:0',
            'sold_count' => 'integer|min:0',
        ];
    }
}
