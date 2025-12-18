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
                                                    <?php
                                                    // Protección contra NULL o keys faltantes
                                                    $cliente = $reserva['cliente_nombre'] ?? 'Sin cliente';
                                                    $servicio = $reserva['servicio_nombre'] ?? 'Sin servicio';
                                                    $estado = $reserva['estado'] ?? 'pendiente';
                                                    ?>
                                                    <tr>
                                                        <td><?php echo date('H:i', strtotime($reserva['hora'])); ?></td>
                                                        <td><?php echo htmlspecialchars($cliente); ?></td>
                                                        <td><?php echo htmlspecialchars($servicio); ?></td>
                                                        <td>
                                                            <?php
                                                            $badge_class = match (strtolower($estado)) {
                                                                'confirmada' => 'bg-success',
                                                                'pendiente' => 'bg-warning text-dark',
                                                                'cancelada' => 'bg-secondary',
                                                                'completada' => 'bg-info',
                                                                default => 'bg-primary',
                                                            };
                                                            ?>
                                                            <span class="badge <?php echo $badge_class; ?>">
                                                                <?php echo htmlspecialchars(ucfirst($estado)); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary"
                                                                onclick="verDetalles(<?php echo $reserva['id']; ?>)">
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
                                        <input type="date" class="form-control" id="filtroFecha"
                                            onchange="filtrarReservas()">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Servicio</label>
                                        <select class="form-select" id="filtroServicio" onchange="filtrarReservas()">
                                            <option value="">Todos los servicios</option>
                                            <?php if (isset($servicios)): ?>
                                                <?php foreach ($servicios as $servicio): ?>
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
                                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($reserva['fecha']))); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars(date('H:i', strtotime($reserva['hora']))); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($reserva['cliente_nombre']); ?></td>
                                                    <td><?php echo htmlspecialchars($reserva['servicio_nombre']); ?></td>
                                                    <td><?php echo htmlspecialchars($reserva['terapeuta_nombre']); ?></td>
                                                    <td><span
                                                            class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($reserva['estado']); ?></span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-info"
                                                            onclick="verDetalles(<?php echo $reserva['id']; ?>)">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-primary"
                                                            onclick="editarReservaAdmin(<?php echo $reserva['id']; ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger"
                                                            onclick="eliminarReserva(<?php echo $reserva['id']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php
                                            endforeach;
                                        else:
                                            ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">No se encontraron reservas
                                                    registradas.</td>
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
                                        <!-- CLIENTE -->
                                        <div class="col-md-6">
                                            <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                            <select class="form-select" required name="cliente_id" id="selectCliente">
                                                <option value="">Selecciona un cliente</option>
                                                <?php if (isset($clientes)): ?>
                                                    <?php foreach ($clientes as $cliente): ?>
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

                                        <!-- SERVICIO -->
                                        <div class="col-md-6">
                                            <label class="form-label">Servicio <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" required name="servicio_id" id="selectServicio">
                                                <option value="">Selecciona un servicio</option>
                                                <?php if (isset($servicios)): ?>
                                                    <?php foreach ($servicios as $servicio): ?>
                                                        <option value="<?= $servicio['id']; ?>"
                                                            data-duracion="<?= $servicio['duracion_min']; ?>"
                                                            data-precio="<?= $servicio['precio']; ?>">
                                                            <?= htmlspecialchars($servicio['nombre']); ?>
                                                            - $<?= number_format($servicio['precio'], 2); ?>
                                                            (<?= $servicio['duracion_min']; ?> min)
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="">No hay servicios disponibles</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <!-- TERAPEUTA -->
                                        <div class="col-md-6">
                                            <label class="form-label">Terapeuta</label>
                                            <select class="form-select" name="therapist_id" id="selectTerapeuta">
                                                <option value="">Selecciona un terapeuta (opcional)</option>
                                                <?php if (isset($terapeutas)): ?>
                                                    <?php foreach ($terapeutas as $terapeuta): ?>
                                                        <option value="<?php echo $terapeuta['id']; ?>">
                                                            <?php echo htmlspecialchars($terapeuta['nombre']); ?>
                                                            <?php if ($terapeuta['especialidad']): ?>
                                                                - <?php echo htmlspecialchars($terapeuta['especialidad']); ?>
                                                            <?php endif; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="">No hay terapeutas disponibles</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <!-- ESTADO -->
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

                                        <!-- FECHA -->
                                        <div class="col-md-6">
                                            <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" required name="fecha"
                                                id="fechaReserva" min="<?php echo date('Y-m-d'); ?>"
                                                value="<?php echo date('Y-m-d'); ?>">
                                        </div>

                                        <!-- HORA -->
                                        <div class="col-md-6">
                                            <label class="form-label">Hora <span class="text-danger">*</span></label>
                                            <select class="form-select" required name="hora" id="horaReserva">
                                                <option value="">Selecciona una hora</option>
                                                <?php
                                                // Generar horas disponibles
                                                $horas = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
                                                foreach ($horas as $hora):
                                                    ?>
                                                    <option value="<?php echo $hora; ?>:00"><?php echo $hora; ?> hrs
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <!-- DURACIÓN -->
                                        <div class="col-md-12">
                                            <label class="form-label">Duración</label>
                                            <div class="form-control-plaintext" id="duracionServicio">
                                                Selecciona un servicio para ver la duración
                                            </div>
                                        </div>

                                        <!-- NOTAS -->
                                        <div class="col-12">
                                            <label class="form-label">Notas (opcional)</label>
                                            <textarea class="form-control" rows="3" placeholder="Notas adicionales..."
                                                name="notas" id="notasReserva"></textarea>
                                        </div>

                                        <!-- BOTONES -->
                                        <div class="col-12 text-end">
                                            <button type="button" class="btn btn-outline-secondary me-2"
                                                onclick="showAdminSection('reservas')">
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
                                <h2><i class="fas fa-history"></i> Historial de Actividades</h2>
                                <div class="header-actions">
                                    <button class="btn btn-outline-secondary btn-sm" onclick="exportarHistorial()">
                                        <i class="fas fa-download"></i> Exportar
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalLimpiarHistorial">
                                        <i class="fas fa-trash-alt"></i> Limpiar
                                    </button>
                                </div>
                            </div>

                            <!-- Barra de filtros mejorada -->
                            <div class="filters-bar mb-4">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small">Tipo de evento</label>
                                        <select class="form-select" id="filtroTipoHistorial"
                                            onchange="filtrarHistorial()">
                                            <option value="todos">Todos los tipos</option>
                                            <option value="reserva">Reservas</option>
                                            <option value="usuario">Usuarios</option>
                                            <option value="terapeuta">Terapeutas</option>
                                            <option value="servicio">Servicios</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label small">Evento específico</label>
                                        <select class="form-select" id="filtroEventoHistorial"
                                            onchange="filtrarHistorial()">
                                            <option value="todos">Todos los eventos</option>
                                            <option value="Reserva Creada">Creada</option>
                                            <option value="Reserva Actualizada">Actualizada</option>
                                            <option value="Reserva Eliminada">Eliminada</option>
                                            <option value="Auto-completada">Auto-completada</option>
                                            <option value="Login">Login</option>
                                            <option value="Logout">Logout</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label small">Fecha desde</label>
                                        <input type="date" class="form-control" id="filtroFechaDesde"
                                            onchange="filtrarHistorial()">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label small">Fecha hasta</label>
                                        <input type="date" class="form-control" id="filtroFechaHasta"
                                            onchange="filtrarHistorial()">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label small">Buscar</label>
                                        <div class="input-group">
                                            <input type="search" class="form-control" id="filtroBusquedaHistorial"
                                                placeholder="Usuario, detalle, IP..." onkeyup="filtrarHistorial()">
                                            <button class="btn btn-outline-secondary" type="button"
                                                onclick="filtrarHistorial()">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estadísticas rápidas -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="card card-sm bg-light">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0">Total registros</h6>
                                                    <small class="text-muted">Historial completo</small>
                                                </div>
                                                <span class="badge bg-primary" id="totalRegistros">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-sm bg-light">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0">Reservas hoy</h6>
                                                    <small class="text-muted">Acciones del día</small>
                                                </div>
                                                <span class="badge bg-success" id="reservasHoy">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-sm bg-light">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0">Últimos 7 días</h6>
                                                    <small class="text-muted">Actividad reciente</small>
                                                </div>
                                                <span class="badge bg-info" id="ultimos7Dias">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-sm bg-light">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0">Usuarios activos</h6>
                                                    <small class="text-muted">Con actividad</small>
                                                </div>
                                                <span class="badge bg-warning" id="usuariosActivos">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla del historial mejorada -->
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="60">#ID</th>
                                            <th width="120">Fecha/Hora</th>
                                            <th>Usuario</th>
                                            <th width="100">Tipo</th>
                                            <th>Evento</th>
                                            <th>Detalles</th>
                                            <th width="80">IP</th>
                                            <th width="100">Acciones</th>
                                        </tr>
                                    </thead>

                                    <tbody id="tablaHistorial">
                                        <!-- Se llena dinámicamente con JavaScript -->
                                        <tr id="loadingRow">
                                            <td colspan="8" class="text-center py-5">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Cargando...</span>
                                                </div>
                                                <p class="mt-2 text-muted">Cargando historial...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación -->
                            <nav aria-label="Paginación del historial" class="mt-4">
                                <ul class="pagination justify-content-center" id="paginacionHistorial">
                                    <!-- Se genera dinámicamente -->
                                </ul>
                            </nav>
                        </div>
                        <div id="clientes" class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-users"></i> Gestión de Clientes</h2>
                                <div class="header-actions">
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalNuevoCliente">
                                        <i class="fas fa-plus"></i> Nuevo Cliente
                                    </button>
                                </div>
                            </div>

                            <!-- Barra de filtros para clientes -->
                            <div class="filters-bar mb-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">Buscar Cliente</label>
                                        <input type="search" class="form-control" id="filtroBusquedaCliente"
                                            placeholder="Nombre, email, teléfono..." onkeyup="filtrarClientes()">
                                    </div>
                                    <div class="col-md-6 text-end d-flex align-items-end justify-content-end">
                                        <button class="btn btn-outline-secondary btn-sm" onclick="cargarClientes()">
                                            <i class="fas fa-sync-alt"></i> Recargar Lista
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Teléfono</th>
                                            <th>Fecha Nacimiento</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaClientes">
                                        <!-- Se llena con JS -->
                                        <tr>
                                            <td colspan="6" class="text-center">Cargando clientes...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="servicios" class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-spa"></i> Gestión de Servicios</h2>
                                <div class="header-actions">
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalNuevoServicio">
                                        <i class="fas fa-plus"></i> Nuevo Servicio
                                    </button>
                                </div>
                            </div>

                            <!-- Barra de filtros para servicios -->
                            <div class="filters-bar mb-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">Buscar Servicio</label>
                                        <input type="search" class="form-control" id="filtroBusquedaServicio"
                                            placeholder="Nombre, categoría..." onkeyup="filtrarServicios()">
                                    </div>
                                    <div class="col-md-6 text-end d-flex align-items-end justify-content-end">
                                        <button class="btn btn-outline-secondary btn-sm" onclick="cargarServicios()">
                                            <i class="fas fa-sync-alt"></i> Recargar Lista
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#ID</th>
                                            <th>Nombre</th>
                                            <th>Categoría</th>
                                            <th>Duración</th>
                                            <th>Precio</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaServicios">
                                        <!-- Se llena con JS -->
                                        <tr>
                                            <td colspan="7" class="text-center">Cargando servicios...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Modal Nuevo Cliente -->
                        <div class="modal fade" id="modalNuevoCliente" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="fas fa-user-plus"></i> Nuevo Cliente</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="formNuevoCliente">
                                            <div class="mb-3">
                                                <label class="form-label">Nombre Completo <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="nombre" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control" name="email" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Contraseña <span
                                                        class="text-danger">*</span></label>
                                                <input type="password" class="form-control" name="password" required>
                                            </div>
                                            <div class="text-end">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Guardar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Editar Cliente -->
                        <div class="modal fade" id="modalEditarCliente" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="fas fa-user-edit"></i> Editar Cliente</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="formEditarCliente">
                                            <input type="hidden" name="id" id="edit_cliente_id_hidden">
                                            <div class="mb-3">
                                                <label class="form-label">Nombre Completo <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="nombre"
                                                    id="edit_cliente_nombre" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control" name="email"
                                                    id="edit_cliente_email" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Teléfono</label>
                                                <input type="tel" class="form-control" name="telefono"
                                                    id="edit_cliente_telefono">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Fecha Nacimiento</label>
                                                <input type="date" class="form-control" name="fecha_nac"
                                                    id="edit_cliente_fecha_nac">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Dirección</label>
                                                <textarea class="form-control" name="direccion"
                                                    id="edit_cliente_direccion" rows="2"></textarea>
                                            </div>
                                            <div class="text-end">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Actualizar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Nuevo Servicio -->
                <div class="modal fade" id="modalNuevoServicio" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Nuevo Servicio</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="formNuevoServicio">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nombre" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Duración (min) <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="duracion_min" required
                                                min="1">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Precio <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control" name="precio" required
                                                min="0">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Categoría</label>
                                        <input type="text" class="form-control" name="categoria"
                                            placeholder="Ej: Masajes, Faciales">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Descripción</label>
                                        <textarea class="form-control" name="descripcion" rows="3"></textarea>
                                    </div>
                                    <div class="text-end">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Editar Servicio -->
                <div class="modal fade" id="modalEditarServicio" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Servicio</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="formEditarServicio">
                                    <input type="hidden" name="id" id="edit_servicio_id_hidden">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nombre" id="edit_servicio_nombre"
                                            required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Duración (min) <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="duracion_min"
                                                id="edit_servicio_duracion" required min="1">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Precio <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control" name="precio"
                                                id="edit_servicio_precio" required min="0">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Categoría</label>
                                        <input type="text" class="form-control" name="categoria"
                                            id="edit_servicio_categoria">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Descripción</label>
                                        <textarea class="form-control" name="descripcion" id="edit_servicio_descripcion"
                                            rows="3"></textarea>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="edit_servicio_activo"
                                            name="activo" value="1">
                                        <label class="form-check-label" for="edit_servicio_activo">Activo</label>
                                    </div>
                                    <div class="text-end">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Actualizar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal para ver detalles completos -->
                <div class="modal fade" id="modalDetallesCompletos" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-info-circle"></i> Detalles completos - Evento #<span
                                        id="detalleEventoId">0</span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Información básica</h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>ID:</strong></td>
                                                <td id="detalleId"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fecha/Hora:</strong></td>
                                                <td id="detalleFechaHora"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Usuario:</strong></td>
                                                <td id="detalleUsuario"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>IP:</strong></td>
                                                <td id="detalleIP"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Navegador:</strong></td>
                                                <td id="detalleNavegador"></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Valores del cambio</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card bg-light">
                                                    <div class="card-header py-2">
                                                        <small class="fw-bold">Valor anterior</small>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <pre class="mb-0" id="detalleAnterior"
                                                            style="font-size: 12px;"></pre>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card bg-light">
                                                    <div class="card-header py-2">
                                                        <small class="fw-bold">Nuevo valor</small>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <pre class="mb-0" id="detalleNuevo"
                                                            style="font-size: 12px;"></pre>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <h6>Descripción completa</h6>
                                    <div class="alert alert-light" id="detalleCompleto"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal para limpiar historial -->
                <div class="modal fade" id="modalLimpiarHistorial" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-trash-alt"></i> Limpiar historial</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Esta acción no se puede deshacer. Se eliminarán registros antiguos del
                                    historial.
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Eliminar registros más antiguos que:</label>
                                    <select class="form-select" id="diasLimpiar">
                                        <option value="30">30 días</option>
                                        <option value="90" selected>90 días</option>
                                        <option value="180">180 días</option>
                                        <option value="365">1 año</option>
                                    </select>
                                    <div class="form-text">
                                        Se conservarán los registros recientes.
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Esta acción solo elimina registros del historial, no afecta las reservas,
                                    usuarios u otros datos.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-danger" onclick="limpiarHistorial()">
                                    <i class="fas fa-trash-alt"></i> Limpiar historial
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
            </div>
            <div class="modal fade" id="modalEditarReservaAdmin" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Reserva #<span
                                    id="edit_reserva_id_display">000</span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formEditarReservaAdmin" method="POST" action="/api/reservas/update">
                                <input type="hidden" name="id" id="edit_reserva_id">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Cliente</label>
                                        <select class="form-select" name="cliente_id" id="edit_cliente_id">
                                            <?php if (isset($clientes)): ?>
                                                <?php foreach ($clientes as $cliente): ?>
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
                                            <?php if (isset($servicios)): ?>
                                                <?php foreach ($servicios as $servicio): ?>
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
                                            <?php if (isset($terapeutas)): ?>
                                                <?php foreach ($terapeutas as $terapeuta): ?>
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
            <!-- Después de Bootstrap y otros scripts -->
            <script src="../public/scripts/historial.js"></script>

            <script>
                // Funcionalidad adicional para la vista
                document.addEventListener('DOMContentLoaded', function () {
                    // Mostrar duración del servicio seleccionado
                    const selectServicio = document.getElementById('selectServicio');
                    const duracionServicio = document.getElementById('duracionServicio');

                    if (selectServicio && duracionServicio) {
                        selectServicio.addEventListener('change', function () {
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
                        fechaInput.addEventListener('change', function () {
                            if (this.value) {
                                cargarHorariosDisponibles(this.value);
                            }
                        });
                    }

                    // También para el formulario de edición
                    const editFechaInput = document.getElementById('edit_fecha');
                    const editHoraSelect = document.getElementById('edit_hora');

                    if (editFechaInput && editHoraSelect) {
                        editFechaInput.addEventListener('change', function () {
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
            <script>
                // ------------------------------------------------------------------
                // LÓGICA DE CLIENTES (JS embebido por brevedad)
                // ------------------------------------------------------------------

                // Cargar clientes al mostrar la sección
                function showAdminSection(sectionId) {
                    // Ocultar todas las secciones
                    document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));

                    // Quitar active de menú
                    document.querySelectorAll('.sidebar-menu li').forEach(l => l.classList.remove('active'));

                    // Mostrar la seleccionada
                    const section = document.getElementById(sectionId);
                    if (section) section.classList.add('active');

                    // Marcar menú activo (simple match)
                    const menuItem = document.querySelector(`.sidebar-menu li[onclick*="'${sectionId}'"]`);
                    if (menuItem) menuItem.classList.add('active');

                    // Si es clientes, cargar datos
                    if (sectionId === 'clientes') {
                        cargarClientes();
                    }
                }

                async function cargarClientes() {
                    const tbody = document.getElementById('tablaClientes');
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">Cargando...</td></tr>';

                    try {
                        const res = await fetch('/api/clientes');
                        const clientes = await res.json();

                        if (!clientes || clientes.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay clientes registrados.</td></tr>';
                            return;
                        }

                        let html = '';
                        clientes.forEach(c => {
                            html += `
                    <tr>
                        <td>#${c.id}</td>
                        <td>${c.nombre}</td>
                        <td>${c.email}</td>
                        <td>${c.telefono || '-'}</td>
                        <td>${c.fecha_nac || '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="abrirEditarCliente(${c.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="eliminarCliente(${c.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    `;
                        });
                        tbody.innerHTML = html;

                    } catch (error) {
                        console.error(error);
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar clientes.</td></tr>';
                    }
                }

                // Filtrar clientes
                function filtrarClientes() {
                    const busqueda = document.getElementById('filtroBusquedaCliente').value.toLowerCase();
                    const filas = document.querySelectorAll('#tablaClientes tr');

                    filas.forEach(fila => {
                        const texto = fila.textContent.toLowerCase();
                        fila.style.display = texto.includes(busqueda) ? '' : 'none';
                    });
                }

                // Crear Cliente
                const formNuevoCliente = document.getElementById('formNuevoCliente');
                if (formNuevoCliente) {
                    formNuevoCliente.addEventListener('submit', async function (e) {
                        e.preventDefault();
                        const data = new FormData(this);

                        try {
                            const res = await fetch('/api/clientes/create', {
                                method: 'POST',
                                body: data
                            });
                            const json = await res.json();

                            if (res.ok && json.status === 'ok') {
                                alert('Cliente creado exitosamente');
                                const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoCliente'));
                                modal.hide();
                                this.reset();
                                cargarClientes();
                            } else {
                                alert('Error: ' + (json.error || 'Desconocido'));
                            }
                        } catch (err) {
                            alert('Error de conexión');
                        }
                    });
                }

                // Cargar datos para editar
                async function abrirEditarCliente(id) {
                    try {
                        const res = await fetch(`/api/clientes/show?id=${id}`);
                        const json = await res.json();

                        if (res.ok) {
                            document.getElementById('edit_cliente_id_hidden').value = json.id;
                            document.getElementById('edit_cliente_nombre').value = json.nombre;
                            document.getElementById('edit_cliente_email').value = json.email;
                            document.getElementById('edit_cliente_telefono').value = json.telefono || '';
                            document.getElementById('edit_cliente_fecha_nac').value = json.fecha_nac || '';
                            document.getElementById('edit_cliente_direccion').value = json.direccion || ''; // Campo direccion añadido en el form

                            const modal = new bootstrap.Modal(document.getElementById('modalEditarCliente'));
                            modal.show();
                        } else {
                            alert('Error al cargar cliente');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Error de conexión');
                    }
                }

                // Guardar Edición
                const formEditarCliente = document.getElementById('formEditarCliente');
                if (formEditarCliente) {
                    formEditarCliente.addEventListener('submit', async function (e) {
                        e.preventDefault();
                        const data = new FormData(this);

                        try {
                            const res = await fetch('/api/clientes/update', {
                                method: 'POST',
                                body: data
                            });
                            const json = await res.json();

                            if (res.ok && json.status === 'ok') {
                                alert('Cliente actualizado');
                                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarCliente'));
                                modal.hide();
                                cargarClientes();
                            } else {
                                alert('Error: ' + (json.error || 'No se pudo actualizar'));
                            }
                        } catch (err) {
                            alert('Error de conexión');
                        }
                    });
                }

                // Eliminar Cliente
                async function eliminarCliente(id) {
                    if (!confirm('¿Seguro que deseas eliminar este cliente? Esta acción no se puede deshacer.')) return;

                    try {
                        const data = new FormData();
                        data.append('id', id);

                        const res = await fetch('/api/clientes/delete', {
                            method: 'POST',
                            body: data
                        });
                        const json = await res.json();

                        if (res.ok && json.status === 'ok') {
                            alert('Cliente eliminado');
                            cargarClientes();
                        } else {
                            alert('Error: ' + (json.error || 'No se pudo eliminar'));
                        }
                    } catch (err) {
                        alert('Error al eliminar');
                    }
                }
            </script>
            <script>
                // ------------------------------------------------------------------
                // LÓGICA DE SERVICIOS
                // ------------------------------------------------------------------

                // Modificar showAdminSection para incluir servicios
                // Guardamos la referencia original si no existe ya un wrapper
                if (typeof originalShowAdminSection === 'undefined') {
                    var originalShowAdminSection = showAdminSection;
                    showAdminSection = function (sectionId) {
                        originalShowAdminSection(sectionId);
                        if (sectionId === 'servicios') {
                            cargarServicios();
                        }
                    };
                }

                async function cargarServicios() {
                    const tbody = document.getElementById('tablaServicios');
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center">Cargando...</td></tr>';

                    try {
                        const res = await fetch('/api/servicios');
                        const servicios = await res.json();

                        if (!servicios || servicios.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No hay servicios registrados.</td></tr>';
                            return;
                        }

                        let html = '';
                        servicios.forEach(s => {
                            const estadoBadge = s.activo == 1
                                ? '<span class="badge bg-success">Activo</span>'
                                : '<span class="badge bg-secondary">Inactivo</span>';

                            html += `
                    <tr>
                        <td>#${s.id}</td>
                        <td>${s.nombre}</td>
                        <td>${s.categoria || '-'}</td>
                        <td>${s.duracion_min} min</td>
                        <td>$${parseFloat(s.precio).toFixed(2)}</td>
                        <td>${estadoBadge}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="abrirEditarServicio(${s.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="eliminarServicio(${s.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    `;
                        });
                        tbody.innerHTML = html;

                    } catch (error) {
                        console.error(error);
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error al cargar servicios.</td></tr>';
                    }
                }

                function filtrarServicios() {
                    const busqueda = document.getElementById('filtroBusquedaServicio').value.toLowerCase();
                    const filas = document.querySelectorAll('#tablaServicios tr');

                    filas.forEach(fila => {
                        const texto = fila.textContent.toLowerCase();
                        fila.style.display = texto.includes(busqueda) ? '' : 'none';
                    });
                }

                // Crear Servicio
                const formNuevoServicio = document.getElementById('formNuevoServicio');
                if (formNuevoServicio) {
                    formNuevoServicio.addEventListener('submit', async function (e) {
                        e.preventDefault();
                        const data = new FormData(this);

                        try {
                            const res = await fetch('/api/servicios/create', {
                                method: 'POST',
                                body: data
                            });
                            const json = await res.json();

                            if (res.ok && json.status === 'ok') {
                                alert('Servicio creado exitosamente');
                                const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoServicio'));
                                modal.hide();
                                this.reset();
                                cargarServicios();
                            } else {
                                alert('Error: ' + (json.error || 'Desconocido'));
                            }
                        } catch (err) {
                            alert('Error de conexión');
                        }
                    });
                }

                // Cargar servicio para editar
                async function abrirEditarServicio(id) {
                    try {
                        const res = await fetch(`/api/servicios/show?id=${id}`);
                        const json = await res.json();

                        if (res.ok) {
                            document.getElementById('edit_servicio_id_hidden').value = json.id;
                            document.getElementById('edit_servicio_nombre').value = json.nombre;
                            document.getElementById('edit_servicio_duracion').value = json.duracion_min;
                            document.getElementById('edit_servicio_precio').value = json.precio;
                            document.getElementById('edit_servicio_categoria').value = json.categoria || '';
                            document.getElementById('edit_servicio_descripcion').value = json.descripcion || '';
                            document.getElementById('edit_servicio_activo').checked = (json.activo == 1);

                            const modal = new bootstrap.Modal(document.getElementById('modalEditarServicio'));
                            modal.show();
                        } else {
                            alert('Error al cargar servicio');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Error de conexión');
                    }
                }

                // Actualizar Servicio
                const formEditarServicio = document.getElementById('formEditarServicio');
                if (formEditarServicio) {
                    formEditarServicio.addEventListener('submit', async function (e) {
                        e.preventDefault();

                        const data = new FormData(this);
                        if (!document.getElementById('edit_servicio_activo').checked) {
                            data.append('activo', '0');
                        }

                        try {
                            const res = await fetch('/api/servicios/update', {
                                method: 'POST',
                                body: data
                            });
                            const json = await res.json();

                            if (res.ok && json.status === 'ok') {
                                alert('Servicio actualizado');
                                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarServicio'));
                                modal.hide();
                                cargarServicios();
                            } else {
                                alert('Error: ' + (json.error || 'No se pudo actualizar'));
                            }
                        } catch (err) {
                            alert('Error de conexión');
                        }
                    });
                }

                // Eliminar Servicio
                async function eliminarServicio(id) {
                    if (!confirm('¿Seguro que deseas eliminar este servicio? Esta acción puede fallar si existen reservas asociadas.')) return;

                    try {
                        const data = new FormData();
                        data.append('id', id);

                        const res = await fetch('/api/servicios/delete', {
                            method: 'POST',
                            body: data
                        });
                        const json = await res.json();

                        if (res.ok && json.status === 'ok') {
                            alert('Servicio eliminado');
                            cargarServicios();
                        } else {
                            alert('Error: ' + (json.error || 'No se pudo eliminar'));
                        }
                    } catch (err) {
                        alert('Error al eliminar');
                    }
                }
            </script>
</body>

</html>