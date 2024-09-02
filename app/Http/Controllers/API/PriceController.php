<?php

namespace App\Http\Controllers\API;

use App\Exceptions\DistanceCalculatorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalculatePriceRequest;
use App\Http\Resources\DistanceCalculationResource;
use App\Models\Vehicle;
use App\Services\DistanceCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PriceController extends Controller
{
    /**
     * Calculate the distance between requested addresses.
     * @param CalculatePriceRequest $request
     * @return AnonymousResourceCollection
     * @throws DistanceCalculatorException
     */
    public function calculateDistance(CalculatePriceRequest $request)
    {
        $addresses = $request->validated()['addresses'];
        $calculator = new DistanceCalculatorService();
        foreach ($addresses as $address) {
            $calculator->addAddress($address['city'], $address['zip'], $address['country']);
        }
        $total_km = $calculator->calculate()->sum() / 1000;
        $vehicles = Vehicle::all()->map(function ($vehicle) use ($total_km) {
            $calculatedPrice = $vehicle->cost_km * $total_km;
            if ($calculatedPrice >= $vehicle->minimum) {
                $vehicle->calculatedPrice = $calculatedPrice;
                return $vehicle;
            }
            return null;
        })->filter();
        return DistanceCalculationResource::collection($vehicles);
    }
}
