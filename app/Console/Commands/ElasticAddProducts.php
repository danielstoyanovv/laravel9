<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Elastic\Elasticsearch\ClientBuilder;
use App\Models\Product;

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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($products = Product::all()) {
            $client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();

            foreach ($products as $product) {
                $params = array();
                $params['body']  = array(
                    'name' => $product['name'], 											//preparing structred data
                    'description' => $product['description'],
                    'price' => $product['price']

                );
                $params['index'] = 'products';
                $params['type']  = 'products_Owner';
                $result = $client->index($params);							//using Index() function to inject the data
            }
        }

        return Command::SUCCESS;
    }
}
