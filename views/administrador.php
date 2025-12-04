<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador - Aruma Spa</title>
    <link href="../public/estilos/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../public/estilos/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="../views/home.php">
                <i class="fas fa-spa"></i> ARUMA - Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-user-shield"></i> Administrador
                        </a>
                    </li>
                </ul>
                <button class="btn btn-login ms-3" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </button>
            </div>
        </div>
    </nav>

    <section class="admin-dashboard">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-2 sidebar-col">
                    <div class="admin-sidebar">
                        <div class="sidebar-header">
                            <i class="fas fa-user-shield"></i>
                            <h5>Panel Admin</h5>
                        </div>
                        <ul class="sidebar-menu">
                            <li class="active" onclick="showAdminSection('dashboard')">
                                <i class="fas fa-chart-line"></i> Dashboard
                            </li>
                            <li onclick="showAdminSection('reservas')">
                                <i class="fas fa-calendar-check"></i> Reservas
                            </li>
                            <li onclick="showAdminSection('nueva-reserva-admin')">
                                <i class="fas fa-plus-circle"></i> Nueva Reserva
                            </li>
                            <li onclick="showAdminSection('historial')">
                                <i class="fas fa-history"></i> Historial
                            </li>
                            <li onclick="showAdminSection('clientes')">
                                <i class="fas fa-users"></i> Clientes
                            </li>
                            <li onclick="showAdminSection('servicios')">
                                <i class="fas fa-spa"></i> Servicios
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-10">
                    <div class="admin-content">
                        <div id="dashboard" class="content-section active">
                            <div class="section-header">
                                <h2><i class="fas fa-chart-line"></i> Dashboard</h2>
                                <div class="date-display">
                                    <i class="fas fa-calendar"></i> Hoy: <span id="fecha-actual"></span>
                                </div>
                            </div>
 
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card stat-primary">
            <div class="stat-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $reservas_hoy ?? 0; ?></h3>
                <p>Reservas Hoy</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-success">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $reservas_confirmadas ?? 0; ?></h3>
                <p>Confirmadas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-warning">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $reservas_pendientes ?? 0; ?></h3>
                <p>Pendientes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-info">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $total_clientes ?? 0; ?></h3>
                <p>Total Clientes</p>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-section">
    <h4><i class="fas fa-calendar-day"></i> Reservas de Hoy</h4>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Cliente</th>
                    <th>Servicio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reservas_detalle)): ?>
                    <?php foreach ($reservas_detalle as $reserva): ?>
                        <tr>
                            <td><?php echo date('H:i', strtotime($reserva['hora'])); ?></td>
                            <td><?php echo htmlspecialchars($reserva['cliente_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['servicio_nombre']); ?></td>
                            <td>
                                <?php 
                                $badge_class = match ($reserva['estado']) {
                                    'Confirmada' => 'bg-success',
                                    'Pendiente' => 'bg-warning text-dark',
                                    'Cancelada' => 'bg-secondary',
                                    'Completada' => 'bg-info',
                                    default => 'bg-primary',
                                };
                                ?>
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo htmlspecialchars($reserva['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="verDetalles(<?php echo $reserva['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No hay reservas para hoy
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
                      </div>

                        <div id="reservas" class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-calendar-check"></i> Gestión de Reservas</h2>
                                <div class="header-actions">
                                    <button class="btn btn-primary" onclick="showAdminSection('nueva-reserva-admin')">
                                        <i class="fas fa-plus"></i> Nueva Reserva
                                    </button>
                                </div>
                            </div>

                            <!-- Barra de filtros mejorada -->
                            <div class="filters-bar mb-4">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small">Estado</label>
                                        <select class="form-select" id="filtroEstado" onchange="filtrarReservas()">
                                            <option value="todos">Todos los estados</option>
                                            <option value="Pendiente">Pendiente</option>
                                            <option value="Confirmada">Confirmada</option>
                                            <option value="En Proceso">En Proceso</option>
                                            <option value="Completada">Completada</option>
                                            <option value="Cancelada">Cancelada</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Fecha</label>
                                        <input type="date" class="form-control" id="filtroFecha" onchange="filtrarReservas()">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Servicio</label>
                                        <select class="form-select" id="filtroServicio" onchange="filtrarReservas()">
                                            <option value="">Todos los servicios</option>
                                            <?php if(isset($servicios)): ?>
                                                <?php foreach($servicios as $servicio): ?>
                                                <option value="<?php echo $servicio['id']; ?>">
                                                    <?php echo htmlspecialchars($servicio['nombre']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Buscar</label>
                                        <input type="search" class="form-control" id="filtroBusqueda" 
                                               placeholder="Cliente, teléfono, email..." onkeyup="filtrarReservas()">
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#ID</th>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Cliente</th>
                                            <th>Servicio</th>
                                            <th>Terapeuta</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaReservas">
                                        <?php 
                                        // --------------------------------------------------------
                                        // LÓGICA PHP INYECTADA: Itera sobre las reservas del Controlador
                                        // --------------------------------------------------------
                                        if (isset($reservas) && is_array($reservas) && count($reservas) > 0): 
                                            foreach ($reservas as $reserva): 
                                                
                                                $badge_class = match ($reserva['estado']) {
                                                    'Confirmada' => 'bg-success',
                                                    'Pendiente' => 'bg-warning text-dark',
                                                    'Cancelada' => 'bg-secondary',
                                                    'Completada' => 'bg-info',
                                                    default => 'bg-primary',
                                                };
                                        ?>
                                                <tr>
                                                    <td><?php echo '#' . htmlspecialchars($reserva['id']); ?></td>
                                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($reserva['fecha']))); ?></td>
                                                    <td><?php echo htmlspecialchars(date('H:i', strtotime($reserva['hora']))); ?></td>
                                                    <td><?php echo htmlspecialchars($reserva['cliente_nombre']); ?></td>
                                                    <td><?php echo htmlspecialchars($reserva['servicio_nombre']); ?></td>
                                                    <td><?php echo htmlspecialchars($reserva['terapeuta_nombre']); ?></td>
                                                    <td><span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($reserva['estado']); ?></span></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-info" onclick="verDetalles(<?php echo $reserva['id']; ?>)">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-primary" onclick="editarReservaAdmin(<?php echo $reserva['id']; ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarReserva(<?php echo $reserva['id']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                        <?php 
                                            endforeach; 
                                        else:
                                        ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">No se encontraron reservas registradas.</td>
                                            </tr>
                                        <?php 
                                        endif; 
                                        // --------------------------------------------------------
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="nueva-reserva-admin" class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-plus-circle"></i> Crear Nueva Reserva</h2>
                            </div>

                            <div class="form-card">
                                <form id="formNuevaReservaAdmin" method="POST" action="/api/reservas/create"> 
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                            <select class="form-select" required name="cliente_id" id="selectCliente">
                                                <option value="">Selecciona un cliente</option>
                                                <?php if(isset($clientes)): ?>
                                                    <?php foreach($clientes as $cliente): ?>
                                                    <option value="<?php echo $cliente['id']; ?>">
                                                        <?php echo htmlspecialchars($cliente['nombre']); ?> 
                                                        (<?php echo htmlspecialchars($cliente['email']); ?>)
                                                    </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="">No hay clientes registrados</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Servicio <span class="text-danger">*</span></label>
                                            <select class="form-select" required name="servicio_id" id="selectServicio">
                                                <option value="">Selecciona un servicio</option>
                                                <?php if(isset($servicios)): ?>
                                                    <?php foreach($servicios as $servicio): ?>
                                                    <option value="<?php echo $servicio['id']; ?>" 
                                                            data-duracion="<?php echo $servicio['duration_min']; ?>"
                                                            data-precio="<?php echo $servicio['precio']; ?>">
                                                        <?php echo htmlspecialchars($servicio['nombre']); ?> 
                                                        - $<?php echo number_format($servicio['precio'], 2); ?> 
                                                        (<?php echo $servicio['duration_min']; ?> min)
                                                    </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="">No hay servicios disponibles</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Terapeuta</label>
                                            <select class="form-select" name="therapist_id" id="selectTerapeuta">
                                                <option value="">Selecciona un terapeuta (opcional)</option>
                                                <?php if(isset($terapeutas)): ?>
                                                    <?php foreach($terapeutas as $terapeuta): ?>
                                                    <option value="<?php echo $terapeuta['id']; ?>">
                                                        <?php echo htmlspecialchars($terapeuta['nombre']); ?>
                                                        <?php if($terapeuta['especialidad']): ?>
                                                        - <?php echo htmlspecialchars($terapeuta['especialidad']); ?>
                                                        <?php endif; ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="">No hay terapeutas disponibles</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Estado <span class="text-danger">*</span></label>
                                            <select class="form-select" required name="estado">
                                                <option value="">Selecciona estado</option>
                                                <option value="Pendiente" selected>Pendiente</option>
                                                <option value="Confirmada">Confirmada</option>
                                                <option value="En Proceso">En Proceso</option>
                                                <option value="Completada">Completada</option>
                                                <option value="Cancelada">Cancelada</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" required name="fecha" id="fechaReserva" 
                                                   min="<?php echo date('Y-m-d'); ?>">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Hora <span class="text-danger">*</span></label>
                                            <select class="form-select" required name="hora" id="horaReserva">
                                                <option value="">Primero selecciona una fecha</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label">Duración</label>
                                            <div class="form-control-plaintext" id="duracionServicio">
                                                Selecciona un servicio para ver la duración
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Notas (opcional)</label>
                                            <textarea class="form-control" rows="3" placeholder="Notas adicionales..." 
                                                      name="notas" id="notasReserva"></textarea>
                                        </div>

                                        <div class="col-12 text-end">
                                            <button type="button" class="btn btn-outline-secondary me-2" onclick="showAdminSection('reservas')">
                                                <i class="fas fa-times"></i> Cancelar
                                            </button>
                                            <button type="submit" class="btn btn-primary" id="btnGuardarReserva">
                                                <i class="fas fa-save"></i> Guardar Reserva
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div id="historial" class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-history"></i> Historial</h2>
                            </div>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Módulo en construcción
                            </div>
                        </div>

                        <div id="clientes" class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-users"></i> Gestión de Clientes</h2>
                            </div>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Módulo en construcción
                            </div>
                        </div>

                        <div id="servicios" class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-spa"></i> Gestión de Servicios</h2>
                            </div>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Módulo en construcción
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalDetalles" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-eye"></i> Detalles de Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalDetallesBody">
                    Cargando detalles...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarReservaAdmin" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Reserva #<span id="edit_reserva_id_display">000</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarReservaAdmin" method="POST" action="/api/reservas/update">
                        <input type="hidden" name="id" id="edit_reserva_id">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Cliente</label>
                                <select class="form-select" name="cliente_id" id="edit_cliente_id">
                                    <?php if(isset($clientes)): ?>
                                        <?php foreach($clientes as $cliente): ?>
                                        <option value="<?php echo $cliente['id']; ?>">
                                            <?php echo htmlspecialchars($cliente['nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Servicio</label>
                                <select class="form-select" name="servicio_id" id="edit_servicio_id">
                                    <?php if(isset($servicios)): ?>
                                        <?php foreach($servicios as $servicio): ?>
                                        <option value="<?php echo $servicio['id']; ?>">
                                            <?php echo htmlspecialchars($servicio['nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="estado" id="edit_estado">
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Confirmada">Confirmada</option>
                                    <option value="En Proceso">En Proceso</option>
                                    <option value="Completada">Completada</option>
                                    <option value="Cancelada">Cancelada</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Terapeuta</label>
                                <select class="form-select" name="therapist_id" id="edit_therapist_id">
                                    <option value="">Sin terapeuta</option>
                                    <?php if(isset($terapeutas)): ?>
                                        <?php foreach($terapeutas as $terapeuta): ?>
                                        <option value="<?php echo $terapeuta['id']; ?>">
                                            <?php echo htmlspecialchars($terapeuta['nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha</label>
                                <input type="date" class="form-control" name="fecha" id="edit_fecha">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hora</label>
                                <select class="form-select" name="hora" id="edit_hora">
                                    <!-- Se llenará dinámicamente -->
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notas</label>
                                <textarea class="form-control" rows="3" name="notas" id="edit_notas"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="formEditarReservaAdmin" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../public/scripts/reservas.js"></script>
    
    <script>
    // Funcionalidad adicional para la vista
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar duración del servicio seleccionado
        const selectServicio = document.getElementById('selectServicio');
        const duracionServicio = document.getElementById('duracionServicio');
        
        if (selectServicio && duracionServicio) {
            selectServicio.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const duracion = selectedOption.getAttribute('data-duracion');
                    const precio = selectedOption.getAttribute('data-precio');
                    duracionServicio.innerHTML = `
                        <strong>Duración:</strong> ${duracion} minutos | 
                        <strong>Precio:</strong> $${parseFloat(precio).toFixed(2)}
                    `;
                } else {
                    duracionServicio.innerHTML = 'Selecciona un servicio para ver la duración';
                }
            });
        }
        
        // Cargar horarios cuando se selecciona fecha
        const fechaInput = document.getElementById('fechaReserva');
        const horaSelect = document.getElementById('horaReserva');
        
        if (fechaInput && horaSelect) {
            fechaInput.addEventListener('change', function() {
                if (this.value) {
                    cargarHorariosDisponibles(this.value);
                }
            });
        }
        
        // También para el formulario de edición
        const editFechaInput = document.getElementById('edit_fecha');
        const editHoraSelect = document.getElementById('edit_hora');
        
        if (editFechaInput && editHoraSelect) {
            editFechaInput.addEventListener('change', function() {
                if (this.value) {
                    cargarHorariosDisponibles(this.value, 'edit_');
                }
            });
        }
        
        // Inicializar fecha mínima
        if (fechaInput) {
            fechaInput.min = new Date().toISOString().split('T')[0];
        }
        if (editFechaInput) {
            editFechaInput.min = new Date().toISOString().split('T')[0];
        }
    });
    
    // Función para cargar horarios disponibles
    async function cargarHorariosDisponibles(fecha, prefix = '') {
        const horaSelect = document.getElementById(prefix + 'hora') || 
                          document.querySelector(`select[name="${prefix}hora"]`);
        
        if (!fecha || !horaSelect) return;
        
        // Mostrar loading
        const originalHTML = horaSelect.innerHTML;
        horaSelect.innerHTML = '<option value="">Cargando horarios disponibles...</option>';
        horaSelect.disabled = true;
        
        try {
            const res = await fetch(`/api/horarios-disponibles?fecha=${fecha}`);
            const horarios = await res.json();
            
            horaSelect.innerHTML = '<option value="">Selecciona una hora</option>';
            
            if (horarios && horarios.length > 0) {
                horarios.forEach(hora => {
                    const horaFormateada = hora.substring(0, 5); // Formato HH:MM
                    const hora12h = convertirHora12h(horaFormateada);
                    
                    const option = document.createElement('option');
                    option.value = hora + ':00'; // Formato HH:MM:SS para la BD
                    option.textContent = `${horaFormateada} hrs (${hora12h})`;
                    horaSelect.appendChild(option);
                });
            } else {
                horaSelect.innerHTML = '<option value="">No hay horarios disponibles para esta fecha</option>';
            }
            
        } catch (err) {
            console.error('Error cargando horarios:', err);
            horaSelect.innerHTML = '<option value="">Error al cargar horarios</option>';
        } finally {
            horaSelect.disabled = false;
        }
    }
    
    // Función para convertir hora 24h a 12h
    function convertirHora12h(hora24) {
        const [horas, minutos] = hora24.split(':');
        let horas12 = parseInt(horas);
        const ampm = horas12 >= 12 ? 'PM' : 'AM';
        horas12 = horas12 % 12 || 12;
        return `${horas12}:${minutos} ${ampm}`;
    }
    
    // Función para filtrar la tabla de reservas
    function filtrarReservas() {
        const filtroEstado = document.getElementById('filtroEstado').value;
        const filtroFecha = document.getElementById('filtroFecha').value;
        const filtroServicio = document.getElementById('filtroServicio').value;
        const filtroBusqueda = document.getElementById('filtroBusqueda').value.toLowerCase();
        
        const filas = document.querySelectorAll('#tablaReservas tr');
        
        filas.forEach(fila => {
            let mostrar = true;
            
            // Filtrar por estado
            if (filtroEstado && filtroEstado !== 'todos') {
                const estadoCelda = fila.querySelector('td:nth-child(7)');
                if (estadoCelda) {
                    const estado = estadoCelda.textContent.trim().toLowerCase();
                    if (estado !== filtroEstado.toLowerCase()) {
                        mostrar = false;
                    }
                }
            }
            
            // Filtrar por fecha
            if (filtroFecha) {
                const fechaCelda = fila.querySelector('td:nth-child(2)');
                if (fechaCelda) {
                    // Convertir fecha de tabla (d/m/Y) a formato Y-m-d
                    const partes = fechaCelda.textContent.trim().split('/');
                    if (partes.length === 3) {
                        const fechaTabla = `${partes[2]}-${partes[1]}-${partes[0]}`;
                        if (fechaTabla !== filtroFecha) {
                            mostrar = false;
                        }
                    }
                }
            }
            
            // Filtrar por servicio
            if (filtroServicio) {
                const servicioCelda = fila.querySelector('td:nth-child(5)');
                if (servicioCelda) {
                    const idServicio = servicioCelda.getAttribute('data-servicio-id');
                    if (idServicio !== filtroServicio) {
                        mostrar = false;
                    }
                }
            }
            
            // Filtrar por búsqueda
            if (filtroBusqueda) {
                const textoFila = fila.textContent.toLowerCase();
                if (!textoFila.includes(filtroBusqueda)) {
                    mostrar = false;
                }
            }
            
            fila.style.display = mostrar ? '' : 'none';
        });
    }
    </script>
</body>
</html>
