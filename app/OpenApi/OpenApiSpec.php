<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Kursy walut NBP API',
    description: 'API do synchronizacji kursow walut z tabeli A Narodowego Banku Polskiego i zapisu w lokalnej bazie danych.'
)]
#[OA\Server(
    url: '/',
    description: 'Biezace srodowisko aplikacji'
)]
#[OA\Tag(
    name: 'Currencies',
    description: 'Operacje synchronizacji kursow walut z NBP'
)]
class OpenApiSpec
{
}
