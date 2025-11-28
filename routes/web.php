<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/{id}/edit', function ($id) {
    $product = \App\Models\Product::findOrFail($id);
    return view('update', ['product' => $product]);
});
Route::get('/products/create', \App\Livewire\ProductForm::class);
Route::get('/products/{product}/edit', \App\Livewire\ProductForm::class);
