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
document.addEventListener('DOMContentLoaded', reinitCardCollapse);
document.addEventListener('turbo:load', initTurboCharts);
document.addEventListener('turbo:load', reinitCardCollapse);

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

// ═══════════════════════════════════════════════
// Bootstrap Modal accessibility fix (aria-hidden)
// Blur ALL focused elements INSIDE the modal BEFORE Bootstrap sets aria-hidden
// ═══════════════════════════════════════════════
document.addEventListener('hide.bs.modal', (e) => {
  const modal = e.target;
  if (!modal) return;
  // Blur the active element if it's inside this modal
  if (document.activeElement && modal.contains(document.activeElement)) {
    document.activeElement.blur();
  }
  // Also blur any focused element inside the modal (handles edge cases)
  const focused = modal.querySelector(':focus');
  if (focused) focused.blur();
});

// ═══════════════════════════════════════════════
// MODAL EVENT DELEGATION (attached once to document, never duplicated)
// Uses show.bs.modal / hidden.bs.modal on document to handle
// all modals without per-element listeners.
// ═══════════════════════════════════════════════
let _modalFetchCtrl = null;
let _editarId = null;

function _ensureFetchCtrl() {
  if (_modalFetchCtrl) _modalFetchCtrl.abort();
  _modalFetchCtrl = new AbortController();
  return _modalFetchCtrl.signal;
}

// --- SHOW: Load data when any modal opens ---
// Guard against duplicate show events (Bootstrap re-execution safety)
document.addEventListener('show.bs.modal', (e) => {
  const modalEl = e.target;
  const related = e.relatedTarget;

  // ── Acceso detail ──
  if (modalEl.id === 'accesoDetalleModal') {
    if (modalEl._isShowing) return;
    const id = related?.dataset?.id;
    if (!id) return;
    modalEl._isShowing = true;
    const body = document.getElementById('accesoDetalleModalBody');
    const signal = _ensureFetchCtrl();
    body.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>Cargando...</div>';
    fetch(`/admin/accesos/${id}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      signal
    })
      .then(r => r.text())
      .then(html => { body.innerHTML = html; })
      .catch(err => {
        if (err.name === 'AbortError') return;
        body.innerHTML = '<div class="text-center text-danger py-4"><i class="fas fa-exclamation-circle fa-2x mb-2 d-block"></i>Error al cargar el detalle</div>';
      });
    return;
  }

  // ── Usuario detail ──
  if (modalEl.id === 'usuarioDetalleModal') {
    if (modalEl._isShowing) return;
    const id = related?.dataset?.id;
    if (!id) return;
    modalEl._isShowing = true;
    const body = document.getElementById('usuarioDetalleModalBody');
    const signal = _ensureFetchCtrl();
    body.innerHTML = '<div class="text-center text-muted py-5"><div class="spinner-border text-primary mb-3" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mb-0">Cargando información...</p></div>';
    fetch(`/admin/usuarios/${id}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      signal
    })
      .then(r => { if (!r.ok) throw new Error('Error ' + r.status); return r.text(); })
      .then(html => {
        body.innerHTML = html;
        body.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
      })
      .catch(err => {
        if (err.name === 'AbortError') return;
        body.innerHTML = '<div class="text-center text-danger py-5"><i class="fas fa-exclamation-circle fa-3x mb-3"></i><p class="mb-0">Error al cargar el detalle</p></div>';
      });
    return;
  }

  // ── Casilleros: move to body to escape stacking context ──
  if (modalEl.id === 'modalDetalle' && modalEl.parentElement !== document.body) {
    const previo = document.body.querySelector(':scope > #modalDetalle');
    if (previo && previo !== modalEl) previo.remove();
    document.body.appendChild(modalEl);
  }

  // ── Casilleros detail (read data attributes from clicked box) ──
  if (modalEl.id === 'modalDetalle' && related?.dataset?.codigo) {
    const d = related.dataset;
    document.getElementById('detalleCodigo').textContent = d.codigo;
    document.getElementById('detalleEstado').textContent = d.estado;
    document.getElementById('detallePersona').textContent = d.persona || 'Libre';
    document.getElementById('detalleActividad').textContent = d.actividad || '—';
    document.getElementById('detalleHora').textContent = d.hora || '—';
  }
});

// --- HIDDEN: Cleanup when any modal closes ---
document.addEventListener('hidden.bs.modal', (e) => {
  e.target._isShowing = false;
  if (_modalFetchCtrl) { _modalFetchCtrl.abort(); _modalFetchCtrl = null; }
  if (e.target.id === 'usuarioDetalleModal') {
    const body = document.getElementById('usuarioDetalleModalBody');
    if (body) body.innerHTML = '';
  }
});

