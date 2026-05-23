<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class PosController extends Controller
{
    public function index(): Response
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        return Inertia::render('POS/Index', [
            'products' => ProductResource::collection($products),
            'categories' => CategoryResource::collection($categories),
        ]);
    }
}
