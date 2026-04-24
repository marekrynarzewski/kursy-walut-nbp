# kursy-walut-nbp

Prosta aplikacja Laravel do pobierania kursow walut z API NBP i zapisywania ich w lokalnej bazie MySQL.

## O czym jest ten projekt

Projekt udostepnia pojedynczy endpoint HTTP, ktory:

- pobiera aktualny kurs sredni waluty z API Narodowego Banku Polskiego,
- sprawdza, czy dana waluta istnieje juz w lokalnej bazie,
- tworzy nowy rekord albo aktualizuje istniejacy,
- zapisuje wynik w tabeli `currency`.

Aplikacja korzysta z tabeli A API NBP (`/api/exchangerates/rates/A/{code}`), czyli standardowych srednich kursow walut wzgledem PLN.

Projekt zawiera takze landing page z interaktywnym runnerem oraz dokumentacje OpenAPI dostepna pod `/api/documentation`.

## Jak to dziala

Wywolanie:

```text
GET /currency/{code}
```

Wersja API do integracji i dokumentacji:

```text
GET /api/currency/{code}
```

Przyklad:

```text
GET /currency/USD
GET /currency/EUR
```

Po otrzymaniu poprawnej odpowiedzi z NBP aplikacja zapisuje:

- `name` - nazwe waluty,
- `currencyCode` - kod waluty, np. `USD`,
- `exchangeRate` - kurs zapisany jako liczba calkowita pomnozona przez `100`.

Przyklad: kurs `4.75` zostanie zapisany jako `475`.

Jesli rekord z danym `currencyCode` juz istnieje, aktualizowany jest tylko kurs. Dla niepoprawnego kodu waluty aplikacja zwraca odpowiedz z kodem HTTP `404`.

## Stos technologiczny

- PHP 8.2
- Laravel 9
- MySQL 8
- Docker Compose

## Uruchomienie lokalne

Z katalogu projektu:

```bash
cd bitbucket/packages/kursy-walut-nbp
```

1. Przygotuj plik srodowiskowy:

```bash
make init
```

2. Zbuduj i uruchom kontenery oraz zainstaluj zaleznosci:

```bash
make install
```

Domyslnie `make install` uruchamia lokalny profil `standalone`, czyli aplikacje razem z kontenerem MySQL.

3. Wygeneruj klucz aplikacji:

```bash
docker compose exec app php artisan key:generate
```

4. Uruchom migracje:

```bash
docker compose exec app php artisan migrate
```

5. Wygeneruj dokumentacje OpenAPI:

```bash
make docs
```

Po starcie aplikacja bedzie dostepna domyslnie pod adresem:

```text
http://localhost
```

Najwazniejsze adresy po starcie:

- `http://localhost/` - landing page z testerem endpointu
- `http://localhost/api/documentation` - Swagger UI / OpenAPI
- `http://localhost/api/currency/EUR` - przykladowe wywolanie API

Port aplikacji i bazy mozna zmienic w pliku `.env`, korzystajac z:

- `FORWARD_APP_PORT`
- `FORWARD_DB_PORT`

Domyslnie lokalna baza MySQL jest publikowana na porcie `3307`, zeby ograniczyc konflikty z innymi lokalnymi instancjami.

## Przyklad uzycia

Pobranie kursu dolara i zapis do bazy:

```bash
curl http://localhost/currency/USD
```

Po wywolaniu endpointu w tabeli `currency` pojawi sie nowy rekord albo zostanie zaktualizowany istniejacy wpis dla `USD`.

## Baza danych

Migracja tworzy tabele `currency` z polami:

- `id` - UUID,
- `name` - nazwa waluty,
- `currencyCode` - kod waluty,
- `exchangeRate` - kurs waluty.

Tabela ma unikalnosc na parze `currencyCode` i `name`.

## Testy

W projekcie znajduja sie testy jednostkowe dla warstwy serwisowej, w tym integracji z klientem API NBP.

Przykladowe uruchomienie:

```bash
docker compose exec app php artisan test
```
