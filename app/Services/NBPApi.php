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

        return [$name, $exchangeRate];
    }
}
