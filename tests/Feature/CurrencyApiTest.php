<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CurrencyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_currency_payload_when_sync_succeeds(): void
    {
        Http::fake([
            'api.nbp.pl/*' => Http::response([
                'table' => 'A',
                'currency' => 'euro',
                'code' => 'EUR',
                'rates' => [
                    [
                        'effectiveDate' => '2026-04-24',
                        'mid' => 4.27,
                    ],
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/currency/eur');

        $response->assertOk()
            ->assertJsonPath('data.currencyCode', 'EUR')
            ->assertJsonPath('data.exchangeRate', 427)
            ->assertJsonPath('data.exchangeRateDecimal', 4.27)
            ->assertJsonPath('meta.status', 'created')
            ->assertJsonPath('meta.source', 'NBP API');

        $this->assertDatabaseHas('currency', [
            'currencyCode' => 'EUR',
            'name' => 'euro',
            'exchangeRate' => 427,
        ]);
    }

    public function test_it_returns_not_found_payload_when_nbp_has_no_currency(): void
    {
        Http::fake([
            'api.nbp.pl/*' => Http::response([], 404),
        ]);

        $response = $this->getJson('/api/currency/zzz');

        $response->assertNotFound()
            ->assertJsonPath('error.code', 'NBP_NOT_FOUND')
            ->assertJsonPath('meta.documentationUrl', '/api/documentation');
    }
}
