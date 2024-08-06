<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user_id' => $this->user_id,
            'total' => $this->total,
            'status' => $this->status,
            'delivery_date' => Carbon::parse($this->delivery_date)->format('Y-m-d'),
            'delivery_time' => Carbon::parse($this->delivery_time)->format('H:i'),
            ];
    }
}
