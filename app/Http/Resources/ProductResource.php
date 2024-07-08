<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'description' => $this->description,
            'price' => $this->price,
            'discount' => $this->discount,
            'price_after_discount' => $this->price - ($this->price * $this->discount / 100),
            // 'store' => new StoreResource($this->whenLoaded('store')),
            "store_img"=>url("uploads/".$this->store->img),
            // 'images'=> asset('uploads/'.$this->images->img),
            // 'sub_category' => new SubCategoryResource($this->whenLoaded('subCategory')),
            'product_images' => ImageResource::collection($this->whenLoaded('images')),
            // 'colors' => ColorResource::collection($this->whenLoaded('colors')),
            // 'sizes' => SizeResource::collection($this->whenLoaded('sizes')),
            'is_favorited' => $this->isFavoritedByUser($request->user()),
        ];
    }

    /**
     * Check if the product is favorited by the given user.
     *
     * @param \App\Models\User|null $user
     * @return bool
     */
    protected function isFavoritedByUser($user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}
