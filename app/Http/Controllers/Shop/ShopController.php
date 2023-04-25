<?php

namespace App\Http\Controllers\Shop;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        try {
            $products = Product::paginate(5);

            if ($request->getMethod() == 'POST' && !empty($request->get('product'))) {
                $client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();
                $response = $client->search([
                    'index' => 'products',
                    'body'  => [
                        'query' => [
                            'multi_match' => [
                                'query' => $request->get('product'),
                                'fields' => [
                                    'name'
                                ]
                            ]
                        ]
                    ]
                ]);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }

        return view('shop.index', [
            "products" => $products
        ]);
    }
}
