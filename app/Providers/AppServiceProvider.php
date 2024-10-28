<?php

namespace App\Providers;

use App\Models\Product;
use App\Policies\Api\v1\ProductPolicy;
use App\Repositories\Contracts\ImagesRepositoryContract;
use App\Repositories\Contracts\OrderRepositoryContract;
use App\Repositories\Contracts\ProductsRepositoryContract;
use App\Repositories\ImagesRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductsRepository;
use App\Services\Contracts\FileServiceContract;
use App\Services\Contracts\InvoicesServiceContract;
use App\Services\Contracts\PaypalServiceContract;
use App\Services\FileService;
use App\Services\InvoicesService;
use App\Services\PaypalService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        ProductsRepositoryContract::class => ProductsRepository::class,
        FileServiceContract::class => FileService::class,
        ImagesRepositoryContract::class => ImagesRepository::class,
        OrderRepositoryContract::class => OrderRepository::class,
        PaypalServiceContract::class => PaypalService::class,
        InvoicesServiceContract::class => InvoicesService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
//        app()->bind(ProductsRepositoryContract::class, ProductsRepository::class);
//        app()->when(ProductsController::class)
//            ->needs(ProductsRepositoryContract::class)
//            ->give(ProductsRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::policy(Product::class, ProductPolicy::class);
    }
}
