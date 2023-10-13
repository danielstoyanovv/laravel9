<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\ProductManagerServiceInterface;
use App\Models\Product;
use Database\Factories\FileFactory;
use function public_path;
use function session;

class ProductManagerService implements ProductManagerServiceInterface
{
    /**
     * @param Product $product
     * @param string $message
     * @return Product
     */
    public function processRequestData(Product $product, string $message): Product
    {
        if (!empty(request('image'))) {
            $this->handleImageRequestData($product);
        }
        $product->description = "";
        if (!empty(request('description'))) {
            $product->description = request('description');
        }
        session()->flash('message', $message);
        $product->save();

        return $product;
    }

    /**
     * @param Product $product
     * @return \App\Models\File|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function handleImageRequestData(Product $product)
    {
        $imageName = time(). '.' . request('image')->extension();
        request('image')->move(public_path('uploads/images'), $imageName);
        $file = FileFactory::new([
            'path' => 'uploads/images/' . $imageName,
            'product_id' => $product->getAttributes()['id'],
            'size' => filesize(public_path("uploads/images/") . $imageName),
            'mime' => mime_content_type(public_path("uploads/images/") . $imageName)
        ])->create();

        return $file;
    }
}
