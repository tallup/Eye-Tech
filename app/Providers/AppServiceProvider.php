<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sales;
use App\Models\Service;
use App\Models\Supplier;
use App\Policies\CategoryPolicy;
use App\Policies\ProductPolicy;
use App\Policies\SalePolicy;
use App\Policies\ServicePolicy;
use App\Policies\SupplierPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Sales::class, SalePolicy::class);
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Service::class, ServicePolicy::class);
    }
}
