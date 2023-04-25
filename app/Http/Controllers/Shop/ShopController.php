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
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }

        return view('shop.index', [
            "products" => $products
        ]);
    }
}
