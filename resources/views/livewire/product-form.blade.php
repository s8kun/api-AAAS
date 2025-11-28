<div class="max-w-4xl mx-auto p-4">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold">{{ $productId ? 'Edit Product' : 'Create New Product' }}</h2>
        <p class="text-sm text-gray-600">{{ $productId ? 'Update existing product details.' : 'Add a new item to your inventory with ease.' }}</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-3 border rounded bg-green-50">
            <div class="flex items-center gap-3">
                <div class="text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="text-sm text-green-800">
                    {{ session('message') }}
                </div>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium">Product Name</label>
                <input type="text" wire:model.defer="name" id="name" class="mt-1 block w-full rounded border p-2"
                       placeholder="e.g. Premium Leather Jacket">
                @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block text-sm font-medium">Slug</label>
                <input type="text" wire:model.defer="slug" id="slug" class="mt-1 block w-full rounded border p-2"
                       placeholder="Auto-generated or edit manually">
                @error('slug') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Original Price -->
            <div>
                <label for="original_price" class="block text-sm font-medium">Original Price </label>
                <div class="mt-1 flex items-center">
                    <span class="mr-2">$</span>
                    <input type="number" step="0.01" wire:model.defer="original_price" id="original_price"
                           class="block w-full rounded border p-2" required placeholder="0.00">
                </div>
                @error('original_price') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Price -->
            <div>
                <label for="price" class="block text-sm font-medium">Sale Price</label>
                <div class="mt-1 flex items-center">
                    <span class="mr-2">$</span>
                    <input type="number" step="0.01" wire:model.defer="price" id="price" class="block w-full rounded border p-2"
                           required placeholder="0.00">
                </div>
                @error('price') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Display savings & discount if available -->
            <div class="md:col-span-2">
                @if($savings > 0)
                    <div class="p-3 bg-yellow-50 border rounded">
                        <p class="text-sm font-medium">Savings: <span class="font-semibold">${{ number_format($savings, 2) }}</span></p>
                        <p class="text-sm text-gray-600">Discount: <span class="font-semibold">{{ $discount_percentage }}%</span>
                            @if(isset($total_discount_percentage) && $total_discount_percentage != $discount_percentage)
                                (Total after global discount: {{ $total_discount_percentage }}%)
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            <!-- Category (main) -->
            <div>
                <label for="category" class="block text-sm font-medium">Category</label>
                <input type="text" wire:model.defer="category" id="category" class="mt-1 block w-full rounded border p-2"
                       placeholder="e.g. Electronics">
                @error('category') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Categories (tags-like, array) -->
            <div>
                <label class="block text-sm font-medium">Categories (optional - comma separated or use inputs below)</label>
                <input type="text" wire:model.defer="categories" class="mt-1 block w-full rounded border p-2"
                       placeholder="electronics, men, jackets">
                @error('categories') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Tags -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Tags (comma separated or provide as array below)</label>
                <input type="text" wire:model.defer="tags" class="mt-1 block w-full rounded border p-2"
                       placeholder="new, winter, sale">
                @error('tags') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Size -->
            <div>
                <label for="size" class="block text-sm font-medium">Size (optional)</label>
                <input type="text" wire:model.defer="size" id="size" class="mt-1 block w-full rounded border p-2"
                       placeholder="e.g. M, 42, 1L">
                @error('size') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- In stock / Featured -->
            <div class="flex items-center gap-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model.defer="in_stock" class="mr-2">
                    <span class="text-sm">In Stock</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model.defer="featured" class="mr-2">
                    <span class="text-sm">Featured</span>
                </label>
            </div>

            <!-- Exterior Image -->
            <div class="md:col-span-2">
                <label for="exterior_image" class="block text-sm font-medium">Exterior Image URL</label>
                <div class="mt-1 flex gap-2">
                    <input type="text" wire:model.defer="exterior_image" id="exterior_image" class="block w-full rounded border p-2"
                           placeholder="https://example.com/image.jpg">
                    <span class="self-center text-sm text-gray-500">URL</span>
                </div>
                @if($exterior_image)
                    <div class="mt-2">
                        <img src="{{ $exterior_image }}" alt="Preview" class="w-32 h-32 object-cover rounded border">
                    </div>
                @endif
                @error('exterior_image') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Interior Images (array inputs) -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Interior Images (URLs)</label>
                <div class="space-y-2 mt-2">
                    @foreach($interior_images as $index => $img)
                        <div class="flex items-center gap-2">
                            <input type="text" wire:model.defer="interior_images.{{ $index }}" class="flex-1 rounded border p-2"
                                   placeholder="https://example.com/interior.jpg">
                            <button type="button" wire:click="removeInteriorImage({{ $index }})" class="p-2 rounded bg-red-50 border">
                                Remove
                            </button>
                        </div>
                    @endforeach
                </div>
                <div class="mt-2">
                    <button type="button" wire:click="addInteriorImage" class="px-3 py-2 rounded border bg-gray-50">
                        + Add Another Image
                    </button>
                </div>
                @error('interior_images.*') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Benefits (array) -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Benefits (optional)</label>
                <div class="space-y-2 mt-2">
                    @foreach($benefits as $i => $b)
                        <div class="flex items-center gap-2">
                            <input type="text" wire:model.defer="benefits.{{ $i }}" class="flex-1 rounded border p-2" placeholder="e.g. Waterproof">
                            <button type="button" wire:click="removeBenefit({{ $i }})" class="p-2 rounded bg-red-50 border">Remove</button>
                        </div>
                    @endforeach
                </div>
                <div class="mt-2">
                    <button type="button" wire:click="addBenefit" class="px-3 py-2 rounded border bg-gray-50">+ Add Benefit</button>
                </div>
                @error('benefits.*') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Ingredients (array) -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Ingredients (optional)</label>
                <div class="space-y-2 mt-2">
                    @foreach($ingredients as $i => $ing)
                        <div class="flex items-center gap-2">
                            <input type="text" wire:model.defer="ingredients.{{ $i }}" class="flex-1 rounded border p-2" placeholder="e.g. Cotton">
                            <button type="button" wire:click="$emit('removeIngredient', {{ $i }})" wire:click.prevent="removeIngredient({{ $i }})" class="p-2 rounded bg-red-50 border">Remove</button>
                        </div>
                    @endforeach
                </div>
                <div class="mt-2">
                    <button type="button" wire:click="$set('ingredients', array_merge(is_array($ingredients) ? $ingredients : [], ['']))" class="px-3 py-2 rounded border bg-gray-50">+ Add Ingredient</button>
                </div>
                @error('ingredients.*') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- How to use -->
            <div class="md:col-span-2">
                <label for="how_to_use" class="block text-sm font-medium">How to Use (optional)</label>
                <textarea wire:model.defer="how_to_use" id="how_to_use" rows="4" class="mt-1 block w-full rounded border p-2" placeholder="Instructions..."></textarea>
                @error('how_to_use') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Warnings (array optional) -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Warnings (optional)</label>
                <div class="space-y-2 mt-2">
                    @foreach($warnings as $i => $w)
                        <div class="flex items-center gap-2">
                            <input type="text" wire:model.defer="warnings.{{ $i }}" class="flex-1 rounded border p-2" placeholder="e.g. Keep away from children">
                            <button type="button" wire:click="$set('warnings', array_values(array_diff($warnings, [$warnings[$i]])))" wire:click.prevent="removeWarning({{ $i }})" class="p-2 rounded bg-red-50 border">Remove</button>
                        </div>
                    @endforeach
                </div>
                <div class="mt-2">
                    <button type="button" wire:click="$set('warnings', array_merge(is_array($warnings) ? $warnings : [], ['']))" class="px-3 py-2 rounded border bg-gray-50">+ Add Warning</button>
                </div>
                @error('warnings.*') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium">Description (optional)</label>
            <textarea wire:model.defer="description" rows="5" class="mt-1 block w-full rounded border p-2" placeholder="Full product description..."></textarea>
            @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Hidden stats (optional editable) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium">Rating</label>
                <input type="number" step="0.1" wire:model.defer="rating" class="mt-1 block w-full rounded border p-2" min="0" max="5">
                @error('rating') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Reviews</label>
                <input type="number" wire:model.defer="reviews" class="mt-1 block w-full rounded border p-2" min="0">
                @error('reviews') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Sold Count</label>
                <input type="number" wire:model.defer="sold_count" class="mt-1 block w-full rounded border p-2" min="0">
                @error('sold_count') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-4 border-t">
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2 rounded bg-blue-600 text-white">{{ $productId ? 'Update Product' : 'Save Product' }}</button>
            </div>
        </div>
    </form>
</div>
