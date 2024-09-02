<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $vehicle_type Number of the vehicle
 * @property float $price Price of calculation
 */
class DistanceCalculationResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'vehicle_type' => $this->number,
            'price' => round($this->calculatedPrice, 2),
        ];
    }
}
