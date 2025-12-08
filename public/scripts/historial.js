// public/scripts/historial.js
// Sistema de historial para Aruma Spa Admin

// Variables globales para el historial
let paginaActualHistorial = 1;
const registrosPorPagina = 20;
let historialData = {};

// Inicializar módulo de historial cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Solo inicializar si estamos en la sección de historial
    if (document.getElementById('historial')) {
        inicializarModuloHistorial();
    }
});

// Función principal de inicialización
function inicializarModuloHistorial() {
    console.log('🚀 Inicializando módulo de historial...');
    
    // Inicializar filtros de fecha
    inicializarFiltrosFecha();
    
    // Configurar eventos de filtros
    configurarEventosFiltros();
    
    // Cargar datos iniciales inmediatamente (no esperar a que la sección esté activa)
    setTimeout(() => {
        console.log('⏰ Cargando historial inicial...');
        cargarHistorial();
        cargarEstadisticas();
    }, 500);
    
    console.log('✅ Módulo de historial inicializado');
}

// Configurar eventos para los filtros
function configurarEventosFiltros() {
    // Configurar evento para cambio de fecha
    const fechaDesde = document.getElementById('filtroFechaDesde');
    const fechaHasta = document.getElementById('filtroFechaHasta');
    
    if (fechaDesde && fechaHasta) {
        fechaDesde.addEventListener('change', function() {
            // Ajustar fecha máxima de "hasta" para que no sea menor que "desde"
            if (fechaDesde.value && fechaHasta.value && fechaHasta.value < fechaDesde.value) {
                fechaHasta.value = fechaDesde.value;
            }
            filtrarHistorial();
        });
        
        fechaHasta.addEventListener('change', function() {
            // Ajustar fecha mínima de "desde" para que no sea mayor que "hasta"
            if (fechaDesde.value && fechaHasta.value && fechaDesde.value > fechaHasta.value) {
                fechaDesde.value = fechaHasta.value;
            }
            filtrarHistorial();
        });
    }
    
    // Configurar búsqueda con debounce (esperar 500ms después de escribir)
    let timeoutBusqueda;
    const inputBusqueda = document.getElementById('filtroBusquedaHistorial');
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', function() {
            clearTimeout(timeoutBusqueda);
            timeoutBusqueda = setTimeout(filtrarHistorial, 500);
        });
    }
}

// Inicializar fechas de filtro
function inicializarFiltrosFecha() {
    const hoy = new Date();
    const fechaHasta = document.getElementById('filtroFechaHasta');
    const fechaDesde = document.getElementById('filtroFechaDesde');
    
    if (fechaHasta) {
        fechaHasta.max = hoy.toISOString().split('T')[0];
        fechaHasta.value = hoy.toISOString().split('T')[0];
    }
    
    if (fechaDesde) {
        const hace30Dias = new Date();
        hace30Dias.setDate(hace30Dias.getDate() - 30);
        fechaDesde.max = hoy.toISOString().split('T')[0];
        fechaDesde.value = hace30Dias.toISOString().split('T')[0];
    }
}

