<?php

namespace App\Http\Controllers\App;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutSaleRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
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

    public function checkout(CheckoutSaleRequest $request, CheckoutService $service): RedirectResponse
    {
        try {
            $sale = $service->checkout(
                user: $request->user(),
                items: $request->input('items'),
                customer: [
                    'name' => $request->input('customer_name'),
                    'phone' => $request->input('customer_phone'),
                    'email' => $request->input('customer_email'),
                ],
                paymentMethod: $request->input('payment_method'),
            );
        } catch (InsufficientStockException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('sales.show', $sale)
            ->with('print', true);
    }
}
