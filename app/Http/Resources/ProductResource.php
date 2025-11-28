<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'originalPrice' => $this->original_price,
            'price' => $this->price,
            'discountPercentage' => $this->discount_percentage,
            'totalDiscountPercentage' => $this->total_discount_percentage,
            'savings' => $this->savings,
            'size' => $this->size,

            'exteriorImage' => $this->exterior_image,
            'interiorImages' => $this->interior_images,

            'categories' => $this->categories,
            'category' => $this->category,
            'tags' => $this->tags,

            'inStock' => $this->in_stock,
            'featured' => $this->featured,

            'description' => $this->description,
            'benefits' => $this->benefits,
            'ingredients' => $this->ingredients,
            'howToUse' => $this->how_to_use,
            'warnings' => $this->warnings,

            'rating' => $this->rating,
            'reviews' => $this->reviews,
            'soldCount' => $this->sold_count,
        ];
    }
}
