<?php

namespace App\Interfaces;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

interface CartManagerServiceInterface
{
    /**
     * @param int $productId
     * @return $this
     */
    public function setProductId(int $productId): \App\Services\CartManagerService;

    /**
     * @param int $qty
     * @return $this
     */
    public function setQty(int $qty): \App\Services\CartManagerService;

    /**
     * @param float $price
     * @return $this
     */
    public function setPrice(float $price): \App\Services\CartManagerService;

    /**
     * @param int|null $cartId
     * @return Cart|false|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function addToCart(int $cartId = null);

    /**
     * @param int|null $cartId
     * @return Cart|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function handleCartData(int $cartId = null);

    /**
     * @param Cart $cart
     * @param Product $product
     * @param int $qty
     * @param float $price
     * @return mixed
     */
    public function handleCartItemData(Cart $cart, Product $product);

    /**
     * @param CartItem $removeCartItem
     * @return void
     */
    public function removeFromCart(CartItem $removeCartItem): void;
}
