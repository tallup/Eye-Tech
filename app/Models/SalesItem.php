<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'item_name',
        'item_sku',
        'item_description',
        'quantity',
        'unit_price',
        'discount_amount',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($salesItem) {
            // Set default values for required fields
            $salesItem->unit_price = $salesItem->unit_price ?? 0;
            $salesItem->discount_amount = $salesItem->discount_amount ?? 0;
            $salesItem->quantity = $salesItem->quantity ?? 1;
        });

        static::saving(function ($salesItem) {
            // Ensure unit_price is not null
            if (is_null($salesItem->unit_price)) {
                $salesItem->unit_price = 0;
            }

            // Ensure discount_amount is not null
            if (is_null($salesItem->discount_amount)) {
                $salesItem->discount_amount = 0;
            }

            $salesItem->total_price = ($salesItem->quantity * $salesItem->unit_price) - $salesItem->discount_amount;
        });

        static::saved(function ($salesItem) {
            // Update product stock when sales item is saved
            if ($salesItem->product) {
                $previousQuantity = $salesItem->product->stock_quantity;
                $salesItem->product->decrement('stock_quantity', $salesItem->quantity);

                // Record stock movement
                \App\Models\StockMovement::create([
                    'product_id' => $salesItem->product_id,
                    'movement_type' => 'sale',
                    'quantity' => $salesItem->quantity,
                    'previous_quantity' => $previousQuantity,
                    'new_quantity' => $previousQuantity - $salesItem->quantity,
                    'reference_type' => 'sale',
                    'reference_id' => $salesItem->sale_id,
                    'user_id' => auth()->id() ?? 1, // Fallback to admin user
                    'notes' => 'Stock reduced due to sale',
                    'unit_cost' => $salesItem->product->cost_price,
                    'total_cost' => $salesItem->quantity * $salesItem->product->cost_price,
                ]);
            }
        });

        static::deleted(function ($salesItem) {
            // Restore product stock when sales item is deleted
            if ($salesItem->product) {
                $previousQuantity = $salesItem->product->stock_quantity;
                $salesItem->product->increment('stock_quantity', $salesItem->quantity);

                // Record stock movement for restoration
                \App\Models\StockMovement::create([
                    'product_id' => $salesItem->product_id,
                    'movement_type' => 'adjustment',
                    'quantity' => $salesItem->quantity,
                    'previous_quantity' => $previousQuantity,
                    'new_quantity' => $previousQuantity + $salesItem->quantity,
                    'reference_type' => 'sale',
                    'reference_id' => $salesItem->sale_id,
                    'user_id' => auth()->id() ?? 1, // Fallback to admin user
                    'notes' => 'Stock restored due to sale deletion',
                    'unit_cost' => $salesItem->product->cost_price,
                    'total_cost' => $salesItem->quantity * $salesItem->product->cost_price,
                ]);
            }
        });
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'sale_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
