<?php

namespace App\Interfaces;

use App\Models\Product;

interface ProductManagerServiceInterface
{
    /**
     * @param Product $product
     * @param string $message
     * @return Product
     */
    public function processRequestData(Product $product, string $message): Product;

    /**
     * @param Product $product
     * @return \App\Models\File|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function handleImageRequestData(Product $product);
}
