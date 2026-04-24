<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ShowcaseController extends Controller
{
    public function index(): View
    {
        return view('welcome', [
            'presentation' => [
                'project' => [
                    'name' => 'Kursy walut NBP',
                    'subtitle' => 'Lekki interfejs do synchronizacji walut z API Narodowego Banku Polskiego.',
                    'summary' => 'Ten ekran laczy landing, tester endpointu i dokumentacje OpenAPI. Jeden request pobiera kurs z tabeli A NBP, zapisuje go lokalnie i zwraca gotowy JSON do dalszej integracji.',
                ],
                'metrics' => [
                    [
                        'label' => 'Endpoint',
                        'value' => 'GET /api/currency/{code}',
                    ],
                    [
                        'label' => 'Dokumentacja',
                        'value' => '/api/documentation',
                    ],
                    [
                        'label' => 'Zrodlo kursu',
                        'value' => 'NBP tabela A',
                    ],
                ],
                'highlights' => [
                    [
                        'title' => 'Synchronizacja live',
                        'copy' => 'Kod waluty jest normalizowany, pobierany z NBP i zapisywany w lokalnej tabeli `currency` bez dodatkowej orkiestracji po stronie klienta.',
                    ],
                    [
                        'title' => 'JSON gotowy do integracji',
                        'copy' => 'Odpowiedz API zwraca dane waluty, wartosc dziesietna kursu oraz meta informacje przydatne w systemach zewnetrznych.',
                    ],
                    [
                        'title' => 'OpenAPI pod reka',
                        'copy' => 'Dokumentacja jest generowana z kodu aplikacji, wiec UI i specyfikacja opisuja ten sam endpoint i te same kontrakty odpowiedzi.',
                    ],
                ],
                'sequence' => [
                    [
                        'step' => '01',
                        'title' => 'Normalize input',
                        'copy' => 'Interfejs zamienia wpisany kod na standard ISO 4217 i odrzuca wartosci, ktore nie wygladaja jak trzyliterowy symbol waluty.',
                    ],
                    [
                        'step' => '02',
                        'title' => 'Fetch from NBP',
                        'copy' => 'Backend pobiera sredni kurs z publicznego API NBP wraz z data publikacji i zapisuje wartosc w formacie przyjaznym dla bazy.',
                    ],
                    [
                        'step' => '03',
                        'title' => 'Persist and return',
                        'copy' => 'Aplikacja tworzy lub aktualizuje rekord w lokalnej tabeli i od razu odsyla JSON, ktory mozna podpiac do innych systemow.',
                    ],
                ],
                'defaults' => [
                    'code' => 'EUR',
                ],
                'routes' => [
                    'currencyBase' => '/api/currency',
                    'documentation' => '/api/documentation',
                ],
                'samples' => ['EUR', 'USD', 'GBP', 'CHF'],
            ],
        ]);
    }
}
