<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\CartManagerServiceInterface;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Database\Factories\CartFactory;
use Database\Factories\CartItemFactory;

class CartManagerService implements CartManagerServiceInterface
{
    public $productId;
    public $qty;
    public $price;

    /**
     * @param int $productId
     * @return $this
     */
    public function setProductId(int $productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @param int $qty
     * @return $this
     */
    public function setQty(int $qty): self
    {
        $this->qty = $qty;
        return $this;
    }

    /**
     * @param float $price
     * @return $this
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @param int|null $cartId
     * @return Cart|false|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function addToCart(int $cartId = null)
    {
        if ($product = Product::find($this->productId)) {
            $cart = $this->handleCartData($cartId);
            $this->handleCartItemData($cart, $product);

            return $cart;
        }

        return false;
    }

    /**
     * @param int|null $cartId
     * @return Cart|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function handleCartData(int $cartId = null)
    {
        if (!empty($cartId)) {
            if ($cart = Cart::find($cartId)) {
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
    public function handleCartItemData(Cart $cart, Product $product)
    {
        foreach ($cart->getCartItem as $item) {
            if ($product->id === $item->product->id) {
                $currentQty = $item->qty;
                $item->update([
                    'qty' => $currentQty + $this->qty
                ]);
                return $item;
            }
        }

        return CartItemFactory::new([
            'price' => $this->price,
            'product_id' => $product->id,
            'qty' => $this->qty,
            'cart_id' => $cart->id
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
