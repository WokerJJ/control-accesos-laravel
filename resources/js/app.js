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
// Accesos modal (AJAX detail)
// ═══════════════════════════════════════════════
let _accesosAbortCtrl = null;

function initAccesosModal() {
  const modalEl = document.getElementById('accesoDetalleModal');
  if (!modalEl) return;

  const modalBody = document.getElementById('accesoDetalleModalBody');
  const spinner = `
    <div class="text-center text-muted py-4">
      <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>
      Cargando...
    </div>`;

  if (modalEl._accesosInit) return;
  modalEl._accesosInit = true;

  modalEl.addEventListener('show.bs.modal', function (e) {
    const id = e.relatedTarget?.dataset?.id;
    if (!id) return;

    if (_accesosAbortCtrl) _accesosAbortCtrl.abort();
    _accesosAbortCtrl = new AbortController();

    modalBody.innerHTML = spinner;

    fetch(`/admin/accesos/${id}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      signal: _accesosAbortCtrl.signal
    })
      .then(r => r.text())
      .then(html => { modalBody.innerHTML = html; })
      .catch(err => {
        if (err.name === 'AbortError') return;
        modalBody.innerHTML = `
          <div class="text-center text-danger py-4">
            <i class="fas fa-exclamation-circle fa-2x mb-2 d-block"></i>
            Error al cargar el detalle
          </div>`;
      });
  });

  modalEl.addEventListener('hidden.bs.modal', function () {
    if (_accesosAbortCtrl) _accesosAbortCtrl.abort();
    modalBody.innerHTML = spinner;
  });
}

// ═══════════════════════════════════════════════
// Usuarios modals (AJAX detail + edit + save)
// ═══════════════════════════════════════════════
let _usuariosAbortCtrl = null;
let _editarAbortCtrl = null;
let _guardarAbortCtrl = null;

function initUsuariosModals() {
  const detalleModalEl = document.getElementById('usuarioDetalleModal');
  if (!detalleModalEl) return;

  if (detalleModalEl._usuariosInit) return;
  detalleModalEl._usuariosInit = true;

  const editarModalEl = document.getElementById('editarModal');
  const detalleBody = document.getElementById('usuarioDetalleModalBody');
  const spinner = `
    <div class="text-center text-muted py-5">
      <div class="spinner-border text-primary mb-3" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
      <p class="mb-0">Cargando información...</p>
    </div>`;

  let editarId = null;

  // ═══════════════════════════════════════════
  // MODAL DETALLE (carga vía AJAX)
  // ═══════════════════════════════════════════
  detalleModalEl.addEventListener('show.bs.modal', function (e) {
    const id = e.relatedTarget?.dataset?.id;
    if (!id) return;

    if (_usuariosAbortCtrl) _usuariosAbortCtrl.abort();
    _usuariosAbortCtrl = new AbortController();

    detalleBody.innerHTML = spinner;

    fetch(`/admin/usuarios/${id}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      signal: _usuariosAbortCtrl.signal
    })
      .then(r => {
        if (!r.ok) throw new Error('Error ' + r.status);
        return r.text();
      })
      .then(html => {
        detalleBody.innerHTML = html;
        const tooltips = detalleBody.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(el => new bootstrap.Tooltip(el));
      })
      .catch(err => {
        if (err.name === 'AbortError') return;
        detalleBody.innerHTML = `
          <div class="text-center text-danger py-5">
            <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
            <p class="mb-0">Error al cargar el detalle</p>
            <button class="btn btn-sm btn-outline-danger mt-2" onclick="location.reload()">
              <i class="fas fa-redo me-1"></i>Reintentar
            </button>
          </div>`;
      });
  });

  detalleModalEl.addEventListener('hidden.bs.modal', function () {
    if (_usuariosAbortCtrl) _usuariosAbortCtrl.abort();
    detalleBody.innerHTML = spinner;
  });

  // ═══════════════════════════════════════════
  // DELEGACIÓN: botón editar dentro del detalle
  // ═══════════════════════════════════════════
  detalleBody.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-editar');
    if (!btn) return;

    const datos = {
      id:           btn.dataset.id,
      email:        btn.dataset.email        ?? '',
      celular:      btn.dataset.celular      ?? '',
      direccion:    btn.dataset.direccion    ?? '',
      municipio_id: btn.dataset.municipioId  ?? '',
      rol_id:       btn.dataset.rolId        ?? '',
      estado:       btn.dataset.estado       ?? 'activo',
    };

    abrirEditar(datos);
  });

  // ═══════════════════════════════════════════
  // MODAL EDITAR (desde tabla o desde detalle)
  // ═══════════════════════════════════════════
  editarModalEl?.addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    if (!btn?.dataset?.id) return;

    if (!btn.classList.contains('btn-editar')) {
      const id = btn.dataset.id;

      if (_editarAbortCtrl) _editarAbortCtrl.abort();
      _editarAbortCtrl = new AbortController();

      fetch(`/admin/usuarios/${id}`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        signal: _editarAbortCtrl.signal
      })
        .then(r => {
          if (!r.ok) throw new Error('Error ' + r.status);
          return r.json();
        })
        .then(data => abrirEditar({
          id:           data.usuario_id,
          email:        data.email,
          celular:      data.celular,
          direccion:    data.direccion,
          municipio_id: data.municipio_id,
          rol_id:       data.rol_id,
          estado:       data.estado,
        }))
        .catch(err => {
          if (err.name === 'AbortError') return;
          alert('Error al cargar datos para editar');
        });
    }
  });

  window.abrirEditar = function (datos) {
    editarId = datos.id;

    document.getElementById('edit_email').value        = datos.email;
    document.getElementById('edit_celular').value      = datos.celular;
    document.getElementById('edit_direccion').value    = datos.direccion;
    document.getElementById('edit_municipio_id').value = datos.municipio_id;
    document.getElementById('edit_rol_id').value       = datos.rol_id;
    document.getElementById('edit_estado').value       = datos.estado;

    const detalleModal = bootstrap.Modal.getInstance(detalleModalEl);
    if (detalleModal) {
      const onHidden = function () {
        detalleModalEl.removeEventListener('hidden.bs.modal', onHidden);
        bootstrap.Modal.getOrCreateInstance(editarModalEl).show();
      };
      detalleModalEl.addEventListener('hidden.bs.modal', onHidden);
      detalleModal.hide();
    } else {
      bootstrap.Modal.getOrCreateInstance(editarModalEl).show();
    }
  };

  // ═══════════════════════════════════════════
  // GUARDAR CAMBIOS
  // ═══════════════════════════════════════════
  document.getElementById('btnGuardarUsuario')?.addEventListener('click', function () {
    if (!editarId) return;

    const btn = this;
    const textoOriginal = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

    if (_guardarAbortCtrl) _guardarAbortCtrl.abort();
    _guardarAbortCtrl = new AbortController();

    fetch(`/admin/usuarios/${editarId}`, {
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
      signal: _guardarAbortCtrl.signal,
      redirect: 'manual'
    })
      .then(r => {
        if (r.type === 'opaqueredirect' || r.status === 0) {
          window.location.reload();
          throw new Error('__skip');
        }
        if (r.status === 419 || r.status === 401) {
          window.location.reload();
          throw new Error('__skip');
        }
        if (!r.ok) {
          return r.json().then(
            err => {
              const msg = err.message || Object.values(err.errors || {}).flat().join('\n') || 'Error del servidor';
              throw new Error(msg);
            },
            () => { throw new Error('Error del servidor (' + r.status + ')'); }
          );
        }
        return r.json();
      })
      .then(data => {
        if (data.ok || data.success) {
          bootstrap.Modal.getInstance(editarModalEl)?.hide();
          mostrarToast('Cambios guardados correctamente', 'success');
          setTimeout(() => window.location.reload(), 800);
        } else {
          throw new Error(data.message || 'Error al guardar');
        }
      })
      .catch(err => {
        if (err.message === '__skip') return;
        alert(err.message || 'Error al guardar. Intenta de nuevo.');
      })
      .finally(() => {
        btn.disabled = false;
        btn.innerHTML = textoOriginal;
      });
  });

  function mostrarToast(mensaje, tipo = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:300px;';
    toast.innerHTML = `
      <i class="fas fa-${tipo === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
      ${mensaje}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
  }
}

// ═══════════════════════════════════════════════
// Casilleros modal (data attributes)
// ═══════════════════════════════════════════════
function initCasillerosModal() {
  const modal = document.getElementById('modalDetalle');
  if (!modal) return;

  if (modal.parentElement !== document.body) {
    const previo = document.body.querySelector(':scope > #modalDetalle');
    if (previo && previo !== modal) previo.remove();
    document.body.appendChild(modal);
  }

  if (modal._casillerosInit) return;
  modal._casillerosInit = true;

  modal.addEventListener('show.bs.modal', event => {
    const box = event.relatedTarget;
    if (!box) return;

    document.getElementById('detalleCodigo').textContent = box.dataset.codigo;
    document.getElementById('detalleEstado').textContent = box.dataset.estado;
    document.getElementById('detallePersona').textContent = box.dataset.persona || 'Libre';
    document.getElementById('detalleActividad').textContent = box.dataset.actividad || '—';
    document.getElementById('detalleHora').textContent = box.dataset.hora || '—';
  });
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
  initAccesosModal();
  initUsuariosModals();
  initCasillerosModal();
  initAjustesCascade();
  initActividadesData();
  initLoginPasswordToggle();
  initAlertas();
  initReloj();
});

// ═══════════════════════════════════════════════
// Turbo cleanup: dispose modals + abort pending fetches
// ═══════════════════════════════════════════════
document.addEventListener('turbo:before-render', () => {
  // Dispose all active Bootstrap modal instances
  document.querySelectorAll('.modal.show').forEach(modalEl => {
    const instance = bootstrap.Modal.getInstance(modalEl);
    if (instance) instance.dispose();
  });

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