// --- Delegation: edit button click inside usuario detail ---
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.btn-editar');
  if (!btn) return;
  e.preventDefault();
  _fillEditarForm({
    id:           btn.dataset.id,
    email:        btn.dataset.email        ?? '',
    celular:      btn.dataset.celular      ?? '',
    direccion:    btn.dataset.direccion    ?? '',
    municipio_id: btn.dataset.municipioId  ?? '',
    rol_id:       btn.dataset.rolId        ?? '',
    estado:       btn.dataset.estado       ?? 'activo',
  });
});

function _fillEditarForm(datos) {
  _editarId = datos.id;
  document.getElementById('edit_email').value        = datos.email;
  document.getElementById('edit_celular').value      = datos.celular;
  document.getElementById('edit_direccion').value    = datos.direccion;
  document.getElementById('edit_municipio_id').value = datos.municipio_id;
  document.getElementById('edit_rol_id').value       = datos.rol_id;
  document.getElementById('edit_estado').value       = datos.estado;

  const detalleModalEl = document.getElementById('usuarioDetalleModal');
  const editarModalEl  = document.getElementById('editarModal');
  const detalleInstance = bootstrap.Modal.getInstance(detalleModalEl);

  if (detalleInstance) {
    const onHidden = () => {
      detalleModalEl.removeEventListener('hidden.bs.modal', onHidden);
      document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
      document.body.classList.remove('modal-open');
      document.body.style.removeProperty('padding-right');
      bootstrap.Modal.getOrCreateInstance(editarModalEl).show();
    };
    detalleModalEl.addEventListener('hidden.bs.modal', onHidden);
    detalleInstance.hide();
  }
}

// --- Table edit button: fetch data then show modal ---
document.addEventListener('click', (e) => {
  const btn = e.target.closest('button[data-bs-target="#editarModal"]');
  if (!btn || btn.classList.contains('btn-editar')) return; // skip detail's edit btn
  e.preventDefault();
  const id = btn.dataset.id;
  if (!id) return;
  const signal = _ensureFetchCtrl();
  fetch(`/admin/usuarios/${id}`, {
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
    signal
  })
    .then(r => { if (!r.ok) throw new Error(); return r.json(); })
    .then(data => {
      _fillEditarForm({
        id: data.usuario_id, email: data.email, celular: data.celular,
        direccion: data.direccion, municipio_id: data.municipio_id,
        rol_id: data.rol_id, estado: data.estado
      });
      // Show modal after form is filled
      document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
      document.body.classList.remove('modal-open');
      bootstrap.Modal.getOrCreateInstance(document.getElementById('editarModal')).show();
    })
    .catch(err => { if (err.name !== 'AbortError') alert('Error al cargar datos para editar'); });
});

// --- Save changes (delegation on document) ---
document.addEventListener('click', (e) => {
  const btn = e.target.closest('#btnGuardarUsuario');
  if (!btn) return;
  if (!_editarId) return;
  e.preventDefault();

  const textoOriginal = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

  const signal = _ensureFetchCtrl();
  fetch(`/admin/usuarios/${_editarId}`, {
    method: 'PUT',
    headers: {
      'Content-Type':     'application/json',
      'X-CSRF-TOKEN':     document.querySelector('meta[name=csrf-token]')?.content,
      'X-Requested-With': 'XMLHttpRequest',
      'Accept':           'application/json',
    },
    body: JSON.stringify({
      email:        document.getElementById('edit_email').value,
      celular:      document.getElementById('edit_celular').value,
      direccion:    document.getElementById('edit_direccion').value,
      municipio_id: document.getElementById('edit_municipio_id').value || null,
      rol_id:       document.getElementById('edit_rol_id').value,
      estado:       document.getElementById('edit_estado').value,
    }),
    signal,
    redirect: 'manual'
  })
    .then(r => {
      if (r.type === 'opaqueredirect' || r.status === 0 || r.status === 419 || r.status === 401) {
        window.location.reload(); throw new Error('__skip');
      }
      if (!r.ok) return r.json().then(err => { throw new Error(err.message || Object.values(err.errors || {}).flat().join('\n') || 'Error del servidor'); }, () => { throw new Error('Error del servidor'); });
      return r.json();
    })
    .then(data => {
      if (data.ok || data.success) {
        bootstrap.Modal.getInstance(document.getElementById('editarModal'))?.hide();
        _mostrarToast('Cambios guardados correctamente', 'success');
        setTimeout(() => window.location.reload(), 800);
      } else {
        throw new Error(data.message || 'Error al guardar');
      }
    })
    .catch(err => {
      if (err.message === '__skip') return;
      alert(err.message || 'Error al guardar.');
    })
    .finally(() => {
      btn.disabled = false;
      btn.innerHTML = textoOriginal;
    });
});

