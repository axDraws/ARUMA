// ========================================
// FUNCIONES GENERALES
// ========================================

// Mostrar fecha actual
function mostrarFechaActual() {
    const fechaElement = document.getElementById('fecha-actual');
    if (fechaElement) {
        const hoy = new Date();
        const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        fechaElement.textContent = hoy.toLocaleDateString('es-ES', opciones);
    }
}

// Logout
function logout() {
    if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        alert('Sesión cerrada correctamente');
        window.location.href = 'index.html';
    }
}

// ========================================
// FUNCIONES VISTA CLIENTE
// ========================================

// Cambiar entre secciones del panel cliente
function showSection(sectionId) {
    // Ocultar todas las secciones
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => {
        section.classList.remove('active');
    });
    
    // Mostrar la sección seleccionada
    const targetSection = document.getElementById(sectionId);
    if (targetSection) {
        targetSection.classList.add('active');
    }
    
    // Actualizar menú activo
    const menuItems = document.querySelectorAll('.sidebar-menu li');
    menuItems.forEach(item => {
        item.classList.remove('active');
    });
    event.target.closest('li').classList.add('active');
}

// Editar reserva (cliente)
function editarReserva(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalEditarReserva'));
    modal.show();
    console.log('Editando reserva #' + id);
}

// Cancelar reserva
function cancelarReserva(id) {
    if (confirm('¿Estás seguro de que deseas cancelar esta reserva?')) {
        alert('Reserva #' + id + ' cancelada correctamente');
        // Aquí se eliminaría la tarjeta o se actualizaría el estado
    }
}

// Guardar cambios de edición
function guardarCambios() {
    alert('Cambios guardados correctamente');
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarReserva'));
    modal.hide();
}

// Manejar envío de nueva reserva (cliente)
const formNuevaReserva = document.getElementById('formNuevaReserva');
if (formNuevaReserva) {
    formNuevaReserva.addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Reserva creada exitosamente. Te enviaremos un correo de confirmación.');
        showSection('mis-reservas');
        formNuevaReserva.reset();
    });
}

// ========================================
// FUNCIONES VISTA ADMINISTRADOR
// ========================================

// Cambiar entre secciones del panel admin
function showAdminSection(sectionId) {
    // Ocultar todas las secciones
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => {
        section.classList.remove('active');
    });
    
    // Mostrar la sección seleccionada
    const targetSection = document.getElementById(sectionId);
    if (targetSection) {
        targetSection.classList.add('active');
    }
    
    // Actualizar menú activo
    const menuItems = document.querySelectorAll('.sidebar-menu li');
    menuItems.forEach(item => {
        item.classList.remove('active');
    });
    
    // Buscar el elemento li que fue clickeado
    const clickedItem = event.target.closest('li');
    if (clickedItem) {
        clickedItem.classList.add('active');
    }
}

// Ver detalles de reserva
function verDetalles(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
    if (modal) {
        modal.show();
    }
    console.log('Viendo detalles de reserva #' + id);
}

// Editar reserva (admin)
function editarReservaAdmin(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalEditarReservaAdmin'));
    if (modal) {
        modal.show();
    }
    console.log('Editando reserva #' + id + ' (Admin)');
}

// Eliminar reserva (admin)
function eliminarReserva(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta reserva? Esta acción no se puede deshacer.')) {
        alert('Reserva #' + id + ' eliminada correctamente');
        // Aquí se eliminaría la fila de la tabla
        location.reload();
    }
}

// Cambiar estado de reserva
function cambiarEstado(id, nuevoEstado) {
    alert('Estado de reserva #' + id + ' cambiado a: ' + nuevoEstado);
    location.reload();
}

// Aprobar reserva
function aprobarReserva(id) {
    if (confirm('¿Aprobar esta reserva?')) {
        cambiarEstado(id, 'Confirmada');
    }
}

// Rechazar reserva
function rechazarReserva(id) {
    const motivo = prompt('Ingresa el motivo del rechazo:');
    if (motivo) {
        alert('Reserva #' + id + ' rechazada. Motivo: ' + motivo);
        cambiarEstado(id, 'Cancelada');
    }
}

// Completar servicio
function completarServicio(id) {
    if (confirm('¿Marcar este servicio como completado?')) {
        cambiarEstado(id, 'Completada');
    }
}

// Agregar cliente
function agregarCliente() {
    const modal = new bootstrap.Modal(document.getElementById('modalAgregarCliente'));
    if (modal) {
        modal.show();
    }
}

// Ver perfil cliente
function verPerfilCliente(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalPerfilCliente'));
    if (modal) {
        modal.show();
    }
    console.log('Viendo perfil de cliente #' + id);
}

// Editar cliente
function editarCliente(id) {
    alert('Editando cliente #' + id);
}

// Eliminar cliente
function eliminarCliente(id) {
    if (confirm('¿Estás seguro de que deseas eliminar este cliente?')) {
        alert('Cliente #' + id + ' eliminado correctamente');
        location.reload();
    }
}

