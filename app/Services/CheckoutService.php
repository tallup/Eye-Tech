<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    /**
     * @param  array{name: ?string, phone: ?string, email: ?string}  $customer
     * @param  array<int, array{product_id: int, quantity: int}>  $items
     */
    public function checkout(User $user, array $items, array $customer, string $paymentMethod): Sales
    {
        return DB::transaction(function () use ($user, $items, $customer, $paymentMethod) {
            $subtotal = 0;
            $resolved = [];

            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    throw new InsufficientStockException(
                        $product->id,
                        $product->stock_quantity,
                        $item['quantity'],
                    );
                }

                $lineTotal = (float) $product->selling_price * $item['quantity'];
                $subtotal += $lineTotal;

                $resolved[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price' => (float) $product->selling_price,
                    'line_total' => $lineTotal,
                ];
            }

            $sale = Sales::create([
                'customer_name' => $customer['name'],
                'customer_phone' => $customer['phone'],
                'customer_email' => $customer['email'],
                'payment_method' => $paymentMethod,
                'status' => 'completed',
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $subtotal,
                'user_id' => $user->id,
            ]);

            foreach ($resolved as $r) {
                $product = $r['product'];

                // Bypass model hooks to prevent double stock decrement.
                SalesItem::withoutEvents(function () use ($sale, $product, $r) {
                    SalesItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'item_name' => $product->name,
                        'item_sku' => $product->sku,
                        'item_description' => $product->description,
                        'quantity' => $r['quantity'],
                        'unit_price' => $r['unit_price'],
                        'discount_amount' => 0,
                        'total_price' => $r['line_total'],
                    ]);
                });

                $previousQuantity = $product->stock_quantity;
                $newQuantity = $previousQuantity - $r['quantity'];

                $product->update(['stock_quantity' => $newQuantity]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'movement_type' => 'sale',
                    'quantity' => $r['quantity'],
                    'previous_quantity' => $previousQuantity,
                    'new_quantity' => $newQuantity,
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'user_id' => $user->id,
                ]);
            }

            // Recalculate totals now that all items exist, then refresh in-memory state.
            $sale->calculateTotals();

            return $sale->refresh()->load(['items', 'user']);
        });
    }
}
