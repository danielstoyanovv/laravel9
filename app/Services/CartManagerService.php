<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Database\Factories\CartFactory;
use Database\Factories\CartItemFactory;

class CartManagerService
{
    /**
     * @param int $productId
     * @param float $cartTotal
     * @param int $qty
     * @param float $price
     * @param int|null $cartId
     * @return Cart|false|mixed
     */
    public function addToCart(int $productId, float $cartTotal, int $qty, float $price, int $cartId = null)
    {
        if ($product = Product::where('id', $productId)->first()) {
            $cart = $this->handleCartData($cartTotal, $cartId);
            $this->handleCartItemData($cart, $product, $qty, $price);

            return $cart;
        }

        return false;
    }

    /**
     * @param float $cartTotal
     * @param int|null $cartId
     * @return Cart|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    private function handleCartData(float $cartTotal, int $cartId = null)
    {
        if (!empty($cartId)) {
            if ($cart = Cart::where('id', $cartId)->first()) {
                return $cart;
            }
        }

        return CartFactory::new()->create();
    }

    /**
     * @param Cart $cart
     * @param Product $product
     * @param int $qty
     * @param float $price
     * @return mixed
     */
    private function handleCartItemData(Cart $cart, Product $product, int $qty, float $price)
    {
        foreach ($cart->getCartItem as $item) {
            if ($product->getAttributes()['id'] == $item->product->getAttributes()['id']) {
                $currentQty = $item->getAttributes()['qty'];
                $item->update([
                    'qty' => $currentQty + $qty
                ]);
                return $item;
            }
        }

        return CartItemFactory::new([
            'price' => $price,
            'product_id' => $product->getAttributes()['id'],
            'qty' => $qty,
            'cart_id' => $cart->getAttributes()['id']
        ])->create();
    }

    /**
     * @param CartItem $removeCartItem
     * @return void
     */
    public function removeFromCart(CartItem $removeCartItem): void
    {
        $removeCartItem->delete();
    }
}
