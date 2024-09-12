<?php

namespace App\Providers;

use App\Repositories\Contracts\ProductsRepositoryContract;
use App\Repositories\ProductsRepository;
use App\Services\Contracts\FileServiceContract;
use App\Services\FileService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        ProductsRepositoryContract::class => ProductsRepository::class,
        FileServiceContract::class => FileService::class,
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
    }
}
