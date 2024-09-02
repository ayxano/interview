<?php

namespace App\Http\Requests;

use App\Models\City;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CalculatePriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'addresses' => 'required|array|min:2',
            'addresses.*.country' => [
                'required',
                'string',
                'size:2',
                Rule::exists(City::class, 'country'),
            ],
            'addresses.*.zip' => [
                'required',
                'string',
                'size:5',
                Rule::exists(City::class, 'zipCode'),
            ],
            'addresses.*.city' => [
                'required',
                'string',
                Rule::exists(City::class, 'name'),
            ],
        ];
    }
}
