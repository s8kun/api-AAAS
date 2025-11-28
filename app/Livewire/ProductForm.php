<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Component;

class ProductForm extends Component
{
    // Required
    public $productId; // For edit mode

    public $name;

    public $slug;

    public $price;

    public $original_price;

    public function mount($product = null)
    {
        if ($product) {
            $this->productId = $product->id;
            $this->name = $product->name;
            $this->slug = $product->slug;
            $this->price = $product->price;
            $this->original_price = $product->original_price;
            $this->exterior_image = $product->exterior_image;
            $this->interior_images = $product->interior_images ?? [];
            $this->category = $product->category;
            $this->categories = $product->categories ?? [];
            $this->tags = $product->tags ?? [];
            $this->size = $product->size;
            $this->description = $product->description;
            $this->benefits = $product->benefits ?? [];
            $this->ingredients = $product->ingredients ?? [];
            $this->how_to_use = $product->how_to_use;
            $this->warnings = $product->warnings ?? [];
            $this->in_stock = (bool) $product->in_stock;
            $this->featured = (bool) $product->featured;
            $this->rating = $product->rating;
            $this->reviews = $product->reviews;
            $this->sold_count = $product->sold_count;

            $this->computeDiscounts();
        }
    }

    // Images
    public $exterior_image = ''; // رابط واحد (nullable)

    public $interior_images = []; // مصفوفة روابط (nullable)

    // Categories/Tags (يمكن ارسال كمصفوفة او نص مفصول بفواصل)
    public $category; // السجل الرئيسي category (string)

    public $categories = []; // قائمة ثانوية (json)

    public $tags = []; // json

    // Optional product details
    public $size;

    public $description;

    public $benefits = []; // json

    public $ingredients = []; // json

    public $how_to_use; // نص طويل (nullable)

    public $warnings = []; // json

    // Flags & stats
    public $in_stock = true;

    public $featured = false;

    public $rating = 0;

    public $reviews = 0;

    public $sold_count = 0;

    // Computed / stored
    public $discount_percentage = 0; // optional: يمكن حسابه أو ادخاله يدوياً

    public $total_discount_percentage = 0; // after applying global discount

