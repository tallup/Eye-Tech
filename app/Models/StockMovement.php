<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'movement_type',
        'quantity',
        'previous_quantity',
        'new_quantity',
        'reference_type',
        'reference_id',
        'user_id',
        'notes',
        'unit_cost',
        'total_cost',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMovementTypeLabelAttribute(): string
    {
        return match ($this->movement_type) {
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'adjustment' => 'Adjustment',
            'transfer' => 'Transfer',
            'sale' => 'Sale',
            'purchase' => 'Purchase',
            default => 'Unknown'
        };
    }

    public function getMovementTypeColorAttribute(): string
    {
        return match ($this->movement_type) {
            'in', 'purchase' => 'success',
            'out', 'sale' => 'danger',
            'adjustment' => 'warning',
            'transfer' => 'info',
            default => 'gray'
        };
    }

    public function getQuantityDisplayAttribute(): string
    {
        $prefix = in_array($this->movement_type, ['in', 'purchase']) ? '+' : '-';
        return $prefix . abs($this->quantity);
    }
}
