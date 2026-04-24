const presentation = window.currencyShowcasePresentation || {};

const form = document.querySelector('#currency-form');
const codeInput = document.querySelector('#currency_code');
const timelineElement = document.querySelector('#timeline');
const payloadViewElement = document.querySelector('#payload-view code');
const runStatusElement = document.querySelector('#run-status');
const runModeLabelElement = document.querySelector('#run-mode-label');

function formatJson(data) {
  return JSON.stringify(data, null, 2);
}

function setPayloadView(step) {
  if (!step) {
    payloadViewElement.textContent = 'Brak danych.';
    return;
  }

  const preview = {};

  if (step.request) {
    preview.request = step.request;
  }

  if (step.response) {
    preview.response = step.response;
  }

  payloadViewElement.textContent = formatJson(preview);
}

function renderTimeline(steps) {
  timelineElement.innerHTML = '';

  if (!steps.length) {
    timelineElement.innerHTML = '<p class="info-note">Brak krokow do pokazania.</p>';
    setPayloadView(null);
    return;
  }

  steps.forEach((step, index) => {
    const item = document.createElement('button');
    item.type = 'button';
    item.className = `timeline-item state-${step.state || 'ready'}`;
    item.innerHTML = `
      <span class="timeline-marker">${String(index + 1).padStart(2, '0')}</span>
      <span class="timeline-copy">
        <strong>${step.title}</strong>
        <span>${step.detail}</span>
      </span>
    `;

    item.addEventListener('click', () => {
      document.querySelectorAll('.timeline-item').forEach((element) => {
        element.classList.remove('is-active');
      });
      item.classList.add('is-active');
      setPayloadView(step);
    });

    timelineElement.appendChild(item);
  });

  const first = timelineElement.querySelector('.timeline-item');
  if (first) {
    first.click();
  }
}

function normalizeCode(value) {
  return String(value || '').trim().toUpperCase();
}

function buildErrorTimeline(code, message) {
  return [
    {
      title: 'Validate input',
      detail: 'Kod waluty musi miec dokladnie trzy litery.',
      state: 'error',
      request: {
        code,
      },
      response: {
        error: message,
      },
    },
  ];
}

async function runCurrencySync() {
  const code = normalizeCode(codeInput.value);
  codeInput.value = code;
  runModeLabelElement.textContent = 'Tryb live';

  if (!/^[A-Z]{3}$/.test(code)) {
    runStatusElement.textContent = 'Formularz wymaga poprawy';
    renderTimeline(buildErrorTimeline(code, 'Uzyj trzyliterowego kodu waluty, np. EUR.'));
    return;
  }

  runStatusElement.textContent = `Pobieranie kursu ${code}...`;

  try {
    const response = await fetch(`${presentation.routes.currencyBase}/${code}`, {
      headers: {
        Accept: 'application/json',
      },
    });

    const result = await response.json();

    if (!response.ok) {
      runStatusElement.textContent = 'NBP nie zwrocilo kursu dla podanego kodu';
      renderTimeline([
        {
          title: 'Normalize currency code',
          detail: `Input normalized to ${code}.`,
          state: 'ready',
          request: {
            code,
          },
        },
        {
          title: 'Fetch exchange rate from NBP',
          detail: 'API zglosilo blad dla podanego kodu waluty.',
          state: 'error',
          response: result,
        },
      ]);
      return;
    }

    runStatusElement.textContent = `${result.data.currencyCode} synchronized as ${result.meta.status}`;
    renderTimeline(result.meta.timeline || []);
  } catch (error) {
    runStatusElement.textContent = 'Nie udalo sie polaczyc z endpointem';
    renderTimeline([
      {
        title: 'Network error',
        detail: 'Przegladarka nie otrzymala poprawnej odpowiedzi z aplikacji.',
        state: 'error',
        response: {
          message: String(error),
        },
      },
    ]);
  }
}

document.querySelectorAll('[data-sample-code]').forEach((button) => {
  button.addEventListener('click', () => {
    const value = button.getAttribute('data-sample-code') || '';
    codeInput.value = value;
    runCurrencySync();
  });
});

document.querySelector('[data-form-submit]')?.addEventListener('click', () => {
  runCurrencySync();
});

form?.addEventListener('submit', (event) => {
  event.preventDefault();
  runCurrencySync();
});

document.querySelectorAll('.reveal').forEach((element, index) => {
  element.style.animationDelay = `${index * 110}ms`;
});

renderTimeline([
  {
    title: 'Showcase ready',
    detail: 'Wpisz kod waluty i uruchom live sync z NBP, zeby zobaczyc request oraz wynik zapisu.',
    state: 'ready',
    request: {
      endpoint: `${presentation.routes.currencyBase}/{code}`,
      docs: presentation.routes.documentation,
    },
  },
]);