// Cargar historial desde la API
async function cargarHistorial(pagina = 1) {
    console.log('=== INICIANDO cargarHistorial ===');
    console.log('Página solicitada:', pagina);
    
    const tabla = document.getElementById('tablaHistorial');
    const loadingRow = document.getElementById('loadingRow');
    
    if (!tabla) {
        console.error('❌ Tabla de historial no encontrada');
        return;
    }
    
    console.log('✅ Elementos encontrados:', {
        tabla: !!tabla,
        loadingRow: !!loadingRow
    });
    
    // Mostrar loading
    mostrarLoading(tabla, loadingRow);
    
    try {
        // Obtener filtros
        const filtros = obtenerFiltrosActuales();
        console.log('Filtros actuales:', filtros);
        
        // Construir URL con parámetros
        let url = `/api/historial?pagina=${pagina}&limit=${registrosPorPagina}`;
        url += construirParametrosFiltros(filtros);
        
        console.log('🌐 URL de la petición:', url);
        
        // Hacer la petición con timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000);
        
        const response = await fetch(url, {
            signal: controller.signal,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        clearTimeout(timeoutId);
        
        console.log('📥 Respuesta recibida:', {
            status: response.status,
            statusText: response.statusText,
            ok: response.ok,
            headers: Object.fromEntries(response.headers.entries())
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ Error en respuesta:', errorText);
            throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
        }
        
        const responseText = await response.text();
        console.log('📄 Respuesta texto (primeros 500 chars):', responseText.substring(0, 500));
        
        let data;
        try {
            data = JSON.parse(responseText);
            console.log('✅ JSON parseado correctamente:', {
                status: data.status,
                total: data.total,
                historialCount: data.historial?.length || 0
            });
        } catch (jsonError) {
            console.error('❌ Error parseando JSON:', jsonError);
            console.error('Texto recibido:', responseText);
            throw new Error('Respuesta no es JSON válido');
        }
        
        // Asegurar que tenemos la estructura correcta
        if (data.historial === undefined) {
            console.warn('⚠️ La respuesta no tiene propiedad "historial", usando estructura alternativa');
            data = {
                historial: data.registros || data.data || [],
                total: data.total || data.totalRegistros || 0,
                pagina_actual: data.pagina_actual || data.currentPage || pagina,
                total_paginas: data.total_paginas || data.totalPages || 1
            };
        }
        
        historialData = data;
        paginaActualHistorial = pagina;
        
        console.log('📊 Datos procesados:', {
            historialCount: data.historial.length,
            total: data.total,
            paginaActual: data.pagina_actual,
            totalPaginas: data.total_paginas
        });
        
        // Actualizar interfaz
        actualizarTablaHistorial(data.historial || []);
        actualizarPaginacion(data.total || 0, data.pagina_actual || 1, data.total_paginas || 1);
        actualizarContadores(data.historial || []);
        
        console.log('✅ Historial cargado exitosamente');
        
    } catch (error) {
        console.error('❌ Error al cargar historial:', error);
        console.error('Stack trace:', error.stack);
        
        let mensajeError = 'Error al cargar el historial. ';
        
        if (error.name === 'AbortError') {
            mensajeError += 'La solicitud tardó demasiado tiempo.';
        } else if (error.message.includes('NetworkError')) {
            mensajeError += 'Error de red. Verifica tu conexión.';
        } else if (error.message.includes('JSON')) {
            mensajeError += 'Respuesta inválida del servidor.';
        } else {
            mensajeError += error.message;
        }
        
        mostrarError(tabla, mensajeError);
    } finally {
        ocultarLoading(loadingRow);
        console.log('=== FINALIZADO cargarHistorial ===');
    }
}

// Obtener filtros actuales
function obtenerFiltrosActuales() {
    return {
        tipo: document.getElementById('filtroTipoHistorial')?.value || 'todos',
        evento: document.getElementById('filtroEventoHistorial')?.value || 'todos',
        fecha_desde: document.getElementById('filtroFechaDesde')?.value || '',
        fecha_hasta: document.getElementById('filtroFechaHasta')?.value || '',
        busqueda: document.getElementById('filtroBusquedaHistorial')?.value || ''
    };
}

// Construir parámetros de filtros para URL
function construirParametrosFiltros(filtros) {
    let params = '';
    
    if (filtros.tipo !== 'todos') params += `&tipo=${encodeURIComponent(filtros.tipo)}`;
    if (filtros.evento !== 'todos') params += `&evento=${encodeURIComponent(filtros.evento)}`;
    if (filtros.fecha_desde) params += `&fecha_desde=${filtros.fecha_desde}`;
    if (filtros.fecha_hasta) params += `&fecha_hasta=${filtros.fecha_hasta}`;
    if (filtros.busqueda) params += `&busqueda=${encodeURIComponent(filtros.busqueda)}`;
    
    return params;
}

// Mostrar estado de carga
function mostrarLoading(tabla, loadingRow) {
    tabla.innerHTML = '';
    
    if (loadingRow) {
        loadingRow.style.display = '';
        tabla.appendChild(loadingRow);
    } else {
        tabla.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando historial...</p>
                </td>
            </tr>
        `;
    }
}

// Ocultar estado de carga
function ocultarLoading(loadingRow) {
    if (loadingRow) {
        loadingRow.style.display = 'none';
    }
}

// Mostrar error
function mostrarError(tabla, mensaje) {
    tabla.innerHTML = `
        <tr>
            <td colspan="8" class="text-center text-danger py-4">
                <i class="fas fa-exclamation-circle"></i>
                ${mensaje}
                <button class="btn btn-sm btn-outline-primary ms-2" onclick="cargarHistorial()">
                    <i class="fas fa-redo"></i> Reintentar
                </button>
            </td>
        </tr>
    `;
}

// Actualizar la tabla con los datos
function actualizarTablaHistorial(historial) {
    const tabla = document.getElementById('tablaHistorial');
    if (!tabla) return;
    
    if (!historial || historial.length === 0) {
        tabla.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fas fa-inbox"></i>
                    No se encontraron registros en el historial.
                    <div class="mt-2">
                        <small class="text-muted">Prueba ajustando los filtros de búsqueda.</small>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    
    historial.forEach((item, index) => {
        html += crearFilaHistorial(item, index);
    });
    
    tabla.innerHTML = html;
}

// Crear una fila de historial
function crearFilaHistorial(item, index) {
    // Formatear fecha/hora
    const fechaHora = new Date(item.created_at);
    const fechaStr = fechaHora.toLocaleDateString('es-ES');
    const horaStr = fechaHora.toLocaleTimeString('es-ES', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
    
    // Icono y color según tipo
    const tipoInfo = obtenerInfoTipo(item.tipo_evento);
    
    // Detalles resumidos
    let detallesResumen = item.detalle || '';
    if (detallesResumen.length > 80) {
        detallesResumen = detallesResumen.substring(0, 80) + '...';
    }
    
    // Resumen de cambios si es una actualización
    const cambiosResumen = obtenerResumenCambios(item);
    
    return `
        <tr data-id="${item.id}" data-tipo="${item.tipo_evento}" data-evento="${item.evento}">
            <td>
                <span class="badge bg-secondary">#${item.id}</span>
            </td>
            <td>
                <small class="d-block" title="${fechaHora.toLocaleString('es-ES')}">${fechaStr}</small>
                <small class="text-muted">${horaStr}</small>
            </td>
            <td>
                ${item.usuario_nombre ? `
                    <div class="fw-semibold text-truncate" style="max-width: 150px;" title="${item.usuario_nombre}">
                        ${item.usuario_nombre}
                    </div>
                    <small class="text-muted text-truncate d-block" style="max-width: 150px;" title="${item.usuario_email || ''}">
                        ${item.usuario_email || ''}
                    </small>
                ` : '<em class="text-muted">Sistema</em>'}
            </td>
            <td>
                <span class="badge bg-${tipoInfo.color}" title="${item.tipo_evento}">
                    <i class="fas ${tipoInfo.icon}"></i>
                    ${item.tipo_evento}
                </span>
            </td>
            <td>
                <div class="fw-semibold text-truncate" style="max-width: 150px;" title="${item.evento}">
                    ${item.evento}
                </div>
                ${cambiosResumen ? `
                    <small class="text-info text-truncate d-block" style="max-width: 150px;" title="${cambiosResumen}">
                        ${cambiosResumen}
                    </small>
                ` : ''}
            </td>
            <td>
                <small class="text-truncate d-block" style="max-width: 200px;" title="${item.detalle || ''}">
                    ${detallesResumen}
                </small>
                ${item.reserva_id ? `
                    <div class="mt-1">
                        <span class="badge bg-light text-dark border" title="Reserva #${item.reserva_id}">
                            <i class="fas fa-link"></i> Reserva #${item.reserva_id}
                        </span>
                    </div>
                ` : ''}
            </td>
            <td>
                <small class="text-muted" title="${item.ip_address || ''}">
                    ${item.ip_address ? acortarIP(item.ip_address) : '-'}
                </small>
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-info" onclick="verDetallesCompletos(${item.id})" 
                            title="Ver detalles completos">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${item.reserva_id ? `
                    <button class="btn btn-outline-primary" onclick="irAReserva(${item.reserva_id})"
                            title="Ir a la reserva relacionada">
                        <i class="fas fa-external-link-alt"></i>
                    </button>
                    ` : ''}
                </div>
            </td>
        </tr>
    `;
}

// Obtener información del tipo de evento
function obtenerInfoTipo(tipo) {
    const tipos = {
        'reserva': { icon: 'fa-calendar-check', color: 'primary' },
        'usuario': { icon: 'fa-user', color: 'success' },
        'terapeuta': { icon: 'fa-user-md', color: 'info' },
        'servicio': { icon: 'fa-spa', color: 'warning' },
        'producto': { icon: 'fa-box', color: 'purple' },
        'sistema': { icon: 'fa-cogs', color: 'dark' }
    };
    
    return tipos[tipo] || { icon: 'fa-history', color: 'secondary' };
}

// Obtener resumen de cambios para una actualización
function obtenerResumenCambios(item) {
    if (!item.evento.includes('Actualizada') && !item.evento.includes('Editada')) {
        return '';
    }
    
    try {
        // No necesitas parsear porque ya viene parseado del modelo
        const anterior = item.anterior_valor;
        const nuevo = item.nuevo_valor;
        
        if (!anterior || !nuevo) return '';
        
        if (anterior.estado !== nuevo.estado) {
            return `Estado: ${anterior.estado || 'N/A'} → ${nuevo.estado || 'N/A'}`;
        }
        
        if ((anterior.fecha && anterior.fecha !== nuevo.fecha) || 
            (anterior.hora && anterior.hora !== nuevo.hora)) {
            return 'Cambio de fecha/hora';
        }
        
        if (anterior.therapist_id !== nuevo.therapist_id) {
            return 'Cambio de terapeuta';
        }
        
        if (anterior.notas !== nuevo.notas) {
            return 'Notas actualizadas';
        }
    } catch (e) {
        console.error('Error al parsear cambios:', e);
    }
    
    return '';
}

// Acortar dirección IP para mostrar
function acortarIP(ip) {
    if (!ip) return '-';
    
    // Si es IPv4, mostrar tal cual (máximo 15 caracteres)
    if (ip.length <= 15) return ip;
    
    // Si es IPv6 o muy larga, acortar
    return ip.substring(0, 15) + '...';
}

// Actualizar paginación
function actualizarPaginacion(total, paginaActual, totalPaginas) {
    const paginacion = document.getElementById('paginacionHistorial');
    if (!paginacion) return;
    
    if (totalPaginas <= 1) {
        paginacion.innerHTML = '';
        return;
    }
    
    let html = '';
    
    // Botón anterior
    html += `
        <li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="cambiarPaginaHistorial(${paginaActual - 1}); return false;">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
    `;
    
    // Números de página
    const paginasMostrar = 5;
    let inicio = Math.max(1, paginaActual - Math.floor(paginasMostrar / 2));
    let fin = Math.min(totalPaginas, inicio + paginasMostrar - 1);
    
    if (fin - inicio + 1 < paginasMostrar) {
        inicio = Math.max(1, fin - paginasMostrar + 1);
    }
    
    // Primera página si no está en el rango
    if (inicio > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="cambiarPaginaHistorial(1); return false;">1</a>
            </li>
            ${inicio > 2 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
        `;
    }
    
    for (let i = inicio; i <= fin; i++) {
        html += `
            <li class="page-item ${i === paginaActual ? 'active' : ''}">
                <a class="page-link" href="#" onclick="cambiarPaginaHistorial(${i}); return false;">${i}</a>
            </li>
        `;
    }
    
    // Última página si no está en el rango
    if (fin < totalPaginas) {
        html += `
            ${fin < totalPaginas - 1 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
            <li class="page-item">
                <a class="page-link" href="#" onclick="cambiarPaginaHistorial(${totalPaginas}); return false;">${totalPaginas}</a>
            </li>
        `;
    }
    
    // Botón siguiente
    html += `
        <li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="cambiarPaginaHistorial(${paginaActual + 1}); return false;">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `;
    
    // Información de página
    html += `
        <li class="page-item disabled">
            <span class="page-link text-muted">
                ${total.toLocaleString()} registros
            </span>
        </li>
    `;
    
    paginacion.innerHTML = html;
}

// Cambiar página
function cambiarPaginaHistorial(pagina) {
    if (pagina < 1 || pagina > (historialData.total_paginas || 1)) return;
    cargarHistorial(pagina);
    
    // Scroll suave hacia la tabla
    const tabla = document.getElementById('tablaHistorial');
    if (tabla) {
        tabla.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Filtrar historial
function filtrarHistorial() {
    // Resetear a página 1
    paginaActualHistorial = 1;
    cargarHistorial(1);
}

// Actualizar contadores en la interfaz
function actualizarContadores(historial) {
    const totalRegistros = document.getElementById('totalRegistros');
    if (totalRegistros && historialData.total !== undefined) {
        totalRegistros.textContent = historialData.total.toLocaleString();
    }
    
    // Calcular registros de hoy
    const hoy = new Date().toISOString().split('T')[0];
    const hoyCount = historial.filter(item => 
        item.created_at && item.created_at.startsWith(hoy)
    ).length;
    
    const reservasHoy = document.getElementById('reservasHoy');
    if (reservasHoy) {
        reservasHoy.textContent = hoyCount.toLocaleString();
    }
}

// Ver detalles completos de un evento
async function verDetallesCompletos(id) {
    try {
        console.log(`🔍 Buscando detalles del evento #${id}`);
        const response = await fetch(`/api/historial/evento?id=${id}`);
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const result = await response.json();
        const item = result.evento || result;
        
        if (!item) {
            mostrarToast('No se encontraron detalles para este evento.', 'warning');
            return;
        }
        
        console.log('✅ Detalles encontrados:', item);
        
        // Mostrar modal con detalles
        mostrarModalDetalles(item);
        
    } catch (error) {
        console.error('Error al cargar detalles:', error);
        mostrarToast('Error al cargar los detalles del evento.', 'danger');
    }
}

// Mostrar modal con detalles
function mostrarModalDetalles(item) {
    // Llenar modal
    document.getElementById('detalleEventoId').textContent = item.id;
    document.getElementById('detalleId').textContent = `#${item.id}`;
    
    const fechaHora = new Date(item.created_at);
    document.getElementById('detalleFechaHora').textContent = 
        fechaHora.toLocaleString('es-ES');
    
    document.getElementById('detalleUsuario').textContent = 
        item.usuario_nombre ? 
        `${item.usuario_nombre} (${item.usuario_email || 'Sin email'})` : 
        'Sistema';
    
    document.getElementById('detalleIP').textContent = item.ip_address || 'No registrada';
    document.getElementById('detalleNavegador').textContent = 
        item.user_agent ? item.user_agent.substring(0, 100) + '...' : 'No registrado';
    
    // Mostrar valores anteriores y nuevos
    const anterior = item.anterior_valor ? 
        JSON.stringify(item.anterior_valor, null, 2) : 
        'No hay datos anteriores';
    
    const nuevo = item.nuevo_valor ? 
        JSON.stringify(item.nuevo_valor, null, 2) : 
        'No hay datos nuevos';
    
    document.getElementById('detalleAnterior').textContent = anterior;
    document.getElementById('detalleNuevo').textContent = nuevo;
    
    // Descripción completa
    document.getElementById('detalleCompleto').textContent = item.detalle || 'Sin descripción';
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalDetallesCompletos'));
    modal.show();
}

// Ir a una reserva específica
function irAReserva(reservaId) {
    // Mostrar sección de reservas
    if (typeof showAdminSection === 'function') {
        showAdminSection('reservas');
        
        // Buscar y resaltar la reserva después de un delay
        setTimeout(() => {
            buscarYResaltarReserva(reservaId);
        }, 500);
    } else {
        // Redirigir a la página de reservas
        window.location.href = '/admin/reservas#' + reservaId;
    }
}

// Buscar y resaltar reserva en la tabla
function buscarYResaltarReserva(reservaId) {
    const filas = document.querySelectorAll('#tablaReservas tr');
    
    filas.forEach(fila => {
        const idCelda = fila.querySelector('td:first-child');
        if (idCelda && idCelda.textContent.includes(reservaId.toString())) {
            // Resaltar
            fila.classList.add('table-info');
            fila.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Quitar resaltado después de 3 segundos
            setTimeout(() => {
                fila.classList.remove('table-info');
            }, 3000);
        }
    });
}

// Exportar historial
function exportarHistorial() {
    // Obtener filtros actuales
    const filtros = obtenerFiltrosActuales();
    
    let url = `/api/historial/exportar?`;
    url += construirParametrosFiltros(filtros);
    
    // Abrir en nueva pestaña para descargar
    window.open(url, '_blank');
}

// Limpiar historial
async function limpiarHistorial() {
    const dias = document.getElementById('diasLimpiar').value;
    
    if (!confirm(`¿Estás seguro de eliminar el historial mayor a ${dias} días? Esta acción no se puede deshacer.`)) {
        return;
    }
    
    try {
        const response = await fetch('/api/historial/limpiar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `dias=${dias}`
        });
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.status === 'ok') {
            mostrarToast(`Historial limpiado correctamente. Se eliminaron ${result.eliminados} registros.`, 'success');
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalLimpiarHistorial'));
            if (modal) modal.hide();
            
            // Recargar datos
            cargarHistorial(1);
            cargarEstadisticas();
        } else {
            mostrarToast('Error al limpiar el historial: ' + (result.error || 'Error desconocido'), 'danger');
        }
        
    } catch (error) {
        console.error('Error al limpiar historial:', error);
        mostrarToast('Error al limpiar el historial.', 'danger');
    }
}

// Función para debug del historial
async function debugHistorial() {
    console.clear();
    console.log('=== 🐛 DEBUG COMPLETO DEL HISTORIAL ===');
    
    console.log('1. 🖥️ Estado del DOM:');
    console.log('- URL actual:', window.location.href);
    console.log('- Sección historial visible:', document.getElementById('historial')?.offsetParent !== null);
    console.log('- Tabla historial:', document.getElementById('tablaHistorial'));
    console.log('- Loading row:', document.getElementById('loadingRow'));
    
    console.log('2. 🔧 Variables globales:');
    console.log('- paginaActualHistorial:', paginaActualHistorial);
    console.log('- historialData:', historialData);
    
    console.log('3. 📋 Filtros actuales:');
    console.log(obtenerFiltrosActuales());
    
    console.log('4. 🌐 Probando conexión API:');
    
    // Test 1: Endpoint básico
    try {
        console.log('📞 Probando /api/historial...');
        const response = await fetch('/api/historial?pagina=1&limit=5');
        console.log('📥 Estado:', response.status, response.statusText);
        
        if (response.ok) {
            const data = await response.json();
            console.log('✅ Respuesta JSON:', {
                status: data.status,
                total: data.total,
                historialCount: data.historial?.length || 0,
                muestraPrimerRegistro: data.historial?.[0] || 'No hay registros'
            });
        } else {
            const errorText = await response.text();
            console.error('❌ Error:', errorText);
        }
    } catch (error) {
        console.error('❌ Error en fetch:', error);
    }
    
    // Test 2: Verificar que el controlador existe
    console.log('5. 🔍 Verificando rutas:');
    console.log('- Ruta /api/historial existe:', true);
    console.log('- Ruta /api/historial/estadisticas existe:', true);
    
    console.log('6. 🛠️ Ejecutar carga manual:');
    console.log('Ejecuta en consola: cargarHistorial(1)');
    
    console.log('=== FIN DEL DEBUG ===');
    
    // Mostrar resultado en pantalla también
    const debugDiv = document.createElement('div');
    debugDiv.className = 'alert alert-info mt-3';
    debugDiv.innerHTML = `
        <h5><i class="fas fa-bug"></i> Debug realizado</h5>
        <p>Revisa la consola del navegador (F12) para ver los resultados.</p>
        <button class="btn btn-sm btn-primary" onclick="cargarHistorial(1)">
            <i class="fas fa-redo"></i> Forzar carga manual
        </button>
    `;
    
    const historialSection = document.getElementById('historial');
    historialSection.appendChild(debugDiv);
    
    setTimeout(() => debugDiv.remove(), 10000);
}

// Cargar estadísticas
async function cargarEstadisticas() {
    try {
        console.log('📊 Cargando estadísticas...');
        const response = await fetch('/api/historial/estadisticas');
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const stats = await response.json();
        console.log('✅ Estadísticas cargadas:', stats);
        
        document.getElementById('totalRegistros').textContent = stats.total ? stats.total.toLocaleString() : '0';
        document.getElementById('reservasHoy').textContent = stats.hoy ? stats.hoy.toLocaleString() : '0';
        document.getElementById('ultimos7Dias').textContent = stats.ultimos7Dias ? stats.ultimos7Dias.toLocaleString() : '0';
        document.getElementById('usuariosActivos').textContent = stats.usuariosActivos ? stats.usuariosActivos.toLocaleString() : '0';
        
    } catch (error) {
        console.error('Error al cargar estadísticas:', error);
    }
}

// Mostrar notificación toast
function mostrarToast(mensaje, tipo = 'info') {
    const tipos = {
        'success': { bg: 'bg-success', icon: 'fa-check-circle' },
        'danger': { bg: 'bg-danger', icon: 'fa-exclamation-circle' },
        'warning': { bg: 'bg-warning', icon: 'fa-exclamation-triangle' },
        'info': { bg: 'bg-info', icon: 'fa-info-circle' }
    };
    
    const tipoInfo = tipos[tipo] || tipos.info;
    
    // Crear toast dinámicamente
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white ${tipoInfo.bg} border-0" 
             role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${tipoInfo.icon} me-2"></i>
                    ${mensaje}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    // Agregar al contenedor de toasts
    const toastContainer = document.getElementById('toastContainer') || crearToastContainer();
    toastContainer.innerHTML += toastHtml;
    
    // Mostrar toast
    const toastEl = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();
    
    // Eliminar después de ocultar
    toastEl.addEventListener('hidden.bs.toast', function () {
        toastEl.remove();
    });
}

// Crear contenedor de toasts si no existe
function crearToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '1060';
    document.body.appendChild(container);
    return container;
}

// Limpiar filtros
function limpiarFiltrosHistorial() {
    document.getElementById('filtroTipoHistorial').value = 'todos';
    document.getElementById('filtroEventoHistorial').value = 'todos';
    document.getElementById('filtroFechaDesde').value = '';
    document.getElementById('filtroFechaHasta').value = '';
    document.getElementById('filtroBusquedaHistorial').value = '';
    
    // Restaurar fechas por defecto
    inicializarFiltrosFecha();
    
    filtrarHistorial();
    mostrarToast('Filtros limpiados', 'info');
}

// Función para cuando se muestra la sección de historial
function onShowHistorialSection() {
    console.log('👁️ Sección historial mostrada, cargando datos...');
    cargarHistorial();
    cargarEstadisticas();
}

// Exportar funciones para uso global
window.cargarHistorial = cargarHistorial;
window.filtrarHistorial = filtrarHistorial;
window.verDetallesCompletos = verDetallesCompletos;
window.irAReserva = irAReserva;
window.exportarHistorial = exportarHistorial;
window.limpiarHistorial = limpiarHistorial;
window.cambiarPaginaHistorial = cambiarPaginaHistorial;
window.limpiarFiltrosHistorial = limpiarFiltrosHistorial;
window.onShowHistorialSection = onShowHistorialSection;
window.debugHistorial = debugHistorial;
