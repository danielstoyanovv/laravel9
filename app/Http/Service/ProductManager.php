<?php

namespace App\Http\Service;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductManager
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
            $fileName = time(). '.' . $request->image->extension();
            $request->image->move(public_path('uploads/images'), $fileName);
        }
        $product->description = "";
        if (!empty($request->get('description'))) {
            $product->description = $request->get('description');
        }
        session()->flash('message', $message);
        $product->save();

        return $product;
    }
}
