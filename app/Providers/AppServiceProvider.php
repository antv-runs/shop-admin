<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\UserServiceInterface;
use App\Contracts\ProductServiceInterface;
use App\Contracts\CategoryServiceInterface;
use App\Contracts\ProfileServiceInterface;
use App\Contracts\AuthServiceInterface;
use App\Contracts\FileUploadServiceInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Services\UserService;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\ProfileService;
use App\Services\AuthService;
use App\Services\FileUploadService;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\UserRepository;
use App\Repositories\OrderRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Bind Repository Interfaces to Implementations
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);

        // Bind Service Interfaces to Implementations
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(ProfileServiceInterface::class, ProfileService::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(FileUploadServiceInterface::class, FileUploadService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