// Editar servicio
function editarServicio(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalEditarServicio'));
    if (modal) {
        modal.show();
    }
    console.log('Editando servicio #' + id);
}

// Eliminar servicio
function eliminarServicio(id) {
    if (confirm('¿Estás seguro de que deseas eliminar este servicio?')) {
        alert('Servicio #' + id + ' eliminado correctamente');
        location.reload();
    }
}

// Activar/Desactivar servicio
function toggleServicio(id) {
    alert('Estado del servicio #' + id + ' cambiado');
    location.reload();
}

// ========================================
// MANEJO DE FORMULARIOS ADMIN
// ========================================

// Formulario nueva reserva admin
const formNuevaReservaAdmin = document.getElementById('formNuevaReservaAdmin');
if (formNuevaReservaAdmin) {
    formNuevaReservaAdmin.addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Reserva creada exitosamente por el administrador');
        showAdminSection('reservas');
        formNuevaReservaAdmin.reset();
    });
}

// Formulario editar reserva admin
const formEditarReservaAdmin = document.getElementById('formEditarReservaAdmin');
if (formEditarReservaAdmin) {
    formEditarReservaAdmin.addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Reserva actualizada correctamente');
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarReservaAdmin'));
        if (modal) modal.hide();
        location.reload();
    });
}

// Formulario agregar cliente
const formAgregarCliente = document.getElementById('formAgregarCliente');
if (formAgregarCliente) {
    formAgregarCliente.addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Cliente agregado correctamente');
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalAgregarCliente'));
        if (modal) modal.hide();
        location.reload();
    });
}

// Formulario editar servicio
const formEditarServicio = document.getElementById('formEditarServicio');
if (formEditarServicio) {
    formEditarServicio.addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Servicio actualizado correctamente');
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarServicio'));
        if (modal) modal.hide();
        location.reload();
    });
}

// ========================================
// BÚSQUEDA Y FILTROS
// ========================================

// Búsqueda en tiempo real
function buscarEnTabla(inputId, tablaId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    input.addEventListener('keyup', function() {
        const filtro = input.value.toLowerCase();
        const tabla = document.getElementById(tablaId);
        if (!tabla) return;
        
        const filas = tabla.getElementsByTagName('tr');
        
        for (let i = 0; i < filas.length; i++) {
            const fila = filas[i];
            const texto = fila.textContent || fila.innerText;
            
            if (texto.toLowerCase().indexOf(filtro) > -1) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        }
    });
}

// Filtrar por estado
function filtrarPorEstado(estado) {
    const filas = document.querySelectorAll('#tablaReservas tr');
    
    filas.forEach(fila => {
        if (estado === '' || fila.textContent.includes(estado)) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
}

// ========================================
// ESTADÍSTICAS Y GRÁFICOS
// ========================================

// Actualizar estadísticas del dashboard
function actualizarEstadisticas() {
    // Aquí se actualizarían las estadísticas con datos reales
    console.log('Actualizando estadísticas...');
}

// ========================================
// NOTIFICACIONES
// ========================================

// Mostrar notificación
function mostrarNotificacion(mensaje, tipo = 'info') {
    // Crear elemento de notificación
    const notif = document.createElement('div');
    notif.className = `alert alert-${tipo} position-fixed top-0 end-0 m-3`;
    notif.style.zIndex = '9999';
    notif.innerHTML = `
        <i class="fas fa-${tipo === 'success' ? 'check-circle' : 'info-circle'}"></i>
        ${mensaje}
    `;
    
    document.body.appendChild(notif);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notif.remove();
    }, 3000);
}

// ========================================
// VALIDACIONES
// ========================================

// Validar formulario de reserva
function validarFormularioReserva(form) {
    const campos = form.querySelectorAll('[required]');
    let valido = true;
    
    campos.forEach(campo => {
        if (!campo.value) {
            campo.classList.add('is-invalid');
            valido = false;
        } else {
            campo.classList.remove('is-invalid');
        }
    });
    
    return valido;
}

// ========================================
// INICIALIZACIÓN
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    // Mostrar fecha actual
    mostrarFechaActual();
    
    // Inicializar tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Establecer fecha mínima en inputs de fecha (hoy)
    const inputsFecha = document.querySelectorAll('input[type="date"]');
    const hoy = new Date().toISOString().split('T')[0];
    inputsFecha.forEach(input => {
        input.setAttribute('min', hoy);
    });
    
    console.log('Sistema de reservas Aruma Spa inicializado correctamente');
});

// ========================================
// PREVENIR ENVÍO DE FORMULARIOS
// ========================================

// Interceptar todos los formularios para evitar envío real
document.querySelectorAll('form').forEach(form => {
    if (!form.id) return; // Solo los que tienen manejo específico
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validarFormularioReserva(form)) {
            console.log('Formulario válido:', form.id);
        } else {
            console.log('Formulario inválido:', form.id);
        }
    });
});

console.log('reservas.js cargado correctamente ✨');
