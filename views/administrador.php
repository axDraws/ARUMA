<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador - Aruma Spa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../estilos/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.html">
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

    <!-- Dashboard -->
    <section class="admin-dashboard">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
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

                <!-- Contenido Principal -->
                <div class="col-lg-10">
                    <div class="admin-content">
                        <!-- Dashboard -->
                        <div id="dashboard" class="content-section active">
                            <div class="section-header">
                                <h2><i class="fas fa-chart-line"></i> Dashboard</h2>
                                <div class="date-display">
                                    <i class="fas fa-calendar"></i> Hoy: <span id="fecha-actual"></span>
                                </div>
                            </div>

                            <!-- Estadísticas -->
                            <div class="row g-4 mb-4">
                                <div class="col-md-3">
                                    <div class="stat-card stat-primary">
                                        <div class="stat-icon">
                                            <i class="fas fa-calendar-day"></i>
                                        </div>
                                        <div class="stat-info">
                                            <h3>12</h3>
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
                                            <h3>45</h3>
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
                                            <h3>8</h3>
                                            <p>Pendientes</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-card stat-info">
                                        <div class="stat-icon">
                                            <i class="fas fa-dollar-sign"></i>
                                        </div>
                                        <div class="stat-info">
                                            <h3>$48,500</h3>
                                            <p>Ingresos Mes</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reservas del Día -->
                            <div class="dashboard-section">
                                <h4><i class="fas fa-calendar-day"></i> Reservas de Hoy</h4>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Hora</th>
                                                <th>Cliente</th>
                                                <th>Servicio</th>
                                                <th>Terapeuta</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>09:00</td>
                                                <td>María González</td>
                                                <td>Masaje Relajante</td>
                                                <td>Ana López</td>
                                                <td><span class="badge bg-success">Confirmada</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>10:30</td>
                                                <td>Carlos Ruiz</td>
                                                <td>Hidroterapia</td>
                                                <td>María García</td>
                                                <td><span class="badge bg-warning text-dark">En Proceso</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>14:00</td>
                                                <td>Laura Martínez</td>
                                                <td>Tratamiento Facial</td>
                                                <td>Ana López</td>
                                                <td><span class="badge bg-info">Confirmada</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Gestión de Reservas -->
                        <div id="reservas" class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-calendar-check"></i> Gestión de Reservas</h2>
                                <div class="header-actions">
                                    <button class="btn btn-primary" onclick="showAdminSection('nueva-reserva-admin')">
                                        <i class="fas fa-plus"></i> Nueva Reserva
                                    </button>
                                </div>
                            </div>

                            <!-- Filtros -->
                            <div class="filters-bar">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <select class="form-select">
                                            <option>Todas las Reservas</option>
                                            <option>Confirmadas</option>
                                            <option>Pendientes</option>
                                            <option>Canceladas</option>
                                            <option>Completadas</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select">
                                            <option>Todos los Servicios</option>
                                            <option>Masaje Relajante</option>
                                            <option>Tratamiento Facial</option>
                                            <option>Hidroterapia</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="search" class="form-control" placeholder="Buscar cliente...">
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla de Reservas -->
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
                                        <tr>
                                            <td>#001</td>
                                            <td>25/10/2024</td>
                                            <td>14:00</td>
                                            <td>Juan Pérez</td>
                                            <td>Masaje Relajante</td>
                                            <td>María García</td>
                                            <td><span class="badge bg-success">Confirmada</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" onclick="verDetalles(1)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-primary" onclick="editarReservaAdmin(1)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarReserva(1)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#002</td>
                                            <td>28/10/2024</td>
                                            <td>10:00</td>
                                            <td>Ana Martínez</td>
                                            <td>Tratamiento Facial</td>
                                            <td>Ana López</td>
                                            <td><span class="badge bg-warning text-dark">Pendiente</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" onclick="verDetalles(2)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-primary" onclick="editarReservaAdmin(2)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarReserva(2)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#003</td>
                                            <td>30/10/2024</td>
                                            <td>16:00</td>
                                            <td>Pedro Sánchez</td>
                                            <td>Aromaterapia</td>
                                            <td>Laura Fernández</td>
                                            <td><span class="badge bg-info">Confirmada</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" onclick="verDetalles(3)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-primary" onclick="editarReservaAdmin(3)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarReserva(3)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#004</td>
                                            <td>26/10/2024</td>
                                            <td>11:00</td>
                                            <td>Sofia Torres</td>
                                            <td>Reflexología</td>
                                            <td>Carlos Martínez</td>
                                            <td><span class="badge bg-secondary">Cancelada</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" onclick="verDetalles(4)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-primary" disabled>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarReserva(4)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Nueva Reserva Admin -->
                        <div id="nueva-reserva-admin" class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-plus-circle"></i> Crear Nueva Reserva</h2>
                            </div>

                            <div class="form-card">
                                <form id="formNuevaReservaAdmin">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Cliente</label>
                                            <select class="form-select" required>
                                                <option value="">Selecciona un cliente</option>
                                                <option>Juan Pérez</option>
                                                <option>Ana Martínez</option>
                                                <option>Pedro Sánchez</option>
                                                <option>Sofia Torres</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Servicio</label>
                                            <select class="form-select" required>
                                                <option value="">Selecciona un servicio</option>
                                                <option>Masaje Relajante - 60 min - $800</option>
                                                <option>Masaje Piedras Calientes - 90 min - $1200</option>
                                                <option>Tratamiento Facial - 90 min - $1000</option>
                                                <option>Hidroterapia - 45 min - $600</option>
                                                <option>Aromaterapia - 60 min - $750</option>
                                                <option>Reflexología - 60 min - $700</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Terapeuta</label>
                                            <select class="form-select" required>
                                                <option value="">Selecciona un terapeuta</option>
                                                <option>María García</option>
                                                <option>Ana López</option>
                                                <option>Carlos Martínez</option>
                                                <option>Laura Fernández</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Estado</label>
                                            <select class="form-select" required>
                                                <option value="">Selecciona estado</option>
                                                <option selected>Confirmada</option>
                                                <option>Pendiente</option>
                                                <option>En Proceso</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Fecha</label>
                                            <input type="date" class="form-control" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Hora</label>
                                            <select class="form-select" required>
                                                <option value="">Selecciona una hora</option>
                                                <option>09:00 AM</option>
                                                <option>10:00 AM</option>
                                                <option>11:00 AM</option>
                                                <option>12:00 PM</option>
                                                <option>02:00 PM</option>
                                                <option>03:00 PM</option>
                                                <option>04:00 PM</option>
                                                <option>05:00 PM</option>
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Notas</label>
                                            <textarea class="form-control" rows="3" placeholder="Notas adicionales..."></textarea>
                                        </div>

                                        <div class="col-12 text-end">
                                            <button type="button" class="btn btn-outline-secondary me-2" onclick="showAdminSection('reservas')">
                                                Cancelar
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Guardar Reserva
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Historial -->
                        <div id="historial" class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-history"></i> Historial de Reservas</h2>
                            </div>

                            <div class="filters-bar">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <input type="date" class="form-control" placeholder="Desde">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" class="form-control" placeholder="Hasta">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select">
                                            <option>Todas las Reservas</option>
                                            <option>Completadas</option>
                                            <option>Canceladas</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-primary w-100">
                                            <i class="fas fa-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#ID</th>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Servicio</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>#120</td>
                                            <td>20/10/2024</td>
                                            <td>María González</td>
                                            <td>Masaje Relajante</td>
                                            <td>$800</td>
                                            <td><span class="badge bg-success">Completada</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" onclick="verDetalles(120)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#119</td>
                                            <td>19/10/2024</td>
                                            <td>Carlos Ruiz</td>
                                            <td>Tratamiento Facial</td>
                                            <td>$1000</td>
                                            <td><span class="badge bg-success">Completada</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" onclick="verDetalles(119)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#118</td>
                                            <td>18/10/2024</td>
                                            <td>Laura Martínez</td>
                                            <td>Hidroterapia</td>
                                            <td>$600</td>
                                            <td><span class="badge bg-secondary">Cancelada</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" onclick="verDetalles(118)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#117</td>
                                            <td>17/10/2024</td>
                                            <td>Pedro Sánchez</td>
                                            <td>Aromaterapia</td>
                                            <td>$750</td>
                                            <td><span class="badge bg-success">Completada</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" onclick="verDetalles(117)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#116</td>
                                            <td>16/10/2024</td>
                                            <td>Sofia Torres</td>
                                            <td>Reflexología</td>
                                            <td>$700</td>
                                            <td><span class="badge bg-success">Completada</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" onclick="verDetalles(116)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Clientes -->
                        <div id="clientes" class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-users"></i> Gestión de Clientes</h2>
                                <button class="btn btn-primary" onclick="agregarCliente()">
                                    <i class="fas fa-user-plus"></i> Agregar Cliente
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Teléfono</th>
                                            <th>Reservas</th>
                                            <th>Última Visita</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>#C001</td>
                                            <td>Juan Pérez</td>
                                            <td>juan@ejemplo.com</td>
                                            <td>+52 555 123 4567</td>
                                            <td>15</td>
                                            <td>25/10/2024</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" onclick="verPerfilCliente(1)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-primary" onclick="editarCliente(1)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarCliente(1)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#C002</td>
                                            <td>Ana Martínez</td>
                                            <td>ana@ejemplo.com</td>
                                            <td>+52 555 987 6543</td>
                                            <td>8</td>
                                            <td>28/10/2024</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" onclick="verPerfilCliente(2)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-primary" onclick="editarCliente(2)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarCliente(2)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#C003</td>
                                            <td>Pedro Sánchez</td>
                                            <td>pedro@ejemplo.com</td>
                                            <td>+52 555 456 7890</td>
                                            <td>12</td>
                                            <td>30/10/2024</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" onclick="verPerfilCliente(3)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-primary" onclick="editarCliente(3)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarCliente(3)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#C004</td>
                                            <td>Sofia Torres</td>
                                            <td>sofia@ejemplo.com</td>
                                            <td>+52 555 321 0987</td>
                                            <td>5</td>
                                            <td>26/10/2024</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" onclick="verPerfilCliente(4)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-primary" onclick="editarCliente(4)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarCliente(4)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Servicios -->
                        <div id="servicios" class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-spa"></i> Gestión de Servicios</h2>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="service-admin-card">
                                        <div class="service-admin-header">
                                            <h4>Masaje Relajante</h4>
                                            <span class="badge bg-success">Activo</span>
                                        </div>
                                        <div class="service-admin-body">
                                            <p><strong>Duración:</strong> 60 minutos</p>
                                            <p><strong>Precio:</strong> $800</p>
                                            <p><strong>Categoría:</strong> Masajes</p>
                                        </div>
                                        <div class="service-admin-footer">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editarServicio(1)">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" onclick="toggleServicio(1)">
                                                <i class="fas fa-power-off"></i> Desactivar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="service-admin-card">
                                        <div class="service-admin-header">
                                            <h4>Tratamiento Facial</h4>
                                            <span class="badge bg-success">Activo</span>
                                        </div>
                                        <div class="service-admin-body">
                                            <p><strong>Duración:</strong> 90 minutos</p>
                                            <p><strong>Precio:</strong> $1000</p>
                                            <p><strong>Categoría:</strong> Faciales</p>
                                        </div>
                                        <div class="service-admin-footer">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editarServicio(2)">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" onclick="toggleServicio(2)">
                                                <i class="fas fa-power-off"></i> Desactivar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="service-admin-card">
                                        <div class="service-admin-header">
                                            <h4>Hidroterapia</h4>
                                            <span class="badge bg-success">Activo</span>
                                        </div>
                                        <div class="service-admin-body">
                                            <p><strong>Duración:</strong> 45 minutos</p>
                                            <p><strong>Precio:</strong> $600</p>
                                            <p><strong>Categoría:</strong> Terapias</p>
                                        </div>
                                        <div class="service-admin-footer">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editarServicio(3)">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" onclick="toggleServicio(3)">
                                                <i class="fas fa-power-off"></i> Desactivar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="service-admin-card">
                                        <div class="service-admin-header">
                                            <h4>Aromaterapia</h4>
                                            <span class="badge bg-success">Activo</span>
                                        </div>
                                        <div class="service-admin-body">
                                            <p><strong>Duración:</strong> 60 minutos</p>
                                            <p><strong>Precio:</strong> $750</p>
                                            <p><strong>Categoría:</strong> Terapias</p>
                                        </div>
                                        <div class="service-admin-footer">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editarServicio(4)">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" onclick="toggleServicio(4)">
                                                <i class="fas fa-power-off"></i> Desactivar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="service-admin-card">
                                        <div class="service-admin-header">
                                            <h4>Reflexología</h4>
                                            <span class="badge bg-success">Activo</span>
                                        </div>
                                        <div class="service-admin-body">
                                            <p><strong>Duración:</strong> 60 minutos</p>
                                            <p><strong>Precio:</strong> $700</p>
                                            <p><strong>Categoría:</strong> Terapias</p>
                                        </div>
                                        <div class="service-admin-footer">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editarServicio(5)">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" onclick="toggleServicio(5)">
                                                <i class="fas fa-power-off"></i> Desactivar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="service-admin-card">
                                        <div class="service-admin-header">
                                            <h4>Piedras Calientes</h4>
                                            <span class="badge bg-secondary">Inactivo</span>
                                        </div>
                                        <div class="service-admin-body">
                                            <p><strong>Duración:</strong> 90 minutos</p>
                                            <p><strong>Precio:</strong> $1200</p>
                                            <p><strong>Categoría:</strong> Masajes</p>
                                        </div>
                                        <div class="service-admin-footer">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editarServicio(6)">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button class="btn btn-sm btn-outline-success" onclick="toggleServicio(6)">
                                                <i class="fas fa-power-off"></i> Activar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Ver Detalles -->
    <div class="modal fade" id="modalDetalles" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle"></i> Detalles de Reserva #001</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Información del Cliente</h6>
                            <p><strong>Nombre:</strong> Juan Pérez</p>
                            <p><strong>Email:</strong> juan@ejemplo.com</p>
                            <p><strong>Teléfono:</strong> +52 555 123 4567</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Información de la Reserva</h6>
                            <p><strong>Servicio:</strong> Masaje Relajante</p>
                            <p><strong>Fecha:</strong> 25/10/2024</p>
                            <p><strong>Hora:</strong> 14:00 - 15:00</p>
                            <p><strong>Terapeuta:</strong> María García</p>
                            <p><strong>Estado:</strong> <span class="badge bg-success">Confirmada</span></p>
                            <p><strong>Total:</strong> $800</p>
                        </div>
                        <div class="col-12">
                            <hr>
                            <h6 class="text-muted">Notas</h6>
                            <p>El cliente prefiere masaje de presión media. Alergia a aceites con fragancia de lavanda.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="editarReservaAdmin(1)">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Reserva Admin -->
    <div class="modal fade" id="modalEditarReservaAdmin" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Reserva #001</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarReservaAdmin">
                        <div class="mb-3">
                            <label class="form-label">Cliente</label>
                            <select class="form-select">
                                <option selected>Juan Pérez</option>
                                <option>Ana Martínez</option>
                                <option>Pedro Sánchez</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Servicio</label>
                            <select class="form-select">
                                <option selected>Masaje Relajante - $800</option>
                                <option>Tratamiento Facial - $1000</option>
                                <option>Hidroterapia - $600</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select">
                                <option selected>Confirmada</option>
                                <option>Pendiente</option>
                                <option>En Proceso</option>
                                <option>Completada</option>
                                <option>Cancelada</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control" value="2024-10-25">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hora</label>
                            <select class="form-select">
                                <option>09:00 AM</option>
                                <option selected>02:00 PM</option>
                                <option>03:00 PM</option>
                                <option>04:00 PM</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Terapeuta</label>
                            <select class="form-select">
                                <option selected>María García</option>
                                <option>Ana López</option>
                                <option>Carlos Martínez</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notas</label>
                            <textarea class="form-control" rows="3">El cliente prefiere masaje de presión media.</textarea>
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

    <!-- Modal Agregar Cliente -->
    <div class="modal fade" id="modalAgregarCliente" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus"></i> Agregar Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formAgregarCliente">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" required placeholder="Ej: Juan Pérez">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" required placeholder="ejemplo@correo.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" required placeholder="+52 555 123 4567">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" placeholder="Calle, Número, Colonia">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notas (Alergias, Preferencias, etc.)</label>
                            <textarea class="form-control" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="formAgregarCliente" class="btn btn-primary">
                        <i class="fas fa-save"></i> Agregar Cliente
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Perfil Cliente -->
    <div class="modal fade" id="modalPerfilCliente" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user"></i> Perfil de Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Información Personal</h6>
                            <p><strong>Nombre:</strong> Juan Pérez</p>
                            <p><strong>Email:</strong> juan@ejemplo.com</p>
                            <p><strong>Teléfono:</strong> +52 555 123 4567</p>
                            <p><strong>Fecha Nacimiento:</strong> 15/05/1990</p>
                            <p><strong>Dirección:</strong> Av. Principal 123, Centro</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Estadísticas</h6>
                            <p><strong>Cliente desde:</strong> Enero 2023</p>
                            <p><strong>Total de Reservas:</strong> 15</p>
                            <p><strong>Última Visita:</strong> 25/10/2024</p>
                            <p><strong>Gasto Total:</strong> $12,800</p>
                            <p><strong>Servicio Favorito:</strong> Masaje Relajante</p>
                        </div>
                        <div class="col-12">
                            <hr>
                            <h6 class="text-muted">Historial de Reservas Recientes</h6>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Servicio</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>25/10/2024</td>
                                        <td>Masaje Relajante</td>
                                        <td><span class="badge bg-success">Completada</span></td>
                                    </tr>
                                    <tr>
                                        <td>18/10/2024</td>
                                        <td>Hidroterapia</td>
                                        <td><span class="badge bg-success">Completada</span></td>
                                    </tr>
                                    <tr>
                                        <td>10/10/2024</td>
                                        <td>Tratamiento Facial</td>
                                        <td><span class="badge bg-success">Completada</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="editarCliente(1)">
                        <i class="fas fa-edit"></i> Editar Cliente
                    </button>
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
                        <div class="mb-3">
                            <label class="form-label">Nombre del Servicio</label>
                            <input type="text" class="form-control" value="Masaje Relajante">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Categoría</label>
                            <select class="form-select">
                                <option selected>Masajes</option>
                                <option>Faciales</option>
                                <option>Terapias</option>
                                <option>Tratamientos</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duración (minutos)</label>
                            <input type="number" class="form-control" value="60">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio</label>
                            <input type="number" class="form-control" value="800">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" rows="3">Masaje terapéutico diseñado para liberar tensiones y promover la relajación profunda.</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select">
                                <option selected>Activo</option>
                                <option>Inactivo</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="formEditarServicio" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../scripts/reservas.js"></script>
</body>
</html>
