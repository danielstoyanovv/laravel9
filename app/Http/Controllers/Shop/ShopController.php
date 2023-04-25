<?php

namespace App\Http\Controllers\Shop;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpClient\CurlHttpClient;

class ShopController extends Controller
{
    public function __construct(private CurlHttpClient $client)
    {
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        try {
            $products = Product::paginate(5);

            if ($request->getMethod() == 'POST' && !empty($request->get('product'))) {
                $searchResponseJson = $this->client->request(
                    "GET",
                    config('elasticsearch.url') . "/products",
                    [
                        "headers" => [
                            "Content-Type" => "application/json"
                        ],
                        "query" => [
                            "match" => [
                                "name" => $request->get('product')
                            ]
                        ]
                    ]
                );
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }

        return view('shop.index', [
            "products" => $products
        ]);
    }
}
