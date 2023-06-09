<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Elastic\Elasticsearch\ClientBuilder;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

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
    protected $description = 'Add products in Elastic search';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            if ($products = Product::all()) {
                $client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();

                foreach ($products as $product) {
                    $params = [];
                    $params['body']  = [
                        'name' => $product['name'],
                        'description' => $product['description'],
                        'price' => $product['price']
                    ];
                    $params['index'] = 'products';
                    $params['id'] = $product['id'];
                    $params['type']  = 'products_Owner';
                    if ($client->exists(['id' => $product['id'], 'index' => 'products'])->getStatusCode() != '200') {
                        $client->create($params);
                    }
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }

        return Command::SUCCESS;
    }
}
