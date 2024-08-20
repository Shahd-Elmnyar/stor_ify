<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreHomeResource extends MainResource
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
            'name' => $this->name,
            'img' => url('uploads/' . $this->img),
            'categories' => new CategoryResource($this->whenLoaded('categories')),
            'total_products_ordered' => $this->products()->count(),
        ];
    }
}
