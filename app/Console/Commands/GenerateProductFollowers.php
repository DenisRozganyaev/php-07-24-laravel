<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateProductFollowers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:product-followers {productId} {--count= : Count of followers}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate product followers by product id and follow type';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productId = $this->argument('productId');
        $count = $this->option('count') ?? 500;
        $type = [
            'price',
            'in_stock'
        ];
        $typeStep = 0;
        $product = Product::findOrFail($productId);
        $step = 1;

        $users = User::factory($count)
            ->create()
            ->pluck('id')
            ->chunk(100);

        $this->withProgressBar($users, function($users) use ($product, $type, &$step) {
            $data = [];
            $this->newLine();
            foreach($users as $id) {
                $data[$id] = [$type[rand(0, 1)] => true];

                $this->info("[Step: $step] Generate data for user $id..");
                $step++;
            }
            $product->followers()->syncWithoutDetaching($data);
        });

    }
}
