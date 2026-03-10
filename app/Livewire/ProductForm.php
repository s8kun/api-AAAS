<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Component;

class ProductForm extends Component
{
    public $productId = null;

    public $name = '';
    public $slug = '';
    public $price = '';
    public $original_price = '';

    public $exterior_image = '';
    public $interior_images = [];

    public $category = '';
    public $categories = [];
    public $tags = [];

    public $size = '';
    public $description = '';
    public $benefits = [];
    public $ingredients = [];
    public $how_to_use = '';
    public $warnings = [];

    public $in_stock = true;
    public $featured = false;

    public $rating = 0;
    public $reviews = 0;
    public $sold_count = 0;

    public $discount_percentage = 0;
    public $total_discount_percentage = 0;
    public $savings = 0;

    protected function rules()
    {
        $productId = $this->productId ?? 'NULL';

        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $productId,
            'price' => 'required|numeric|min:0',
            'original_price' => 'required|numeric|min:0',

            'exterior_image' => 'nullable|url',
            'interior_images' => 'nullable|array',
            'interior_images.*' => 'nullable|url',

            'category' => 'required|string|max:255',
            'categories' => 'nullable',
            'tags' => 'nullable',

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

            'rating' => 'nullable|numeric|min:0|max:5',
            'reviews' => 'nullable|integer|min:0',
            'sold_count' => 'nullable|integer|min:0',
        ];
    }

    public function mount($product = null)
    {
        if ($product) {
            $this->productId = $product->id;
            $this->name = $product->name ?? '';
            $this->slug = $product->slug ?? '';
            $this->price = $product->price ?? '';
            $this->original_price = $product->original_price ?? '';

            $this->exterior_image = $product->exterior_image ?? '';
            $this->interior_images = is_array($product->interior_images) ? $product->interior_images : ($product->interior_images ?? []);

            $this->category = $product->category ?? '';
            $this->categories = is_array($product->categories) ? $product->categories : ($product->categories ?? []);
            $this->tags = is_array($product->tags) ? $product->tags : ($product->tags ?? []);

            $this->size = $product->size ?? '';
            $this->description = $product->description ?? '';
            $this->benefits = is_array($product->benefits) ? $product->benefits : ($product->benefits ?? []);
            $this->ingredients = is_array($product->ingredients) ? $product->ingredients : ($product->ingredients ?? []);
            $this->how_to_use = $product->how_to_use ?? '';
            $this->warnings = is_array($product->warnings) ? $product->warnings : ($product->warnings ?? []);

            $this->in_stock = (bool) ($product->in_stock ?? true);
            $this->featured = (bool) ($product->featured ?? false);

            $this->rating = $product->rating ?? 0;
            $this->reviews = $product->reviews ?? 0;
            $this->sold_count = $product->sold_count ?? 0;

            $this->computeDiscounts();
        }
    }

    public function updatedName($value)
    {
        if (blank($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedPrice()
    {
        $this->computeDiscounts();
    }

    public function updatedOriginalPrice()
    {
        $this->computeDiscounts();
    }

    public function addInteriorImage()
    {
        $this->interior_images[] = '';
    }

    public function removeInteriorImage($index)
    {
        if (isset($this->interior_images[$index])) {
            unset($this->interior_images[$index]);
            $this->interior_images = array_values($this->interior_images);
        }
    }

    public function addBenefit()
    {
        $this->benefits[] = '';
    }

    public function removeBenefit($index)
    {
        if (isset($this->benefits[$index])) {
            unset($this->benefits[$index]);
            $this->benefits = array_values($this->benefits);
        }
    }

    public function addIngredient()
    {
        $this->ingredients[] = '';
    }

    public function removeIngredient($index)
    {
        if (isset($this->ingredients[$index])) {
            unset($this->ingredients[$index]);
            $this->ingredients = array_values($this->ingredients);
        }
    }

    public function addWarning()
    {
        $this->warnings[] = '';
    }

    public function removeWarning($index)
    {
        if (isset($this->warnings[$index])) {
            unset($this->warnings[$index]);
            $this->warnings = array_values($this->warnings);
        }
    }

    protected function normalizeArraysBeforeValidate()
    {
        if (is_string($this->categories)) {
            $this->categories = $this->stringToArray($this->categories);
        }

        if (is_string($this->tags)) {
            $this->tags = $this->stringToArray($this->tags);
        }

        if (is_string($this->benefits)) {
            $this->benefits = $this->stringToArray($this->benefits);
        }

        if (is_string($this->ingredients)) {
            $this->ingredients = $this->stringToArray($this->ingredients);
        }

        if (is_string($this->warnings)) {
            $this->warnings = $this->stringToArray($this->warnings);
        }

        if ($this->interior_images === null) {
            $this->interior_images = [];
        }

        $this->interior_images = array_values(array_filter((array) $this->interior_images, fn ($item) => filled($item)));
        $this->benefits = array_values(array_filter((array) $this->benefits, fn ($item) => filled($item)));
        $this->ingredients = array_values(array_filter((array) $this->ingredients, fn ($item) => filled($item)));
        $this->warnings = array_values(array_filter((array) $this->warnings, fn ($item) => filled($item)));
        $this->categories = array_values(array_filter((array) $this->categories, fn ($item) => filled($item)));
        $this->tags = array_values(array_filter((array) $this->tags, fn ($item) => filled($item)));
    }

    protected function stringToArray(string $value): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $value)), fn ($item) => $item !== ''));
    }

    protected function computeDiscounts()
    {
        $original = (float) ($this->original_price ?: 0);
        $price = (float) ($this->price ?: 0);

        $this->savings = 0;
        $this->discount_percentage = 0;

        if ($original > 0 && $price < $original) {
            $this->savings = round($original - $price, 2);
            $this->discount_percentage = (int) round((($original - $price) / $original) * 100);
        }

        $globalDiscount = (int) config('shop.global_discount', 0);
        $this->total_discount_percentage = min($this->discount_percentage + $globalDiscount, 100);
    }

    public function save()
    {
        $this->normalizeArraysBeforeValidate();
        $this->computeDiscounts();
        $validated = $this->validate();

        $validated['slug'] = filled($this->slug) ? Str::slug($this->slug) : Str::slug($this->name);
        $validated['original_price'] = $this->original_price ?: $this->price;
        $validated['discount_percentage'] = $this->discount_percentage;
        $validated['total_discount_percentage'] = $this->total_discount_percentage;
        $validated['savings'] = $this->savings;

        if ($this->productId) {
            $product = Product::findOrFail($this->productId);
            $product->update($validated);

            session()->flash('message', 'Product Updated Successfully');
        } else {
            Product::create($validated);

            session()->flash('message', 'Product Created Successfully');
            $this->resetForm();
        }
    }

    protected function resetForm()
    {
        $this->reset([
            'productId',
            'name',
            'slug',
            'price',
            'original_price',
            'exterior_image',
            'interior_images',
            'category',
            'categories',
            'tags',
            'size',
            'description',
            'benefits',
            'ingredients',
            'how_to_use',
            'warnings',
            'in_stock',
            'featured',
            'rating',
            'reviews',
            'sold_count',
            'discount_percentage',
            'total_discount_percentage',
            'savings',
        ]);

        $this->in_stock = true;
        $this->featured = false;
        $this->rating = 0;
        $this->reviews = 0;
        $this->sold_count = 0;

        $this->interior_images = [];
        $this->categories = [];
        $this->tags = [];
        $this->benefits = [];
        $this->ingredients = [];
        $this->warnings = [];
    }

    public function render()
    {
        return view('livewire.product-form');
    }
}