    public $savings = 0;

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255',
        'price' => 'required|numeric|min:0',
        'original_price' => 'required|numeric|min:0',
        'exterior_image' => 'nullable|url',
        'interior_images.*' => 'nullable|url',
        'category' => 'required|string|max:255',
        'categories' => 'nullable|array',
        'categories.*' => 'nullable|string|max:255',
        'tags' => 'nullable|array',
        'tags.*' => 'nullable|string|max:255',
        'size' => 'nullable|string|max:100',
        'description' => 'nullable|string',
        'benefits' => 'nullable|array',
        'benefits.*' => 'nullable|string',
        'ingredients' => 'nullable|array',
        'ingredients.*' => 'nullable|string',
        'how_to_use' => 'nullable|string',
        'warnings' => 'nullable|array',
        'warnings.*' => 'nullable|string',
        'in_stock' => 'boolean',
        'featured' => 'boolean',
        'rating' => 'numeric|min:0|max:5',
        'reviews' => 'integer|min:0',
        'sold_count' => 'integer|min:0',
    ];

    public function updatedName($value)
    {
        if (! $this->slug) {
            $this->slug = Str::slug($value);
        }
    }

    // Helpers to add/remove interior images and array items
    public function addInteriorImage()
    {
        $this->interior_images[] = '';
    }

    public function removeInteriorImage($i)
    {
        if (isset($this->interior_images[$i])) {
            unset($this->interior_images[$i]);
            $this->interior_images = array_values($this->interior_images);
        }
    }

    public function addBenefit()
    {
        $this->benefits[] = '';
    }

    public function removeBenefit($i)
    {
        if (isset($this->benefits[$i])) {
            unset($this->benefits[$i]);
            $this->benefits = array_values($this->benefits);
        }
    }

    // Convert comma-separated strings to arrays if needed
    protected function normalizeArraysBeforeValidate()
    {
        // If user passed comma-separated strings for categories or tags, convert them
        if (is_string($this->categories) && strlen($this->categories) > 0) {
            $this->categories = array_values(array_filter(array_map('trim', explode(',', $this->categories))));
        }

        if (is_string($this->tags) && strlen($this->tags) > 0) {
            $this->tags = array_values(array_filter(array_map('trim', explode(',', $this->tags))));
        }

        if (is_string($this->benefits) && strlen($this->benefits) > 0) {
            $this->benefits = array_values(array_filter(array_map('trim', explode(',', $this->benefits))));
        }

        if (is_string($this->ingredients) && strlen($this->ingredients) > 0) {
            $this->ingredients = array_values(array_filter(array_map('trim', explode(',', $this->ingredients))));
        }

        if (is_string($this->warnings) && strlen($this->warnings) > 0) {
            $this->warnings = array_values(array_filter(array_map('trim', explode(',', $this->warnings))));
        }

        // ensure interior_images is an array
        if ($this->interior_images === null) {
            $this->interior_images = [];
        }
    }

    protected function computeDiscounts()
    {
        $original = floatval($this->original_price ?? 0);
        $price = floatval($this->price ?? 0);

        // savings
        $this->savings = 0;
        if ($original > 0 && $price < $original) {
            $this->savings = round($original - $price, 2);
            $this->discount_percentage = (int) round((($original - $price) / $original) * 100);
        } else {
            // no savings if original not set or price >= original
            $this->savings = 0;
            $this->discount_percentage = 0;
        }

        // apply global discount from config/shop.php if exists
        $globalDiscount = (int) config('shop.global_discount', 0);

        $this->total_discount_percentage = $this->discount_percentage + $globalDiscount;
        if ($this->total_discount_percentage > 100) {
            $this->total_discount_percentage = 100;
        }
    }

    public function save()
    {
        // Normalize array-like fields if user passed comma-separated strings
        $this->normalizeArraysBeforeValidate();

        // Validate
        $this->validate();

        // Compute savings and discounts
        $this->computeDiscounts();

        // Ensure slug
        $slug = $this->slug ?: Str::slug($this->name);

        // Create the product
        // Create or Update the product
        if ($this->productId) {
            $product = Product::find($this->productId);
            $product->update([
                'name' => $this->name,
                'slug' => $slug,
                'price' => $this->price,
                'original_price' => $this->original_price ?? $this->price,
                'discount_percentage' => $this->discount_percentage,
                'total_discount_percentage' => $this->total_discount_percentage,
                'savings' => $this->savings,
                'size' => $this->size,
                'exterior_image' => $this->exterior_image,
                'interior_images' => $this->interior_images,
                'categories' => $this->categories,
                'category' => $this->category,
                'tags' => $this->tags,
                'in_stock' => $this->in_stock,
                'featured' => $this->featured,
                'description' => $this->description,
                'benefits' => $this->benefits,
                'ingredients' => $this->ingredients,
                'how_to_use' => $this->how_to_use,
                'warnings' => $this->warnings,
                'rating' => $this->rating,
                'reviews' => $this->reviews,
                'sold_count' => $this->sold_count,
            ]);
            session()->flash('message', 'Product Updated Successfully');
        } else {
            Product::create([
                'name' => $this->name,
                'slug' => $slug,
                'price' => $this->price,
                'original_price' => $this->original_price ?? $this->price,
                'discount_percentage' => $this->discount_percentage,
                'total_discount_percentage' => $this->total_discount_percentage,
                'savings' => $this->savings,
                'size' => $this->size,
                'exterior_image' => $this->exterior_image,
                'interior_images' => $this->interior_images,
                'categories' => $this->categories,
                'category' => $this->category,
                'tags' => $this->tags,
                'in_stock' => $this->in_stock,
                'featured' => $this->featured,
                'description' => $this->description,
                'benefits' => $this->benefits,
                'ingredients' => $this->ingredients,
                'how_to_use' => $this->how_to_use,
                'warnings' => $this->warnings,
                'rating' => $this->rating,
                'reviews' => $this->reviews,
                'sold_count' => $this->sold_count,
            ]);
            session()->flash('message', 'Product Created Successfully');
            $this->resetForm();
        }

        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->reset([
            'name', 'slug', 'price', 'original_price', 'exterior_image', 'interior_images',
            'category', 'categories', 'tags', 'size', 'description', 'benefits', 'ingredients',
            'how_to_use', 'warnings', 'in_stock', 'featured', 'rating', 'reviews', 'sold_count',
            'discount_percentage', 'total_discount_percentage', 'savings',
        ]);
    }

    public function render()
    {
        return view('livewire.product-form');
    }
}