function _mostrarToast(mensaje, tipo = 'success') {
  const toast = document.createElement('div');
  toast.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
  toast.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:300px;';
  toast.innerHTML = `<i class="fas fa-${tipo === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${mensaje}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}


// ═══════════════════════════════════════════════
// Ajustes: Departamentos → Municipios cascade
// ═══════════════════════════════════════════════
function initAjustesCascade() {
  const selectDepto = document.getElementById('selectDepartamento');
  const selectMuni = document.getElementById('selectMunicipio');
  if (!selectDepto || !selectMuni) return;

  if (selectDepto._cascadeInit) return;
  selectDepto._cascadeInit = true;

  const departamentos = JSON.parse(decodeURIComponent(selectDepto.dataset.departamentos || '{}'));
  const municipioActual = selectDepto.dataset.municipioActual || '';

  function cargarMunicipios(deptoId, seleccionarId) {
    const municipios = departamentos[deptoId] ?? [];
    selectMuni.innerHTML = '<option value="">— Selecciona un municipio —</option>';
    if (!municipios.length) { selectMuni.disabled = true; return; }
    municipios.forEach(m => {
      const opt = document.createElement('option');
      opt.value = m.id;
      opt.textContent = m.nombre;
      if (m.id == seleccionarId) opt.selected = true;
      selectMuni.appendChild(opt);
    });
    selectMuni.disabled = false;
  }

  selectDepto.addEventListener('change', function () {
    cargarMunicipios(this.value, null);
  });

  if (selectDepto.value) {
    cargarMunicipios(selectDepto.value, municipioActual);
  }
}

// ═══════════════════════════════════════════════
// Actividades: Pass calendar events via data attributes
// ═══════════════════════════════════════════════
function initActividadesData() {
  const calendarEl = document.getElementById('calendar');
  if (!calendarEl) return;
  if (calendarEl._dataInit) return;
  calendarEl._dataInit = true;

  if (calendarEl.dataset.events) {
    window.calendarEvents = JSON.parse(calendarEl.dataset.events);
  }
  if (calendarEl.dataset.routeCrear) {
    window.routeCrear = calendarEl.dataset.routeCrear;
  }
  if (calendarEl.dataset.routeActualizar) {
    window.routeActualizar = calendarEl.dataset.routeActualizar;
  }
  if (calendarEl.dataset.routeEliminar) {
    window.routeEliminar = calendarEl.dataset.routeEliminar;
  }
}

// ═══════════════════════════════════════════════
// Login: password toggle
// ═══════════════════════════════════════════════
function initLoginPasswordToggle() {
  const toggle = document.getElementById('toggle-password');
  if (!toggle) return;
  if (toggle._toggleInit) return;
  toggle._toggleInit = true;

  const input = document.getElementById('input-password');
  const icono = document.getElementById('icono-password');
  if (!input || !icono) return;

  toggle.addEventListener('click', () => {
    const visible = input.type === 'text';
    input.type = visible ? 'password' : 'text';
    icono.classList.toggle('fa-eye', visible);
    icono.classList.toggle('fa-eye-slash', !visible);
    input.focus();
  });

  input.addEventListener('blur', () => {
    input.type = 'password';
    icono.classList.remove('fa-eye-slash');
    icono.classList.add('fa-eye');
  });
}

// ═══════════════════════════════════════════════
// Alerta auto-close (progress bar animation)
// ═══════════════════════════════════════════════
function initAlertas() {
  const alertaGlobal = document.getElementById('alerta-global');
  const barraProgreso = document.getElementById('barra-progreso');
  if (alertaGlobal && barraProgreso) {
    requestAnimationFrame(() => requestAnimationFrame(() => {
      barraProgreso.style.width = '0%';
    }));
    setTimeout(() => {
      const instance = bootstrap.Alert.getOrCreateInstance(alertaGlobal);
      instance.close();
    }, 4200);
  }

  const alertaSession = document.getElementById('alerta-session');
  const barraSession = document.getElementById('barra-session');
  if (alertaSession && barraSession) {
    requestAnimationFrame(() => requestAnimationFrame(() => {
      barraSession.style.width = '0%';
    }));
    setTimeout(() => {
      const instance = bootstrap.Alert.getOrCreateInstance(alertaSession);
      instance.close();
    }, 4200);
  }
}

// ═══════════════════════════════════════════════
// Ingreso: real-time clock
// ═══════════════════════════════════════════════
let _clockInterval = null;

function initReloj() {
  const reloj = document.getElementById('reloj');
  if (!reloj) {
    if (_clockInterval) { clearInterval(_clockInterval); _clockInterval = null; }
    return;
  }
  if (_clockInterval) return;

  function actualizarReloj() {
    const ahora = new Date();
    const horas = String(ahora.getHours()).padStart(2, '0');
    const minutos = String(ahora.getMinutes()).padStart(2, '0');
    reloj.textContent = `${horas}:${minutos}`;
  }
  actualizarReloj();
  _clockInterval = setInterval(actualizarReloj, 1000);
}

// ═══════════════════════════════════════════════
// Re-initialize page-specific functionality after Turbo navigation
// ═══════════════════════════════════════════════
document.addEventListener('turbo:load', () => {
  initAjustesCascade();
  initActividadesData();
  initLoginPasswordToggle();
  initAlertas();
  initReloj();
  reinitCardCollapse();
});

// ═══════════════════════════════════════════════
// Turbo cleanup: dispose modals + abort pending fetches
// ═══════════════════════════════════════════════
document.addEventListener('turbo:before-render', () => {
  // Abort any in-flight fetch
  if (_modalFetchCtrl) { _modalFetchCtrl.abort(); _modalFetchCtrl = null; }

  // Dispose all active Bootstrap modal instances + remove backdrops
  document.querySelectorAll('.modal.show').forEach(modalEl => {
    const instance = bootstrap.Modal.getInstance(modalEl);
    if (instance) instance.dispose();
  });
  document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
  document.body.classList.remove('modal-open');
  document.body.style.removeProperty('padding-right');

  // Cleanup FullCalendar
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

// ═══════════════════════════════════════════════
// Card collapse (AdminLTE) — re-bind on Turbo navigation
// AdminLTE binds handlers at DOMContentLoaded. When Turbo replaces the body,
// new [data-lte-toggle="card-collapse"] buttons lose their handlers.
// Re-bind by cloning+replacing (removes old listeners) then attaching fresh ones.
// ═══════════════════════════════════════════════
function reinitCardCollapse() {
  document.querySelectorAll('[data-lte-toggle="card-collapse"]').forEach(btn => {
    // Skip if already bound (AdminLTE adds its own handler)
    if (btn._cardCollapseBound) return;
    btn._cardCollapseBound = true;
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const card = btn.closest('.card');
      if (!card) return;
      const L = 'collapsed-card';
      const body = card.querySelector(':scope > .card-body, :scope > .card-footer');
      const icon = btn.querySelector('i');

      if (card.classList.contains(L)) {
        // Expand
        card.classList.remove(L);
        card.classList.remove('was-collapsed');
        if (body) {
          body.style.removeProperty('display');
          let display = getComputedStyle(body).display;
          if (display === 'none') display = 'block';
          body.style.display = display;
          body.style.overflow = 'hidden';
          const targetH = body.scrollHeight;
          body.style.height = '0';
          body.offsetHeight;
          body.style.transition = 'height 0.3s ease';
          body.style.height = targetH + 'px';
          const done = () => {
            body.style.removeProperty('height');
            body.style.removeProperty('overflow');
            body.style.removeProperty('transition');
            body.removeEventListener('transitionend', done);
          };
          body.addEventListener('transitionend', done);
        }
        if (icon) { icon.classList.remove('fa-plus'); icon.classList.add('fa-minus'); }
      } else {
        // Collapse
        card.classList.add(L);
        if (body) {
          body.style.overflow = 'hidden';
          body.style.height = body.scrollHeight + 'px';
          body.offsetHeight;
          body.style.transition = 'height 0.3s ease';
          body.style.height = '0';
          const done = () => {
            body.style.display = 'none';
            body.style.removeProperty('height');
            body.style.removeProperty('overflow');
            body.style.removeProperty('transition');
            body.removeEventListener('transitionend', done);
          };
          body.addEventListener('transitionend', done);
        }
        if (icon) { icon.classList.remove('fa-minus'); icon.classList.add('fa-plus'); }
      }
    });
  });
}

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
