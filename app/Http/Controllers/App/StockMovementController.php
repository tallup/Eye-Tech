<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockMovementController extends Controller
{
    public function index(Request $request): Response
    {
        $query = StockMovement::with(['product', 'user'])->orderByDesc('created_at');

        if ($request->filled('type')) {
            $query->where('movement_type', $request->input('type'));
        }

        $movements = $query->paginate(30)->withQueryString();

        return Inertia::render('StockMovements/Index', [
            'movements' => $movements->through(fn ($m) => StockMovementResource::make($m)->resolve()),
            'filters' => $request->only(['type']),
        ]);
    }
}
