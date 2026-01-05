document.addEventListener('DOMContentLoaded', () => {
    // Verificar sesión (opcional aquí, ya que el backend protege, pero buena práctica)
    // Cargar datos iniciales
    loadProfile();
    loadReservations();
    loadServices();

    // Event Listeners
    const formNuevaReserva = document.getElementById('formNuevaReserva');
    if (formNuevaReserva) {
        formNuevaReserva.addEventListener('submit', handleNewReservation);
    }

    // Listener para formulario perfil si existe
    const formPerfil = document.querySelector('#mi-perfil form');
    if (formPerfil) {
        formPerfil.addEventListener('submit', updateProfile);
    }
});

// ==========================================
// NAVEGACIÓN
// ==========================================
function showSection(sectionId) {
    // Ocultar todas las secciones
    document.querySelectorAll('.content-section').forEach(sec => {
        sec.classList.remove('active');
    });

    // Quitar active del sidebar
    document.querySelectorAll('.sidebar-menu li').forEach(li => {
        li.classList.remove('active');
    });

    // Mostrar sección deseada
    const target = document.getElementById(sectionId);
    if (target) target.classList.add('active');

    // Activar item del sidebar correspondiente
    const activeLink = Array.from(document.querySelectorAll('.sidebar-menu li')).find(li => {
        return li.getAttribute('onclick') && li.getAttribute('onclick').includes(sectionId);
    });
    if (activeLink) {
        activeLink.classList.add('active');
    }
}

// ==========================================
// PERFIL
// ==========================================
async function loadProfile() {
    try {
        const response = await fetch('api/client/profile');
        if (!response.ok) throw new Error('Error al cargar perfil');

        const user = await response.json();

        // Llenar campos del perfil
        // Asignaremos IDs en el HTML para facilitar esto
        if (document.getElementById('profileNombre')) document.getElementById('profileNombre').value = user.nombre || '';
        if (document.getElementById('profileEmail')) document.getElementById('profileEmail').value = user.email || '';
        if (document.getElementById('profilePhone')) document.getElementById('profilePhone').value = user.telefono || '';
        if (document.getElementById('profileDob')) document.getElementById('profileDob').value = user.fecha_nac || '';
        if (document.getElementById('profileAddress')) document.getElementById('profileAddress').value = user.direccion || '';

    } catch (error) {
        console.error(error);
    }
}

async function updateProfile(event) {
    event.preventDefault();

    // Recolectar datos manualmente o via FormData
    // Necesitamos asegurarnos que los inputs tengan 'name' correctos
    const formData = new FormData(event.target);

    try {
        const response = await fetch('api/client/profile/update', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'ok') {
            alert('Perfil actualizado correctamente');
        } else {
            alert('Error: ' + (result.error || 'Desconocido'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al actualizar perfil');
    }
}

// ==========================================
// RESERVAS
// ==========================================
async function loadReservations() {
    try {
        const response = await fetch('api/client/reservas');
        if (!response.ok) throw new Error('Error al cargar reservas');

        const reservations = await response.json();
        renderReservations(reservations);

    } catch (error) {
        console.error(error);
        const container = document.getElementById('mis-reservas-container');
        if (container) container.innerHTML = '<p class="text-danger">Error al cargar reservas.</p>';
    }
}

function renderReservations(reservations) {
    const container = document.getElementById('mis-reservas-container');
    if (!container) return;

    container.innerHTML = '';

    if (reservations.length === 0) {
        container.innerHTML = '<div class="col-12"><p class="text-muted">No tienes reservas activas.</p></div>';
        return;
    }

    reservations.forEach(res => {
        const badgeClass = getStatusBadge(res.estado);
        const card = `
            <div class="col-md-6">
                <div class="reserva-card">
                    <div class="reserva-header">
                        <span class="badge ${badgeClass}">${(res.estado || 'Pendiente').toUpperCase()}</span>
                        <span class="reserva-id">#${res.id}</span>
                    </div>
                    <div class="reserva-body">
                        <h4>${res.servicio_nombre || res.servicio || 'Servicio'}</h4>
                        <div class="reserva-info">
                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <span>${formatDate(res.fecha)}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <span>${res.hora}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-user-md"></i>
                                <span>Terapeuta: ${res.terapeuta_nombre || res.terapeuta || 'Asignado al llegar'}</span>
                            </div>
                        </div>
                    </div>
                    <div class="reserva-footer">
                        ${res.estado !== 'cancelada' && res.estado !== 'completada' ? `
                            <button class="btn btn-sm btn-outline-danger" onclick="cancelReservation(${res.id})">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
        container.innerHTML += card;
    });
}

function getStatusBadge(status) {
    if (!status) return 'bg-secondary';
    switch (status.toLowerCase()) {
        case 'confirmada': return 'bg-success';
        case 'pendiente': return 'bg-warning text-dark';
        case 'cancelada': return 'bg-danger';
        case 'completada': return 'bg-info';
        default: return 'bg-secondary';
    }
}

function formatDate(dateString) {
    if (!dateString) return '';
    // Manejo simple de fecha
    try {
        const date = new Date(dateString + 'T00:00:00');
        return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' });
    } catch (e) {
        return dateString;
    }
}

// ==========================================
// NUEVA RESERVA
// ==========================================
async function loadServices() {
    try {
        const response = await fetch('api/public/services');
        if (!response.ok) throw new Error('Error al cargar servicios');

        const servicios = await response.json();
        populateServices(servicios);

    } catch (e) {
        console.log("No se pudieron cargar servicios dinámicos, usando mock si existe o mostrando error");
        // Fallback or empty
    }
}

function populateServices(servicios) {
    const select = document.getElementById('reservaServicio');
    if (!select) return;

    select.innerHTML = '<option value="">Selecciona un servicio</option>';
    servicios.forEach(s => {
        select.innerHTML += `<option value="${s.id}">${s.nombre} - ${s.duracion_min} min - $${s.precio}</option>`;
    });
}

function populateTimeSlots() {
    // Generar horas 9am a 6pm
    const select = document.getElementById('reservaHora');
    if (!select) return;

    select.innerHTML = '<option value="">Selecciona una hora</option>';
    const start = 9;
    const end = 18;

    for (let i = start; i <= end; i++) {
        const hour = i < 10 ? `0${i}` : i;
        select.innerHTML += `<option value="${hour}:00:00">${hour}:00</option>`;
    }
}

// Llamar a population de horas (servicios se llama tras fetch)
populateTimeSlots();


async function handleNewReservation(event) {
    event.preventDefault();

    const formData = new FormData(event.target);

    try {
        const response = await fetch('api/client/reservas/create', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'ok') {
            alert('Reserva creada con éxito');
            document.getElementById('formNuevaReserva').reset();
            showSection('mis-reservas');
            loadReservations();
        } else {
            alert('Error: ' + (result.error || 'No se pudo crear'));
        }
    } catch (error) {
        console.error(error);
        alert('Error al crear reserva');
    }
}

async function cancelReservation(id) {
    if (!confirm('¿Estás seguro de cancelar esta reserva?')) return;

    const formData = new FormData();
    formData.append('id', id);

    try {
        const response = await fetch('api/client/reservas/cancel', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'ok') {
            alert('Reserva cancelada');
            loadReservations();
        } else {
            alert('Error: ' + result.error);
        }

    } catch (error) {
        console.error(error);
        alert('Error al cancelar');
    }
}

function logout() {
    window.location.href = 'logout';
}
