<?php

namespace App\Services;

use App\Exceptions\NBPApiException;
use Illuminate\Http\Client\Factory;

class NBPApi
{
    private const URL = 'http://api.nbp.pl/api/exchangerates/rates/A/%s/';

    public function __construct(private Factory $http)
    {
    }

    /**
     * @throws NBPApiException
     */
    public function getExchangeRate(string $currencyCode): array
    {
        $details = $this->getExchangeRateDetails($currencyCode);

        return [$details['name'], $details['exchangeRate']];
    }

    /**
     * @return array{
     *     name: string,
     *     code: string,
     *     table: string,
     *     exchangeRate: int,
     *     exchangeRateDecimal: float,
     *     effectiveDate: string|null
     * }
     *
     * @throws NBPApiException
     */
    public function getExchangeRateDetails(string $currencyCode): array
    {
        $url = sprintf(self::URL, $currencyCode);
        $response = $this->http->get($url, [
            'format' => 'json',
        ]);
        if (!$response->successful()) {
            throw new NBPApiException($response->reason());
        }
        $name = $response->json('currency');
        $rates = $response->json('rates');
        $rate = $rates[0];
        $exchangeRate = round($rate['mid']*100);

        return [
            'name' => $name,
            'code' => strtoupper((string) $response->json('code', $currencyCode)),
            'table' => (string) $response->json('table', 'A'),
            'exchangeRate' => $exchangeRate,
            'exchangeRateDecimal' => (float) $rate['mid'],
            'effectiveDate' => $rate['effectiveDate'] ?? null,
        ];
    }
}
