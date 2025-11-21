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
   CARGAR RESERVAS (ADMIN)
============================================================ */
async function cargarReservas() {
    const tabla = document.getElementById("tablaReservas");
    if (!tabla) return;

    tabla.innerHTML = `<tr><td colspan="8">Cargando...</td></tr>`;

    try {
        const res = await fetch("/reservas");
        const data = await res.json();

        tabla.innerHTML = "";

        data.forEach(r => {
            tabla.innerHTML += `
                <tr>
                    <td>#${r.id}</td>
                    <td>${r.fecha}</td>
                    <td>${r.hora}</td>
                    <td>${r.cliente_nombre}</td>
                    <td>${r.servicio_nombre}</td>
                    <td>${r.terapeuta_nombre ?? "—"}</td>
                    <td><span class="badge bg-info">${r.estado}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-info" onclick="verDetalles(${r.id})"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline-primary" onclick="editarReservaAdmin(${r.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarReserva(${r.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });

    } catch (err) {
        tabla.innerHTML = `<tr><td colspan="8">Error cargando reservas</td></tr>`;
        console.error(err);
    }
}

/* ============================================================
   VER DETALLES (modal)
============================================================ */
async function verDetalles(id) {
    const modal = new bootstrap.Modal(document.getElementById("modalDetalles"));

    try {
        const res = await fetch(`/reservas/show?id=${id}`);
        const r = await res.json();

        document.querySelector("#modalDetalles .modal-title").innerHTML =
            `Detalles de reserva #${r.id}`;

        document.querySelector("#modalDetalles .modal-body").innerHTML = `
            <p><strong>Cliente:</strong> ${r.cliente_nombre}</p>
            <p><strong>Servicio:</strong> ${r.servicio_nombre}</p>
            <p><strong>Terapeuta:</strong> ${r.terapeuta_nombre ?? "No asignado"}</p>
            <p><strong>Fecha:</strong> ${r.fecha}</p>
            <p><strong>Hora:</strong> ${r.hora}</p>
            <p><strong>Estado:</strong> ${r.estado}</p>
            <p><strong>Notas:</strong> ${r.notas}</p>
        `;

        modal.show();

    } catch (err) {
        console.error(err);
        mostrarNotificacion("Error al cargar detalles", "danger");
    }
}

/* ============================================================
   CREAR RESERVA (ADMIN)
============================================================ */
const formNuevaReservaAdmin = document.getElementById("formNuevaReservaAdmin");
if (formNuevaReservaAdmin) {
    formNuevaReservaAdmin.addEventListener("submit", async e => {
        e.preventDefault();

        const formData = new FormData(formNuevaReservaAdmin);

        try {
            const res = await fetch("/reservas", {
                method: "POST",
                body: formData
            });

            const json = await res.json();

            if (json.status === "ok") {
                mostrarNotificacion("Reserva creada", "success");
                formNuevaReservaAdmin.reset();
                cargarReservas();
                showAdminSection("reservas");
            } else {
                mostrarNotificacion(json.message, "danger");
            }

        } catch (err) {
            console.error(err);
            mostrarNotificacion("Error al crear reserva", "danger");
        }
    });
}

/* ============================================================
   EDITAR RESERVA
============================================================ */
async function editarReservaAdmin(id) {
    const modal = new bootstrap.Modal(document.getElementById("modalEditarReservaAdmin"));

    const res = await fetch(`/reservas/show?id=${id}`);
    const r = await res.json();

    document.getElementById("editFecha").value = r.fecha;
    document.getElementById("editNotas").value = r.notas;

    modal.show();

    document.getElementById("formEditarReservaAdmin").onsubmit = async e => {
        e.preventDefault();

        const fd = new FormData(e.target);
        fd.append("id", id);

        await fetch("/reservas/update", {
            method: "POST",
            body: fd
        });

        mostrarNotificacion("Reserva actualizada", "success");
        modal.hide();
        cargarReservas();
    };
}

/* ============================================================
   ELIMINAR RESERVA
============================================================ */
async function eliminarReserva(id) {
    if (!confirm("¿Eliminar esta reserva?")) return;

    const fd = new FormData();
    fd.append("id", id);

    await fetch("/reservas/delete", {
        method: "POST",
        body: fd
    });

    mostrarNotificacion("Reserva eliminada", "success");
    cargarReservas();
}

/* ============================================================
   NOTIFICACIONES
============================================================ */
function mostrarNotificacion(msg, tipo = "info") {
    const div = document.createElement("div");
    div.className = `alert alert-${tipo} position-fixed top-0 end-0 m-3`;
    div.textContent = msg;

    div.style.zIndex = "99999";

    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}

/* ============================================================
   INICIALIZACIÓN
============================================================ */
document.addEventListener("DOMContentLoaded", () => {
    mostrarFechaActual();
    cargarReservas();
});
