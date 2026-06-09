import Chart from 'chart.js/auto';
import * as Turbo from '@hotwired/turbo';
window.Turbo = Turbo;
window.turbo = Turbo;

// ═══════════════════════════════════════════════
// Chart initialization (Turbo-compatible)
// ═══════════════════════════════════════════════

function initTurboCharts() {
  document.querySelectorAll('canvas[data-chart-config]').forEach((canvas) => {
    try {
      const cfg = JSON.parse(canvas.dataset.chartConfig);
      if (canvas._chart) canvas._chart.destroy();
      canvas._chart = new Chart(canvas, cfg);
    } catch (e) {
      console.error('Error initializing chart:', canvas.id, e);
    }
  });
}

document.addEventListener('DOMContentLoaded', initTurboCharts);
document.addEventListener('turbo:load', initTurboCharts);
document.addEventListener('turbo:frame-load', initTurboCharts);

// ═══════════════════════════════════════════════
// Global Chart reference
// ═══════════════════════════════════════════════

window.Chart = Chart;

// ═══════════════════════════════════════════════
// Calendar lazy loading (Turbo-compatible)
// ═══════════════════════════════════════════════

let calendarioModule = null;

async function loadCalendar() {
  const calendarEl = document.getElementById('calendar');
  if (!calendarEl) return;

  // Skip if calendar is already rendered in this DOM (prevents double init on first load)
  if (calendarEl.querySelector('.fc')) return;

  if (!calendarioModule) {
    calendarioModule = await import('./calendario.js');
  }

  calendarioModule.initCalendar();
}

document.addEventListener('DOMContentLoaded', loadCalendar);
document.addEventListener('turbo:load', loadCalendar);
document.addEventListener('turbo:frame-load', loadCalendar);

// Cleanup FullCalendar on Turbo navigation to prevent memory leaks
document.addEventListener('turbo:before-render', () => {
  calendarioModule?.destroyCalendar?.();
});

// ═══════════════════════════════════════════════
// AdminLTE sidebar
// Handled natively by adminlte.min.js via data-lte-toggle="sidebar"
// No custom handler needed — it conflicts with AdminLTE's built-in logic
// ═══════════════════════════════════════════════

// ═══════════════════════════════════════════════
// Character counters for inputs with maxlength
// Auto-attaches to any input/textarea with maxlength attribute
// ═══════════════════════════════════════════════

document.addEventListener('input', (e) => {
  const el = e.target;
  const max = el.getAttribute('maxlength');
  if (!max) return;
  const counter = el.parentElement.querySelector('.char-counter');
  if (!counter) return;
  const len = el.value.length;
  counter.textContent = `${len}/${max}`;
  counter.classList.toggle('text-danger', len >= max);
  counter.classList.toggle('text-warning', len >= max * 0.85 && len < max);
});

function initCharCounters() {
  document.querySelectorAll('input[maxlength], textarea[maxlength]').forEach((el) => {
    if (el.closest('.input-group')) return; // skip input-group (login, etc.)
    if (el.parentElement.querySelector('.char-counter')) return; // already initialized
    const max = parseInt(el.getAttribute('maxlength'));
    const len = el.value.length;
    const counter = document.createElement('small');
    counter.className = 'char-counter text-muted float-end';
    counter.style.cssText = 'font-size: 0.72rem; margin-top: 2px;';
    counter.textContent = `${len}/${max}`;
    if (len >= max) counter.classList.add('text-danger');
    else if (len >= max * 0.85) counter.classList.add('text-warning');
    el.parentElement.appendChild(counter);
  });
}

document.addEventListener('DOMContentLoaded', initCharCounters);
document.addEventListener('turbo:load', initCharCounters);
document.addEventListener('turbo:frame-load', initCharCounters);

// Treeview accordion — only toggle on parent items (href="#")
document.addEventListener('click', (e) => {
  const link = e.target.closest('.nav-treeview .nav-item > .nav-link[href="#"]');
  if (!link) return;
  e.preventDefault();
  const li = link.closest('.nav-item');
  li.classList.toggle('menu-open');
  const treeview = li.querySelector('.nav-treeview');
  if (treeview) {
    treeview.style.display = li.classList.contains('menu-open') ? 'block' : 'none';
  }
});

// ═══════════════════════════════════════════════
// Export buttons loading state
// Shows spinner + disables button while download starts
// ═══════════════════════════════════════════════
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.export-btn');
  if (!btn) return;
  const textEl = btn.querySelector('.btn-text');
  if (textEl) btn.dataset.originalText = textEl.textContent;
  btn.classList.add('disabled');
  btn.setAttribute('aria-disabled', 'true');
  if (textEl) textEl.textContent = 'Descargando...';
  const spinner = document.createElement('span');
  spinner.className = 'spinner-border spinner-border-sm me-1';
  spinner.setAttribute('role', 'status');
  btn.prepend(spinner);
  // Re-enable after 8s as safety net
  setTimeout(() => {
    btn.classList.remove('disabled');
    btn.removeAttribute('aria-disabled');
    const sp = btn.querySelector('.spinner-border');
    if (sp) sp.remove();
    if (textEl) textEl.textContent = btn.dataset.originalText || 'Descargar';
  }, 8000);
});
