import { Calendar } from '@fullcalendar/core'
import dayGridPlugin     from '@fullcalendar/daygrid'
import listPlugin        from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import { Modal }         from 'bootstrap'

// ═══════════════════════════════════════════════
// Configuración
// ═══════════════════════════════════════════════

const COLORES = {
    success:   '#28a745',
    info:      '#17a2b8',
    primary:   '#007bff',
    secondary: '#6c757d',
    warning:   '#ffc107',
    danger:    '#dc3545',
}

const ROUTE_CREATE = window.routeCrear
const ROUTE_UPDATE = window.routeActualizar
const ROUTE_DELETE = window.routeEliminar

let calendarInstance = null
let resizeHandler = null

// ═══════════════════════════════════════════════
// Bootstrap (Turbo-compatible)
// ═══════════════════════════════════════════════

export function initCalendar() {
    // Destruir instancia anterior si existe (al navegar con Turbo)
    if (calendarInstance) {
        calendarInstance.destroy()
        calendarInstance = null
    }

    // ── Referencias DOM ───────────────────────────
    const calendarEl = document.getElementById('calendar')
    if (!calendarEl) {
        return  // ← Sale silenciosamente si no hay calendario
    }

    const modalEl = document.getElementById('modalActividad')
    if (!modalEl) {
        console.warn('Modal de actividad no encontrado')
        return
    }
    const formActividad = document.getElementById('formActividad')
    const formEliminar  = document.getElementById('formEliminar')
    const wrapEliminar  = document.getElementById('wrapEliminar')
    const btnEliminar   = document.getElementById('btnEliminar')

    const modal = Modal.getOrCreateInstance(modalEl)
    const csrfToken = document.querySelector('meta[name=csrf-token]')?.content ?? ''

    // ═══════════════════════════════════════════════
    // Helpers de modal
    // ═══════════════════════════════════════════════

    function modoCrear() {
        formActividad.action = ROUTE_CREATE
        document.getElementById('formMethod').value = 'POST'

        // Limpiar _method si existe
        const methodInput = formActividad.querySelector('input[name="_method"]')
        if (methodInput) methodInput.remove()

        formActividad.reset()

        document.getElementById('modalTitulo').textContent = 'Nueva actividad programada'
        document.getElementById('modalIcon').className     = 'fas fa-calendar-plus me-2 text-primary'
        document.getElementById('btnTexto').textContent    = 'Crear actividad'
        document.getElementById('btnIcon').className       = 'fas fa-calendar-plus me-1'

        wrapEliminar?.classList.add('d-none')
        document.getElementById('wrapEstado')?.classList.add('d-none')

        const estadoField = formActividad.querySelector('[name=estado]')
        if (estadoField) estadoField.value = ''

        const hoy = new Date().toISOString().split('T')[0]
        formActividad.querySelector('[name=fecha_inicio]').value = hoy
        formActividad.querySelector('[name=fecha_fin]').value    = hoy
        formActividad.querySelector('[name=hora_inicio]').value  = '08:00'
        formActividad.querySelector('[name=hora_fin]').value     = '10:00'
    }

    function modoEditar(evento) {
        const props = evento.extendedProps

        document.getElementById('wrapEstado')?.classList.remove('d-none')

        // Para PUT, cambiamos action y agregamos _method
        formActividad.action = ROUTE_UPDATE.replace('__ID__', evento.id)
        document.getElementById('formMethod').value = 'PUT'

        // Asegurar que existe _method=PUT
        let methodInput = formActividad.querySelector('input[name="_method"]')
        if (!methodInput) {
            methodInput = document.createElement('input')
            methodInput.type = 'hidden'
            methodInput.name = '_method'
            formActividad.appendChild(methodInput)
        }
        methodInput.value = 'PUT'

        document.getElementById('modalTitulo').textContent = 'Editar actividad'
        document.getElementById('modalIcon').className     = 'fas fa-edit me-2 text-warning'
        document.getElementById('btnTexto').textContent    = 'Guardar cambios'
        document.getElementById('btnIcon').className       = 'fas fa-save me-1'

        formActividad.querySelector('[name=nombre]').value            = evento.title
        formActividad.querySelector('[name=descripcion]').value       = props.descripcion        ?? ''
        formActividad.querySelector('[name=tipo_actividad_id]').value = props.tipo_actividad_id  ?? ''
        formActividad.querySelector('[name=locacion_id]').value       = props.locacion_id        ?? ''
        formActividad.querySelector('[name=fecha_inicio]').value      = evento.startStr.split('T')[0]
        formActividad.querySelector('[name=fecha_fin]').value         = props.fecha_fin          ?? evento.startStr.split('T')[0]
        formActividad.querySelector('[name=hora_inicio]').value       = props.hora_inicio?.slice(0, 5) ?? '08:00'
        formActividad.querySelector('[name=hora_fin]').value          = props.hora_fin?.slice(0, 5)    ?? '10:00'
        formActividad.querySelector('[name=estado]').value            = props.estado_db ?? 'pendiente'

        // Guardar ID para eliminar
        formEliminar.action = ROUTE_DELETE.replace('__ID__', evento.id)
        wrapEliminar?.classList.remove('d-none')
    }

    // ═══════════════════════════════════════════════
    // Listeners
    // ═══════════════════════════════════════════════

    document.querySelector('[data-bs-target="#modalActividad"]')
        ?.addEventListener('click', (e) => {
            e.preventDefault()
            modoCrear()
            modal.show()
        })

    // Eliminar con fetch (NO form anidado)
    btnEliminar?.addEventListener('click', (e) => {
        e.preventDefault()
        e.stopPropagation()

        const eventoId = formActividad.dataset.eventoId
        if (!eventoId) return

        if (!confirm('¿Cancelar esta actividad? Esta acción no se puede deshacer.')) return

        fetch(ROUTE_DELETE.replace('__ID__', eventoId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ _method: 'DELETE' })
        })
            .then(r => {
                if (!r.ok) throw new Error('Error al eliminar')
                return r.json()
            })
            .then(() => {
                modal.hide()
                calendar.refetchEvents?.() || location.reload()
            })
            .catch(err => {
                alert(err.message)
                console.error(err)
            })
    })

    // ═══════════════════════════════════════════════
    // FullCalendar
    // ═══════════════════════════════════════════════

    const isMobile   = () => window.innerWidth < 768
    const getView    = () => isMobile() ? 'listMonth' : 'dayGridMonth'
    const getToolbar = () => ({
        left:   'prev,next today',
        center: 'title',
        right:  isMobile() ? 'listMonth' : 'dayGridMonth,listMonth',
    })

    const calendar = new Calendar(calendarEl, {
        plugins:  [dayGridPlugin, listPlugin, interactionPlugin],
        locale:   'es',
        initialView:        getView(),
        headerToolbar:      getToolbar(),
        handleWindowResize: false,
        height:             750,
        contentHeight:      700,
        stickyHeaderDates:  true,

        views: {
            dayGridMonth: { dayMaxEventRows: 3 },
            listMonth:    { noEventsText: 'Sin actividades este mes', displayEventTime: false },
        },

        events: window.calendarEvents,

        eventClick(info) {
            modoEditar(info.event)
            modal.show()
        },

        eventContent(info) {
            const { color, hora_inicio, hora_fin, estado, locacion } = info.event.extendedProps
            const bg = COLORES[color] ?? COLORES.primary

            if (info.view.type === 'listMonth') {
                return {
                    html: `
                        <div style="color:${bg}; pointer-events:none;">
                            <div style="font-weight:600;">${info.event.title}</div>
                            <small style="opacity:.75;">
                                🕐 ${hora_inicio} - ${hora_fin}
                                ${locacion ? ` · 📍 ${locacion}` : ''}
                                · ${estado}
                            </small>
                        </div>`
                }
            }

            return {
                html: `
                    <div style="
                        background:${bg}; border-radius:6px; padding:4px 6px;
                        color:#fff; width:100%; box-sizing:border-box;
                        overflow:hidden; box-shadow:0 1px 2px rgba(0,0,0,.15);
                        pointer-events:none;
                    ">
                        <div style="
                            font-size:.72rem; font-weight:600; line-height:1.2;
                            display:-webkit-box; -webkit-line-clamp:2;
                            -webkit-box-orient:vertical; overflow:hidden; word-break:break-word;
                        ">${info.event.title}</div>
                        <div style="
                            margin-top:3px; font-size:.63rem; opacity:.9;
                            display:flex; align-items:center; gap:4px;
                            white-space:nowrap; overflow:hidden;
                        ">
                            <span>🕐</span>
                            <span>${hora_inicio.slice(0,5)} - ${hora_fin.slice(0,5)}</span>
                        </div>
                    </div>`
            }
        },
    })

    try {
        calendar.render()
    } finally {
        calendarInstance = calendar
        // Ocultar spinner
        const loadingEl = document.getElementById('calendar-loading')
        if (loadingEl) loadingEl.remove()
        // Mostrar calendario y forzar recálculo de dimensiones
        requestAnimationFrame(() => {
            calendarEl.style.visibility = 'visible'
            calendar.updateSize()
        })
    }

    // Limpiar listener anterior para evitar leaks entre navegaciones Turbo
    if (resizeHandler) window.removeEventListener('resize', resizeHandler)
    let resizeTimer = null
    resizeHandler = () => {
        clearTimeout(resizeTimer)
        resizeTimer = setTimeout(() => {
            const view = getView()
            if (calendar.view.type !== view) calendar.changeView(view)
            calendar.setOption('headerToolbar', getToolbar())
        }, 200)
    }
    window.addEventListener('resize', resizeHandler)
}

export function destroyCalendar() {
    if (calendarInstance) {
        calendarInstance.destroy()
        calendarInstance = null
    }
}
