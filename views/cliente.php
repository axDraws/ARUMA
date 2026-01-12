<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /");
    exit();
}
?>
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
            <a class="navbar-brand" href="../views/home.php">
                <i class="fas fa-spa"></i> ARUMA
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../views/mision-vision.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="../views/home#servicios">Servicios</a></li>
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
                            <li onclick="showSection('mi-perfil')">
                                <i class="fas fa-user"></i> Mi Perfil
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

                        <div class="row g-3" id="mis-reservas-container">
                            <!-- Las reservas se cargarán aquí dinámicamente -->
                            <div class="col-12 text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2">Cargando tus reservas...</p>
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
                                        <select class="form-select" id="reservaServicio" name="servicio_id" required>
                                            <option value="">Cargando servicios...</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Terapeuta (Opcional)</label>
                                        <select class="form-select" id="reservaTerapeuta" name="therapist_id">
                                            <option value="">Cualquiera disponible</option>
                                            <!-- Se puede poblar dinámicamente si se desea -->
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Fecha</label>
                                        <input type="date" class="form-control" name="fecha" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Hora</label>
                                        <select class="form-select" id="reservaHora" name="hora" required>
                                            <option value="">Selecciona una hora</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Notas Adicionales (Opcional)</label>
                                        <textarea class="form-control" name="notas" rows="3"
                                            placeholder="Alguna preferencia o comentario especial..."></textarea>
                                    </div>

                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Nota:</strong> Tu reserva será confirmada en un plazo de 24 horas.
                                            Recibirás un correo de confirmación.
                                        </div>
                                    </div>

                                    <div class="col-12 text-end">
                                        <button type="button" class="btn btn-outline-secondary me-2"
                                            onclick="showSection('mis-reservas')">
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
                                        <input type="text" class="form-control" id="profileNombre" name="nombre"
                                            required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="profileEmail" name="email"
                                            required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="profilePhone" name="telefono"
                                            required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" id="profileDob" name="fecha_nac">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Dirección</label>
                                        <input type="text" class="form-control" id="profileAddress" name="direccion">
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
    <script src="public/scripts/client_logic.js"></script>
</body>

</html>