<?php

namespace App\Services;

use App\Abstraction\ModelSaverInterface;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Currency createModel()
 */
class CurrencyService extends AbstractService
{
    protected Model|Currency $model;

    public function __construct(ModelSaverInterface $modelSaver, Currency $model)
    {
        parent::__construct($modelSaver, $model);
    }

    public function create(string $name, string $currencyCode, int $exchangeRate): Currency
    {
        $currency = $this->model->newQuery()->where('currencyCode', $currencyCode)->get()->first();
        if (null === $currency) {
            $currency = $this->createModel();
            $currency->name = $name;
            $currency->currencyCode = $currencyCode;
            $currency->exchangeRate = $exchangeRate;
        } else {
            $this->update($currency, $exchangeRate);
        }
        $this->save($currency);

        return $currency;
    }

    public function update(Currency $currency, int $exchangeRate): void
    {
        $currency->exchangeRate = $exchangeRate;
        $this->save($currency);
    }
}
