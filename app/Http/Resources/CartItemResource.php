<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends MainResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    protected function transformData(array $data): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'price' => $this->price,
            'cart_id' => $this->cart_id,
            'size' => new SizeResource($this->size),
            'color' => new ColorResource($this->color),
            'product' => new ProductResource($this->product),
        ];
    }
}
