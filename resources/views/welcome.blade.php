<!doctype html>
<html lang="pl">
<head>
    @if (!empty(config('services.google_analytics.measurement_id')))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.measurement_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ config('services.google_analytics.measurement_id') }}');
        </script>
    @endif
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $presentation['project']['name'] }}</title>
    <meta name="description" content="{{ $presentation['project']['summary'] }}">
    <link rel="stylesheet" href="{{ asset('currency-showcase.css') }}">
</head>
<body>
    <div class="page-shell">
        <header class="hero">
            <div class="hero-copy reveal">
                <p class="eyebrow">Portfolio API Showcase</p>
                <h1>{{ $presentation['project']['name'] }}</h1>
                <p class="lede">{{ $presentation['project']['subtitle'] }}</p>
                <p class="summary">{{ $presentation['project']['summary'] }}</p>

                <div class="hero-actions">
                    <button type="button" class="action action-primary" data-sample-code="{{ $presentation['defaults']['code'] }}">
                        Uruchom test {{ $presentation['defaults']['code'] }}
                    </button>
                    <a class="action action-secondary" href="{{ $presentation['routes']['documentation'] }}" target="_blank" rel="noreferrer">
                        Otworz OpenAPI
                    </a>
                </div>

                <div class="inline-meta">
                    <span class="meta-chip">Laravel 9</span>
                    <span class="meta-chip">NBP table A</span>
                    <span class="meta-chip">Swagger UI</span>
                </div>
            </div>

            <aside class="hero-panel reveal">
                <p class="panel-label">Co dostajesz od razu</p>
                <p class="panel-copy">
                    Warstwa prezentacji pokazuje nie tylko endpoint, ale tez caly przeplyw: normalizacje kodu, pobranie z NBP, zapis w bazie i finalny JSON.
                </p>

                <dl class="metric-grid">
                    @foreach ($presentation['metrics'] as $metric)
                        <div class="metric">
                            <dt>{{ $metric['label'] }}</dt>
                            <dd>{{ $metric['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </aside>
        </header>

        <main class="layout">
            <section class="story reveal">
                <div class="section-head">
                    <p class="eyebrow">Jak dziala ten ekran</p>
                    <h2>Front, API i dokumentacja opisuja ten sam kontrakt</h2>
                </div>

                <div class="highlight-grid">
                    @foreach ($presentation['highlights'] as $highlight)
                        <article class="story-card reveal">
                            <h3>{{ $highlight['title'] }}</h3>
                            <p>{{ $highlight['copy'] }}</p>
                        </article>
                    @endforeach
                </div>

                <div class="sequence reveal">
                    @foreach ($presentation['sequence'] as $step)
                        <article class="sequence-step">
                            <p class="sequence-index">{{ $step['step'] }}</p>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['copy'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="console reveal">
                <div class="console-head">
                    <div>
                        <p class="eyebrow">Interaktywny runner</p>
                        <h2>Podglad synchronizacji waluty i odpowiedzi JSON</h2>
                        <p class="console-copy">
                            UI jest celowo podobne do `inpost-api`: najpierw historia i metryki, a nizej panel do recznego wywolania endpointu oraz inspekcji payloadu.
                        </p>
                    </div>
                    <p class="console-badge" id="run-mode-label">Tryb live</p>
                </div>

                <form id="currency-form" class="demo-form">
                    <div class="inline-grid">
                        <div class="inline-card">
                            <div class="field-group">
                                <label for="currency_code">Kod waluty</label>
                                <input
                                    id="currency_code"
                                    name="currency_code"
                                    maxlength="3"
                                    autocomplete="off"
                                    spellcheck="false"
                                    value="{{ $presentation['defaults']['code'] }}"
                                    placeholder="EUR"
                                >
                            </div>

                            <div class="sample-list">
                                @foreach ($presentation['samples'] as $sample)
                                    <button type="button" class="sample-chip" data-sample-code="{{ $sample }}">{{ $sample }}</button>
                                @endforeach
                            </div>
                        </div>

                        <div class="inline-card">
                            <p class="mini-label">Kontrakt endpointu</p>
                            <h3>GET /api/currency/{code}</h3>
                            <p class="info-note">
                                Endpoint pobiera kurs z NBP, zapisuje go lokalnie i zwraca dane waluty wraz z meta informacjami dla UI oraz OpenAPI.
                            </p>
                            <div class="inline-meta">
                                <span class="meta-chip">Response 200 / 404</span>
                                <span class="meta-chip">JSON only</span>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="runner-actions">
                    <button type="button" class="action action-primary" data-form-submit>
                        Synchronizuj walute
                    </button>
                    <a class="action action-secondary" href="{{ $presentation['routes']['documentation'] }}" target="_blank" rel="noreferrer">
                        Otworz dokumentacje
                    </a>
                </div>

                <div class="output-grid">
                    <div class="timeline-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Timeline</h3>
                                <p id="run-status">Gotowe do uruchomienia</p>
                            </div>
                        </div>
                        <div id="timeline" class="timeline"></div>
                    </div>

                    <div class="payload-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Payload / response</h3>
                                <p>Kliknij krok po lewej, aby zobaczyc request albo odpowiedz API.</p>
                            </div>
                        </div>
                        <pre id="payload-view" class="payload-view"><code>Wybierz przykladowy kod waluty albo uruchom synchronizacje recznie.</code></pre>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        window.currencyShowcasePresentation = {!! json_encode($presentation, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!};
    </script>
    <script src="{{ asset('currency-showcase.js') }}"></script>
</body>
</html>
