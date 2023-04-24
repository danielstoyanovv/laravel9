<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Symfony\Component\HttpClient\CurlHttpClient;

class ElasticAddProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elastic:add:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add products in elastic search';


    public function __construct(private CurlHttpClient $client)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($products = Product::all()) {
            foreach ($products as $product) {
                $this->client->request(
                    "POST",
                    config('elasticsearch.url') . "products/" . urlencode($product['name']) .'/' . $product['id'],
                    [
                        "headers" => [
                            "Content-Type" => "application/json"

                        ],
                        "json" =>
                            [
                                'name' => $product['name'],
                                'description' => $product['description'],
                                'price' => $product['price']
                            ]
                    ]
                );
            }
        }

        return Command::SUCCESS;
    }
}
