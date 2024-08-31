<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryDetailResource extends MainResource
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
            'description' => $this->description,
            'img' => url("uploads/" . $this->img),
            // 'sub_categories' => SubCategoryResource::collection($this->whenLoaded('subCategories')),
            // 'products' =>  ProductResource::collection($this->whenLoaded('products')),
            'products' => isset($data['products']) ? ProductResource::collection($this->whenLoaded('products')) : null,
        ];
    }
}
