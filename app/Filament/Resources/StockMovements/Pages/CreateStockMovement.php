<?php

namespace App\Filament\Resources\StockMovements\Pages;

use App\Filament\Resources\StockMovements\StockMovementResource;
use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStockMovement extends CreateRecord
{
    protected static string $resource = StockMovementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['previous_quantity'] = Product::find($data['product_id'])->stock_quantity;
        
        // Calculate new quantity based on movement type
        $product = Product::find($data['product_id']);
        $previousQuantity = $product->stock_quantity;
        
        if (in_array($data['movement_type'], ['in', 'purchase'])) {
            $data['new_quantity'] = $previousQuantity + $data['quantity'];
        } else {
            $data['new_quantity'] = max(0, $previousQuantity - $data['quantity']);
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $data = $this->data;
        $product = Product::find($data['product_id']);
        
        // Update product stock
        $product->update(['stock_quantity' => $data['new_quantity']]);
    }
}
