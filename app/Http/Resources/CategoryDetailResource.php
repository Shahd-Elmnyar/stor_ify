<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // 'sub_categories' => SubCategoryResource::collection($this->whenLoaded('subCategories') ),
            'products' => ProductResource::collection($this->whenLoaded('products') ),
        ];
    }
}
