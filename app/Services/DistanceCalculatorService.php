<?php

namespace App\Services;

use App\Exceptions\DistanceCalculatorException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class DistanceCalculatorService
{
    public function __construct(
        private array $addresses = [],
    )
    {
    }

    /**
     * Add an address to calculate the distance.
     * @param string $city
     * @param string $zip
     * @param string $country
     * @return void
     */
    public function addAddress(string $city, string $zip, string $country): void
    {
        $this->addresses[] = [
            'city' => $city,
            'zip' => $zip,
            'country' => $country,
        ];
    }

    /**
     * Get the addresses to calculate the distance.
     * @return array
     */
    public function getAddresses(): array
    {
        return $this->addresses;
    }

    /**
     * @return Collection
     * @throws DistanceCalculatorException
     */
    public function calculate()
    {
        $requestAddress = array_map(function ($address) {
            return "{$address['zip']} {$address['city']}, {$address['country']}";
        }, $this->addresses);

        $request = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => implode('|', $requestAddress),
            'destinations' => implode('|', $requestAddress),
            'key' => config('services.google.maps_key'),
            'units' => 'metric',
        ])->json();
        $status = $request['status'] ?? null;
        if ($status !== 'OK') {
            throw new DistanceCalculatorException($request['error_message'] ?? 'An error occurred while calculating the distance.');
        }
        $rows = $request['rows'];

        return collect($rows)
            ->flatMap(fn($row) => Arr::pluck($row['elements'], 'distance.value'))
            ->filter(fn($distance) => $distance !== 0);
    }
}
