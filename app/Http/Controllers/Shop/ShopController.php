<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function index(Request $request)
    {
        try {
            if ($request->getMethod() == 'POST' && !empty($request->get('product'))) {
                $searchResponseJson = $this->client->request(
                    "GET",
                    config('elasticsearch.url') . "products/_search?pretty",
                    [
                        "headers" => [
                            "Content-Type" => "application/json"
                        ],
                        'json'  => [
                            'query' => [
                                'match' => [
                                    'name' => $request->get('product')
                                ]
                            ]
                        ]
                    ]
                );
                $result = json_decode($searchResponseJson->getContent(), true);
                if (!empty($result['hits']['hits'][0]['_source']) && !empty($result['hits']['hits'][0]['_id'])) {
                    $products = [];
                    $products[] = array_merge(
                        $result['hits']['hits'][0]['_source'],
                        ['id' => $result['hits']['hits'][0]['_id']]
                    );

                    return view('shop.search_result', [
                        "products" => $products
                    ]);
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }

        return view('shop.index', [
            "products" => Product::paginate(5)
        ]);
    }
}
