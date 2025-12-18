<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - Aruma Spa</title>
    <link href="../public/estilos/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../public/estilos/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="../index.html">
                <i class="fas fa-spa"></i> ARUMA
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../index.html">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="../index.html#servicios">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link" href="../index.html#contacto">Contacto</a></li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cliente.html">
                            <i class="fas fa-user"></i> Mi Cuenta
                        </a>
                    </li>
                </ul>
                <button class="btn btn-login ms-3" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Small -->
    <section class="hero-small">
        <div class="container">
            <div class="row align-items-center justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="hero-title">Bienvenido, <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></h1>
                    <p class="hero-subtitle">Gestiona tus reservas fácilmente</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Panel Cliente -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3 mb-4">
                    <div class="admin-sidebar">
                        <div class="sidebar-header">
                            <i class="fas fa-user-circle"></i>
                            <h5>Panel Cliente</h5>
                        </div>
                        <ul class="sidebar-menu">
                            <li class="active" onclick="showSection('mis-reservas')">
                                <i class="fas fa-calendar-alt"></i> Mis Reservas
                            </li>
                            <li onclick="showSection('nueva-reserva')">
                                <i class="fas fa-plus-circle"></i> Nueva Reserva
                            </li>
                            <li>
                                <a href="/mi-perfil" class="text-decoration-none text-dark">
                                    <i class="fas fa-user"></i> Mi Perfil
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Contenido Principal -->
                <div class="col-lg-9">
                    <!-- Mis Reservas -->
                    <div id="mis-reservas" class="content-section active">
                        <div class="section-header">
                            <h2><i class="fas fa-calendar-alt"></i> Mis Reservas</h2>
                            <button class="btn btn-primary" onclick="showSection('nueva-reserva')">
                                <i class="fas fa-plus"></i> Nueva Reserva
                            </button>
                        </div>

                        <div class="row g-3">
                            <!-- Reserva 1 -->
                            <div class="col-md-6">
                                <div class="reserva-card">
                                    <div class="reserva-header">
                                        <span class="badge bg-success">Confirmada</span>
                                        <span class="reserva-id">#001</span>
                                    </div>
                                    <div class="reserva-body">
                                        <h4>Masaje Relajante</h4>
                                        <div class="reserva-info">
                                            <div class="info-item">
                                                <i class="fas fa-calendar"></i>
                                                <span>25 de Octubre, 2024</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-clock"></i>
                                                <span>14:00 - 15:00</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-user-md"></i>
                                                <span>Terapeuta: María García</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="reserva-footer">
                                        <button class="btn btn-sm btn-outline-primary" onclick="editarReserva(1)">
                                            <i class="fas fa-edit"></i> Modificar
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="cancelarReserva(1)">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Reserva 2 -->
                            <div class="col-md-6">
                                <div class="reserva-card">
                                    <div class="reserva-header">
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                        <span class="reserva-id">#002</span>
                                    </div>
                                    <div class="reserva-body">
                                        <h4>Tratamiento Facial</h4>
                                        <div class="reserva-info">
                                            <div class="info-item">
                                                <i class="fas fa-calendar"></i>
                                                <span>28 de Octubre, 2024</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-clock"></i>
                                                <span>10:00 - 11:30</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-user-md"></i>
                                                <span>Terapeuta: Ana López</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="reserva-footer">
                                        <button class="btn btn-sm btn-outline-primary" onclick="editarReserva(2)">
                                            <i class="fas fa-edit"></i> Modificar
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="cancelarReserva(2)">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nueva Reserva -->
                    <div id="nueva-reserva" class="content-section">
                        <div class="section-header">
                            <h2><i class="fas fa-plus-circle"></i> Nueva Reserva</h2>
                        </div>

                        <div class="form-card">
                            <form id="formNuevaReserva">
                                <div class="row g-3">
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
                                        <label class="form-label">Notas Adicionales (Opcional)</label>
                                        <textarea class="form-control" rows="3" placeholder="Alguna preferencia o comentario especial..."></textarea>
                                    </div>

                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Nota:</strong> Tu reserva será confirmada en un plazo de 24 horas. Recibirás un correo de confirmación.
                                        </div>
                                    </div>

                                    <div class="col-12 text-end">
                                        <button type="button" class="btn btn-outline-secondary me-2" onclick="showSection('mis-reservas')">
                                            Cancelar
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-check"></i> Confirmar Reserva
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Mi Perfil -->
                    <div id="mi-perfil" class="content-section">
                        <div class="section-header">
                            <h2><i class="fas fa-user"></i> Mi Perfil</h2>
                        </div>

                        <div class="form-card">
                            <form>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control" value="Juan Pérez" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" value="juan@ejemplo.com" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" value="+52 555 123 4567" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" value="1990-05-15">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Dirección</label>
                                        <input type="text" class="form-control" value="Av. Principal 123, Centro">
                                    </div>

                                    <div class="col-12">
                                        <hr>
                                        <h5>Cambiar Contraseña</h5>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Contraseña Actual</label>
                                        <input type="password" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Nueva Contraseña</label>
                                        <input type="password" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Confirmar Contraseña</label>
                                        <input type="password" class="form-control">
                                    </div>

                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Editar Reserva -->
    <div class="modal fade" id="modalEditarReserva" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Modificar Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarReserva">
                        <div class="mb-3">
                            <label class="form-label">Servicio</label>
                            <select class="form-select">
                                <option>Masaje Relajante - 60 min - $800</option>
                                <option>Masaje Piedras Calientes - 90 min - $1200</option>
                                <option>Tratamiento Facial - 90 min - $1000</option>
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
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarCambios()">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="text-center">
                <p>&copy; 2024 Aruma Spa. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="reservas.js"></script>
</body>

</html>