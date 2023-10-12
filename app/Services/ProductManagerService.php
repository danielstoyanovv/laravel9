<?php

namespace App\Services;

use App\Models\Product;
use Database\Factories\FileFactory;
use Illuminate\Http\Request;
use function public_path;
use function session;

class ProductManagerService
{
    /**
     * process validate
     *
     * @param Request $request
     * @return array
     */
    public function validateRequestData(Request $request): array
    {
        return $request->validate(
            [
                'name' => 'required',
                'price' => 'required',
                'image' => 'mimes:jpg,bmp,png,gif,jpeg,webp|max:5000'
            ]
        );
    }

    /**
     * @param array $validated
     * @param Request $request
     * @param Product $product
     * @param string $message
     * @return Product
     */
    public function processRequestData(Request $request, Product $product, string $message): Product
    {
        if (!empty($request->image)) {
            $this->handleImageRequestData($request, $product);
        }
        $product->description = "";
        if (!empty($request->get('description'))) {
            $product->description = $request->get('description');
        }
        session()->flash('message', $message);
        $product->save();

        return $product;
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return \App\Models\File|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    private function handleImageRequestData(Request $request, Product $product)
    {
        $imageName = time(). '.' . $request->image->extension();
        $request->image->move(public_path('uploads/images'), $imageName);
        $file = FileFactory::new([
            'path' => 'uploads/images/' . $imageName,
            'product_id' => $product->getAttributes()['id'],
            'size' => filesize(public_path("uploads/images/") . $imageName),
            'mime' => mime_content_type(public_path("uploads/images/") . $imageName)
        ])->create();

        return $file;
    }
}
