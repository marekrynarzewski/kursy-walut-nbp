<?php

namespace App\Services;

use App\Models\Currency;

class CurrencyService
{
    public function create(string $name, string $currencyCode, int $exchangeRate): Currency
    {
        $currency = Currency::where('currencyCode', $currencyCode)->get()->first();
        if (null === $currency) {
            $currency = new Currency();
            $currency->name = $name;
            $currency->currencyCode = $currencyCode;
            $currency->exchangeRate = $exchangeRate;
        } else {
            $this->update($currency, $exchangeRate);
        }
        $currency->save();

        return $currency;
    }

    public function update(Currency $currency, int $exchangeRate): void
    {
        $currency->exchangeRate = $exchangeRate;
        $currency->save();
    }
}
