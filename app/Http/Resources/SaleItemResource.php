<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'sale_id' => $this->sale_id,
            'product_id' => $this->product_id,
            'item_name' => $this->item_name,
            'item_sku' => $this->item_sku,
            'item_description' => $this->item_description,
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'discount_amount' => (float) $this->discount_amount,
            'total_price' => (float) $this->total_price,
        ];
    }
}
