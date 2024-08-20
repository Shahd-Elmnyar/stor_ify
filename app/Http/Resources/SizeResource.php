<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SizeResource extends MainResource
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
            'size' => $this->size,
        ];
    }
}
