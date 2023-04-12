<?php

namespace App\Http\Controllers\Shop;

use App\Http\Service\CartManager;
use App\Models\CartItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        return view('cart.index', [
            'cart' => $request->getSession()->get('cart_id') ? Cart::where('id', $request->getSession()->get('cart_id'))->first() : null
        ]);
    }

    /**
     * @param Request $request
     * @param CartManager $cartManager
     * @return RedirectResponse
     */
    public function addToCart(Request $request, CartManager $cartManager): RedirectResponse
    {
        try {
            DB::beginTransaction();

            if ($request->getMethod() == 'POST') {
                if (!empty($request->get('product')) && !empty($request->get('price')) && !empty($request->get('qty'))) {
                    $productId = $request->get('product');
                    $price = $request->get('price');
                    $qty = $request->get('qty');
                    $cartTotal = $price * $qty;
                    if ($cart = $cartManager->addToCart($productId, $cartTotal, $qty, $price, $request->getSession()->get('cart_id'))) {
                        $request->getSession()->set('cart_id', $cart->getAttributes()['id']);
                    }

                    DB::commit();
                    session()->flash('message', __('Product was added in cart'));
                }
            }
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());
        }

        return redirect()->route('cart');
    }

    /**
     * @param Request $request
     * @param CartManager $cartManager
     * @return RedirectResponse
     */
    public function removeFromCart(Request $request, CartManager $cartManager): RedirectResponse
    {
        try {
            DB::beginTransaction();

            if ($request->getMethod() == 'POST') {
                if (!empty($request->get('cart_item_id'))) {
                    if ($removeCartItem = CartItem::where('id', $request->get('cart_item_id'))->first()) {
                        if ($removeCartItem->cart->getAttributes()['id'] != $request->getSession()->get('cart_id')) {
                            throw new \Exception(sprintf(
                                'Product name : %s can\'t be removed',
                                $removeCartItem->product->getAttributes()['name']
                            ));
                        }
                        $cartManager->removeFromCart($removeCartItem);
                        DB::commit();
                    }
                    session()->flash('message', __('Product was removed from cart'));
                }
            }
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());
        }

        return redirect()->route('cart');
    }
}
