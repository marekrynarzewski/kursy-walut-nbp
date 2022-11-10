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
    public function getExchangeRate(string $currencyCode): float
    {
        $url = sprintf(self::URL, $currencyCode);
        $response = $this->http->get($url, [
            'format' => 'json',
        ]);
        if (!$response->successful()) {
            throw new NBPApiException($response->reason());
        }
        $rates = $response->json('rates');
        $rate = $rates[0];

        return round($rate['mid']*100);
    }
}
