<?php

namespace Tests\Unit\Services;

use App\Exceptions\NBPApiException;
use App\Services\NBPApi;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NBPApiTest extends TestCase
{
    private NBPApi $service;

    private Factory|MockObject $http;

    public function testGetExchangeRateFailure(): void
    {
        $currencyCode = '';
        $response = $this->createMock(Response::class);
        [$_, $method] = [$this->http, '__call'];
        $this->http->expects($this->once())
            ->method($method)
            ->willReturnCallback(function ($method, $parameters) use ($response) {
                if ('get' !== $method) {
                    return;
                }
                return $response;
            })
        ;
        $response->expects($this->once())
            ->method('successful')
            ->willReturn(false)
        ;
        $response->expects($this->once())
            ->method('reason')
            ->willReturn('Not found')
        ;
        $this->expectException(NBPApiException::class);
        $this->service->getExchangeRate($currencyCode);
    }

    public function testGetExchangeRateSuccess(): void
    {
        $currencyCode = '';
        $response = $this->createMock(Response::class);
        [$_, $method] = [Factory::class, '__call'];
        $this->http->expects($this->once())
            ->method($method)
            ->willReturnCallback(function ($method, $parameters) use ($response) {
                if ('get' !== $method) {
                    return;
                }
                return $response;
            })
        ;
        $response->expects($this->once())
            ->method('successful')
            ->willReturn(true)
        ;
        $response->expects($this->once())
            ->method('json')
            ->with('rates')
            ->willReturn([0 => ['mid' => 4.75]])
        ;

        $exchangeRate = $this->service->getExchangeRate($currencyCode);
        $this->assertEquals(475, $exchangeRate);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->http = $this->createMock(Factory::class);
        $this->service = new NBPApi(
            $this->http
        );
    }
}
