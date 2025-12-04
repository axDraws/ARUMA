/* ============================================================
   ARUMA SPA - SISTEMA DE RESERVAS CONECTADO A MVC REAL
============================================================ */

// Mostrar fecha actual
function mostrarFechaActual() {
    const fechaElement = document.getElementById("fecha-actual");
    if (fechaElement) {
        const hoy = new Date();
        const opciones = { weekday: "long", year: "numeric", month: "long", day: "numeric" };
        fechaElement.textContent = hoy.toLocaleDateString("es-ES", opciones);
    }
}

// Logout REAL (dirige al backend)
function logout() {
    if (confirm("¿Cerrar sesión?")) {
        window.location.href = "/logout";
    }
}

/* ============================================================
   CAMBIO DE SECCIÓN DEL ADMIN
============================================================ */
function showAdminSection(sectionId) {
    document.querySelectorAll(".content-section").forEach(sec => sec.classList.remove("active"));
    const section = document.getElementById(sectionId);
    if (section) section.classList.add("active");

    document.querySelectorAll(".sidebar-menu li").forEach(li => li.classList.remove("active"));
    const clicked = event.target.closest("li");
    if (clicked) clicked.classList.add("active");

    if (sectionId === "reservas") cargarReservas();
}

/* ============================================================
   CARGAR RESERVAS (ADMIN) - ACTUALIZADO CON NUEVAS RUTAS API
============================================================ */
async function cargarReservas() {
    const tabla = document.getElementById("tablaReservas");
    if (!tabla) return;

    tabla.innerHTML = `<tr><td colspan="8">Cargando...</td></tr>`;

    try {
        // ACTUALIZADO: Usar /api/reservas en lugar de /reservas
        const res = await fetch("/api/reservas");
        
        if (!res.ok) {
            throw new Error(`Error HTTP: ${res.status}`);
        }
        
        const data = await res.json();

        // Verificar si hay error en la respuesta
        if (data.error) {
            tabla.innerHTML = `<tr><td colspan="8" class="text-center text-danger">${data.error}</td></tr>`;
            return;
        }

        tabla.innerHTML = "";

        if (data.length === 0) {
            tabla.innerHTML = `<tr><td colspan="8" class="text-center text-muted">No hay reservas registradas</td></tr>`;
            return;
        }

        data.forEach(r => {
            // Determinar clase del badge según estado
            let badgeClass = 'bg-info';
            if (r.estado === 'Confirmada') badgeClass = 'bg-success';
            if (r.estado === 'Pendiente') badgeClass = 'bg-warning text-dark';
            if (r.estado === 'Cancelada') badgeClass = 'bg-secondary';
            if (r.estado === 'Completada') badgeClass = 'bg-primary';
            if (r.estado === 'En Proceso') badgeClass = 'bg-info';
            
            tabla.innerHTML += `
                <tr>
                    <td>#${r.id}</td>
                    <td>${r.fecha ? new Date(r.fecha).toLocaleDateString('es-ES') : '--'}</td>
                    <td>${r.hora ? r.hora.substring(0, 5) : '--'}</td>
                    <td>${r.cliente_nombre || '--'}</td>
                    <td>${r.servicio_nombre || '--'}</td>
                    <td>${r.terapeuta_nombre || "—"}</td>
                    <td><span class="badge ${badgeClass}">${r.estado || 'Pendiente'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-info me-1" onclick="verDetalles(${r.id})" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editarReservaAdmin(${r.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarReserva(${r.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

    } catch (err) {
        console.error('Error cargando reservas:', err);
        tabla.innerHTML = `<tr><td colspan="8" class="text-center text-danger">
            Error al cargar reservas: ${err.message}
        </td></tr>`;
    }
}

/* ============================================================
   VER DETALLES (modal) - ACTUALIZADO
============================================================ */
async function verDetalles(id) {
    try {
        // ACTUALIZADO: Usar /api/reservas/show
        const res = await fetch(`/api/reservas/show?id=${id}`);
        
        if (!res.ok) {
            throw new Error(`Error HTTP: ${res.status}`);
        }
        
        const r = await res.json();
        
        if (r.error) {
            mostrarNotificacion(r.error, "danger");
            return;
        }
        
        // Crear modal dinámicamente si no existe
        let modal = document.getElementById("modalDetalles");
        if (!modal) {
            modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.id = 'modalDetalles';
            modal.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
        
        document.querySelector("#modalDetalles .modal-title").innerHTML =
            `📋 Detalles de reserva #${r.id}`;

        document.querySelector("#modalDetalles .modal-body").innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Cliente:</strong> ${r.cliente_nombre || '--'}</p>
                    <p><strong>Servicio:</strong> ${r.servicio_nombre || '--'}</p>
                    <p><strong>Terapeuta:</strong> ${r.terapeuta_nombre || "No asignado"}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Fecha:</strong> ${r.fecha ? new Date(r.fecha).toLocaleDateString('es-ES', {
                        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                    }) : '--'}</p>
                    <p><strong>Hora:</strong> ${r.hora ? r.hora.substring(0, 5) : '--'}</p>
                    <p><strong>Estado:</strong> <span class="badge bg-info">${r.estado || 'Pendiente'}</span></p>
                </div>
            </div>
            ${r.notas ? `
            <hr>
            <p><strong>Notas:</strong></p>
            <p>${r.notas}</p>
            ` : ''}
        `;

        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();

    } catch (err) {
        console.error('Error al cargar detalles:', err);
        mostrarNotificacion("Error al cargar detalles de la reserva", "danger");
    }
}

/* ============================================================
   CREAR RESERVA (ADMIN) - ACTUALIZADO
============================================================ */
const formNuevaReservaAdmin = document.getElementById("formNuevaReservaAdmin");
if (formNuevaReservaAdmin) {
    formNuevaReservaAdmin.addEventListener("submit", async e => {
        e.preventDefault();

        const formData = new FormData(formNuevaReservaAdmin);

        try {
            // ACTUALIZADO: Usar /api/reservas/create
            const res = await fetch("/api/reservas/create", {
                method: "POST",
                body: formData
            });

            const json = await res.json();

            if (json.status === "ok") {
                mostrarNotificacion("✅ Reserva creada exitosamente", "success");
                formNuevaReservaAdmin.reset();
                cargarReservas();
                showAdminSection("reservas");
            } else {
                mostrarNotificacion(`❌ ${json.error || json.message}`, "danger");
            }

        } catch (err) {
            console.error('Error al crear reserva:', err);
            mostrarNotificacion("❌ Error al crear reserva", "danger");
        }
    });
}

/* ============================================================
   EDITAR RESERVA - ACTUALIZADO
============================================================ */
async function editarReservaAdmin(id) {
    try {
        // ACTUALIZADO: Usar /api/reservas/show
        const res = await fetch(`/api/reservas/show?id=${id}`);
        
        if (!res.ok) {
            throw new Error(`Error HTTP: ${res.status}`);
        }
        
        const r = await res.json();
        
        if (r.error) {
            mostrarNotificacion(r.error, "danger");
            return;
        }
        
        // Llenar el formulario del modal con los datos
        document.getElementById("edit_reserva_id").value = r.id;
        document.getElementById("edit_reserva_id_display").textContent = r.id;
        document.getElementById("edit_cliente_id").value = r.clients_id || r.cliente_id;
        document.getElementById("edit_servicio_id").value = r.service_id || r.servicio_id;
        document.getElementById("edit_therapist_id").value = r.therapist_id || '';
        document.getElementById("edit_fecha").value = r.fecha;
        document.getElementById("edit_hora").value = r.hora;
        document.getElementById("edit_estado").value = r.estado;
        document.getElementById("edit_notas").value = r.notas || '';
        
        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById("modalEditarReservaAdmin"));
        modal.show();
        
        // Configurar submit del formulario
        const form = document.getElementById("formEditarReservaAdmin");
        form.onsubmit = async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('id', id);
            
            try {
                // ACTUALIZADO: Usar /api/reservas/update
                const updateRes = await fetch("/api/reservas/update", {
                    method: "POST",
                    body: formData
                });
                
                const json = await updateRes.json();
                
                if (json.status === "ok") {
                    mostrarNotificacion("✅ Reserva actualizada exitosamente", "success");
                    modal.hide();
                    cargarReservas();
                } else {
                    mostrarNotificacion(`❌ ${json.error || json.message}`, "danger");
                }
            } catch (err) {
                console.error('Error al actualizar:', err);
                mostrarNotificacion("❌ Error al actualizar reserva", "danger");
            }
        };
        
    } catch (err) {
        console.error('Error al cargar datos para editar:', err);
        mostrarNotificacion("❌ Error al cargar datos de la reserva", "danger");
    }
}

/* ============================================================
   ELIMINAR RESERVA - ACTUALIZADO
============================================================ */
async function eliminarReserva(id) {
    if (!confirm(`¿Estás seguro de eliminar la reserva #${id}?\nEsta acción no se puede deshacer.`)) return;
    
    try {
        const formData = new FormData();
        formData.append('id', id);
        
        // ACTUALIZADO: Usar /api/reservas/delete
        const res = await fetch("/api/reservas/delete", {
            method: "POST",
            body: formData
        });
        
        const json = await res.json();
        
        if (json.status === "ok") {
            mostrarNotificacion("✅ Reserva eliminada exitosamente", "success");
            cargarReservas();
        } else {
            mostrarNotificacion(`❌ ${json.error || json.message}`, "danger");
        }
        
    } catch (err) {
        console.error('Error al eliminar:', err);
        mostrarNotificacion("❌ Error al eliminar reserva", "danger");
    }
}

/* ============================================================
   NOTIFICACIONES MEJORADAS
============================================================ */
function mostrarNotificacion(msg, tipo = "info") {
    // Remover notificaciones anteriores
    document.querySelectorAll('.alert-notification').forEach(el => el.remove());
    
    const div = document.createElement("div");
    div.className = `alert alert-${tipo} alert-notification position-fixed`;
    div.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 99999;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border: none;
    `;
    
    // Icono según tipo
    let icon = 'info-circle';
    if (tipo === 'success') icon = 'check-circle';
    if (tipo === 'danger') icon = 'exclamation-circle';
    if (tipo === 'warning') icon = 'exclamation-triangle';
    
    div.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${icon} me-2"></i>
            <span>${msg}</span>
        </div>
    `;
    
    document.body.appendChild(div);
    
    // Auto-eliminar después de 5 segundos
    setTimeout(() => {
        if (div.parentNode) {
            div.style.opacity = '0';
            div.style.transition = 'opacity 0.3s ease-out';
            setTimeout(() => div.remove(), 300);
        }
    }, 5000);
}

/* ============================================================
   INICIALIZACIÓN
============================================================ */
document.addEventListener("DOMContentLoaded", () => {
    mostrarFechaActual();
    // Solo cargar reservas si estamos en la sección de admin
    if (window.location.pathname.includes('administrador')) {
        cargarReservas();
    }
});
