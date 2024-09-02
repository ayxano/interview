<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiKeyTest extends TestCase
{
    #[Test]
    public function it_blocks_requests_without_api_key()
    {
        $response = $this->postJson(route('calculatePrice'), [
            'addresses' => [
                ['country' => 'DE', 'zip' => '10115', 'city' => 'Berlin'],
                ['country' => 'DE', 'zip' => '20095', 'city' => 'Hamburg']
            ]
        ]);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthorized']);
    }

    #[Test]
    public function it_denies_requests_with_one_address()
    {
        $response = $this->withHeaders(['API_KEY' => config('app.api_key')])
            ->postJson(route('calculatePrice'), [
                'addresses' => [
                    ['country' => 'DE', 'zip' => '10115', 'city' => 'Berlin'],
                ]
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['addresses']);
    }

    #[Test]
    public function it_denies_requests_with_wrong_keys()
    {
        $response = $this->withHeaders(['API_KEY' => config('app.api_key')])
            ->postJson(route('calculatePrice'), [
                'addresses' => [
                    ['country1' => 'DE', 'zip' => '10115', 'city' => 'Berlin'],
                    ['country' => 'DE', 'zip2' => '20095', 'city' => 'Hamburg'],
                    ['country' => 'DE', 'zip' => '20095', 'city3' => 'Hamburg']
                ]
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['addresses.0.country', 'addresses.1.zip', 'addresses.2.city']);
    }

    #[Test]
    public function it_denies_requests_with_wrong_values()
    {
        $response = $this->withHeaders(['API_KEY' => config('app.api_key')])
            ->postJson(route('calculatePrice'), [
                'addresses' => [
                    ['country' => 'AZ', 'zip' => '10115', 'city' => 'Berlin'],
                    ['country' => 'DE', 'zip' => '01068', 'city' => 'Hamburg'],
                    ['country' => 'DE', 'zip' => '20095', 'city' => 'Baku']
                ]
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'addresses.0.country', // Should be invalid as 'AZ' might not be accepted
            'addresses.1.zip',     // Missing zip code for the second address
            'addresses.2.city'     // Missing city for the third address
        ]);
    }

    #[Test]
    public function it_allows_requests_with_correct_api_key()
    {
        $response = $this->withHeaders(['API_KEY' => config('app.api_key')])
            ->postJson(route('calculatePrice'), [
                'addresses' => [
                    ['country' => 'DE', 'zip' => '10115', 'city' => 'Berlin'],
                    ['country' => 'DE', 'zip' => '20095', 'city' => 'Hamburg']
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'vehicle_type',
                'price',
            ]
        ]);
        foreach ($response->json() as $item) {
            $this->assertIsInt($item['vehicle_type']);
            $this->assertIsFloat($item['price']);
        }
    }
}
