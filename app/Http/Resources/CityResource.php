<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CityResource
 * @package App\Http\Resources
 * @property string $id The id of the city.
 * @property string $name The name of the city.
 * @property string $zipCode The zip code of the city.
 * @property string $country The country of the city.
 */
class CityResource extends JsonResource
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
            'zip' => $this->zipCode,
            'country' => $this->country,
        ];
    }
}
