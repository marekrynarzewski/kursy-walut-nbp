<?php

namespace App\Http\Controllers;

use App\Exceptions\NBPApiException;
use App\Services\CurrencyService;
use App\Services\NBPApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\Events\JobExceptionOccurred;

class CurrencyController extends Controller
{
    public function index(string $code, NBPApi $api, CurrencyService $service): JsonResponse
    {
        try {
            [$name, $exchangeRate] = $api->getExchangeRate($code);
            $service->create($name, $code, $exchangeRate);
        } catch (NBPApiException $e) {
            return new JsonResponse([
                'error' => $e->getCode(),
            ], status: 404);
        }

        return new JsonResponse();
    }
}
