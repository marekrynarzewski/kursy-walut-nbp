<?php

namespace Tests\Feature\Services;

use App\Exceptions\NBPApiException;
use App\Models\Currency;
use App\Services\CurrencyService;
use App\Services\NBPApi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    use RefreshDatabase;

    private CurrencyService $service;

    public function testCreateCurrencyNotFound(): void
    {
        DB::table('currency')->truncate();
        $name = 'Eurosd';
        $currencyCode = 'EURO';
        $exchangeRate = 475;
        $this->service->create($name, $currencyCode, $exchangeRate);
        $this->assertDatabaseCount('currency', 1);
        $this->assertDatabaseHas('currency', [
            'currencyCode' => 'EUR',
        ]);
    }

    public function testCreateCurrencyFound(): void
    {
        $this->markTestIncomplete();
        $this->service->create();
    }

    public function testUpdate(): void
    {
        $this->markTestIncomplete();
        $this->service->update();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $app = $this->createApplication();
        $this->service = $app->make(CurrencyService::class);
    }
}
