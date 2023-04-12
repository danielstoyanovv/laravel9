<?php

namespace App\Http\Controllers\Shop;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShopController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        return view('shop.index', [
            "products" => Product::paginate(5)
        ]);
    }
}
