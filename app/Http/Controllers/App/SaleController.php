<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\SaleResource;
use App\Models\Sales;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SaleController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Sales::with(['user', 'items.product'])->orderByDesc('created_at');

        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        $sales = $query->paginate(20)->withQueryString();

        return Inertia::render('Sales/Index', [
            'sales' => $sales->through(fn ($s) => SaleResource::make($s)->resolve()),
        ]);
    }

    public function show(Sales $sale): Response
    {
        $this->authorize('view', $sale);
        $sale->load(['items.product', 'user']);

        return Inertia::render('Sales/Show', [
            'sale' => SaleResource::make($sale),
        ]);
    }
}
