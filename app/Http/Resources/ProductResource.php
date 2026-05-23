<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'selling_price' => (float) $this->selling_price,
            'cost_price' => (float) $this->cost_price,
            'stock_quantity' => $this->stock_quantity,
            'min_stock_level' => $this->min_stock_level,
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'brand' => $this->brand,
            'model' => $this->model,
            'image_url' => $this->image_url,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
