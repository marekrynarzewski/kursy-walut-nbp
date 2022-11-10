<?php

namespace Tests\Unit\Services;

use App\Models\Currency;
use App\Services\CurrencyService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\MockObject\MockObject;

class CurrencyServiceTest extends AbstractServiceTest
{
    private CurrencyService $service;

    protected Currency|MockObject|Model $model;

    public function testCreateCurrencyNotFound(): void
    {
        $name = '';
        $currencyCode = '';
        $exchangeRate = 0;
        $query = $this->createMock(Builder::class);
        [, $method] = [Currency::class, 'newQuery'];
        $this->model->expects($this->once())
            ->method($method)
            ->willReturn($query)
        ;
        [, $method] = [Builder::class, 'where'];
        $query->expects($this->once())
            ->method($method)
            ->with('currencyCode', $currencyCode)
            ->willReturn($query)
        ;

        [, $method] = [Builder::class, 'get'];
        $collection = $this->createMock(Collection::class);
        $query->expects($this->once())
            ->method($method)
            ->willReturn($collection)
        ;
        [, $method] = [Collection::class, 'first'];
        $collection->expects($this->once())
            ->method($method)
            ->willReturn(null)
        ;
        $response = $this->service->create($name, $currencyCode, $exchangeRate);
        $this->assertInstanceOf(Currency::class, $response);
    }

    public function testCreateCurrencyFound(): void
    {
        $this->markTestIncomplete();
        $name = '';
        $currencyCode = '';
        $exchangeRate = 0;
        $this->service->create($name, $currencyCode, $exchangeRate);
    }

    public function testUpdate(): void
    {
        $this->markTestIncomplete();
        $this->service->update();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $modelSaver = $this->getParams();
        $this->service = new CurrencyService(
            $modelSaver,
            $this->model,
        );
    }

    protected function createModel(): Model|Currency
    {
        return new Currency();
    }

    protected function mockModel(): Model|MockObject|Currency
    {
        return  $this->createMock(Currency::class);
    }
}
