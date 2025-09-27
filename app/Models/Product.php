<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'description',
        'category_id',
        'supplier_id',
        'cost_price',
        'selling_price',
        'stock_quantity',
        'min_stock_level',
        'brand',
        'model',
        'specifications',
        'image',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'specifications' => 'array',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock_quantity == 0) {
            return 'out_of_stock';
        }
        
        if ($this->stock_quantity <= $this->min_stock_level) {
            return 'low_stock';
        }
        
        if ($this->stock_quantity <= $this->min_stock_level * 1.5) {
            return 'warning';
        }
        
        return 'in_stock';
    }

    public function getStockValueAttribute(): float
    {
        return $this->stock_quantity * $this->cost_price;
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->cost_price == 0) {
            return 0;
        }
        
        return (($this->selling_price - $this->cost_price) / $this->cost_price) * 100;
    }

    public function getTotalInvestmentAttribute(): float
    {
        return $this->stock_quantity * $this->cost_price;
    }

    public function getPotentialProfitAttribute(): float
    {
        return $this->stock_quantity * ($this->selling_price - $this->cost_price);
    }
}
