<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Sales extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'payment_method',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (empty($sale->sale_number)) {
                $sale->sale_number = 'SALE-'.date('Ymd').'-'.strtoupper(Str::random(6));
            }
        });

        static::saved(function ($sale) {
            // Recalculate totals when sale is saved
            $sale->calculateTotals();
        });

        static::creating(function ($sale) {
            // Set default values for required fields
            $sale->subtotal = $sale->subtotal ?? 0;
            $sale->total_amount = $sale->total_amount ?? 0;
        });
    }

    public function calculateTotals()
    {
        $itemCount = $this->salesItems()->count();
        if ($itemCount === 0) {
            return;
        }

        $subtotal = $this->salesItems()->sum('total_price');
        $discount = $this->discount_amount ?? 0;
        $total = $subtotal - $discount;

        $this->updateQuietly([
            'subtotal' => $subtotal,
            'total_amount' => $total,
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function salesItems(): HasMany
    {
        return $this->hasMany(SalesItem::class, 'sale_id');
    }

    /**
     * Alias used by POS controllers and API resources.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SalesItem::class, 'sale_id');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'info',
            default => 'secondary',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Cash',
            'card' => 'Card',
            'mobile_money' => 'Mobile Money',
            'bank_transfer' => 'Bank Transfer',
            default => 'Unknown',
        };
    }
}
