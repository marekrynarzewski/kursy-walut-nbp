<?php

namespace App\Http\Controllers;

use App\Exceptions\NBPApiException;
use App\Models\Currency;
use App\Services\CurrencyService;
use App\Services\NBPApi;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CurrencyController extends Controller
{
    #[OA\Get(
        path: '/api/currency/{code}',
        operationId: 'syncCurrencyRate',
        tags: ['Currencies'],
        summary: 'Pobierz kurs waluty z API NBP i zapisz go lokalnie',
        description: 'Endpoint synchronizuje kurs waluty z tabeli A NBP. Jesli rekord juz istnieje, kurs zostanie zaktualizowany.',
        parameters: [
            new OA\Parameter(
                name: 'code',
                description: 'Trzyliterowy kod waluty zgodny z ISO 4217, np. EUR albo USD.',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', pattern: '^[A-Za-z]{3}$'),
                example: 'EUR'
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Waluta zostala zsynchronizowana',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '8f3b7f43-2dc9-4f89-9244-0d89f8ed91f4'),
                                new OA\Property(property: 'name', type: 'string', example: 'euro'),
                                new OA\Property(property: 'currencyCode', type: 'string', example: 'EUR'),
                                new OA\Property(property: 'exchangeRate', type: 'integer', example: 427),
                                new OA\Property(property: 'exchangeRateDecimal', type: 'number', format: 'float', example: 4.27),
                                new OA\Property(property: 'table', type: 'string', example: 'A'),
                                new OA\Property(property: 'effectiveDate', type: 'string', format: 'date', example: '2026-04-24'),
                            ]
                        ),
                        new OA\Property(
                            property: 'meta',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'status', type: 'string', example: 'created'),
                                new OA\Property(property: 'source', type: 'string', example: 'NBP API'),
                                new OA\Property(property: 'documentationUrl', type: 'string', example: '/api/documentation'),
                                new OA\Property(
                                    property: 'timeline',
                                    type: 'array',
                                    items: new OA\Items(
                                        type: 'object',
                                        properties: [
                                            new OA\Property(property: 'title', type: 'string', example: 'Fetch exchange rate from NBP'),
                                            new OA\Property(property: 'detail', type: 'string', example: 'Tabela A, data 2026-04-24'),
                                            new OA\Property(property: 'state', type: 'string', example: 'success'),
                                        ]
                                    )
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Waluta nie zostala znaleziona w API NBP',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'error',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'code', type: 'string', example: 'NBP_NOT_FOUND'),
                                new OA\Property(property: 'message', type: 'string', example: 'Currency code XYZ was not found in NBP API.'),
                            ]
                        ),
                        new OA\Property(
                            property: 'meta',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'documentationUrl', type: 'string', example: '/api/documentation'),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function show(string $code, NBPApi $api, CurrencyService $service): JsonResponse
    {
        $normalizedCode = strtoupper($code);
        $exists = Currency::query()->where('currencyCode', $normalizedCode)->exists();

        try {
            $details = $api->getExchangeRateDetails($normalizedCode);
            $currency = $service->create($details['name'], $normalizedCode, $details['exchangeRate']);
        } catch (NBPApiException $e) {
            return new JsonResponse([
                'error' => [
                    'code' => 'NBP_NOT_FOUND',
                    'message' => sprintf('Currency code %s was not found in NBP API.', $normalizedCode),
                ],
                'meta' => [
                    'documentationUrl' => '/api/documentation',
                ],
            ], status: 404);
        }

        $payload = [
            'data' => [
                'id' => $currency->id,
                'name' => $currency->name,
                'currencyCode' => $currency->currencyCode,
                'exchangeRate' => $currency->exchangeRate,
                'exchangeRateDecimal' => $details['exchangeRateDecimal'],
                'table' => $details['table'],
                'effectiveDate' => $details['effectiveDate'],
            ],
            'meta' => [
                'status' => $exists ? 'updated' : 'created',
                'source' => 'NBP API',
                'documentationUrl' => '/api/documentation',
            ],
        ];

        $payload['meta']['timeline'] = [
            [
                'title' => 'Normalize currency code',
                'detail' => sprintf('Input normalized to %s.', $normalizedCode),
                'state' => 'ready',
                'request' => [
                    'code' => $normalizedCode,
                ],
            ],
            [
                'title' => 'Fetch exchange rate from NBP',
                'detail' => sprintf('Tabela %s, data %s.', $details['table'], $details['effectiveDate'] ?? 'brak'),
                'state' => 'success',
                'response' => [
                    'name' => $details['name'],
                    'code' => $details['code'],
                    'exchangeRate' => $details['exchangeRate'],
                    'exchangeRateDecimal' => $details['exchangeRateDecimal'],
                    'effectiveDate' => $details['effectiveDate'],
                ],
            ],
            [
                'title' => 'Persist in local database',
                'detail' => $exists ? 'Existing record updated.' : 'New record created.',
                'state' => $exists ? 'pending' : 'success',
                'response' => [
                    'id' => $currency->id,
                    'currencyCode' => $currency->currencyCode,
                    'exchangeRate' => $currency->exchangeRate,
                    'status' => $payload['meta']['status'],
                ],
            ],
            [
                'title' => 'Return API response',
                'detail' => 'JSON response prepared for UI and external integrations.',
                'state' => 'ready',
                'response' => [
                    'data' => $payload['data'],
                    'meta' => [
                        'status' => $payload['meta']['status'],
                        'source' => $payload['meta']['source'],
                    ],
                ],
            ],
        ];

        return new JsonResponse($payload);
    }
}
