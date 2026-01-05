<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Misión y Visión - Aruma Spa</title>
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
                    <li class="nav-item"><a class="nav-link" href="../views/home.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="../views/home.php">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link" href="../views/home.php">Productos</a></li>
                    <li class="nav-item"><a class="nav-link active" href="mision-vision.php">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="../index.html">Galería</a></li>
                    <li class="nav-item"><a class="nav-link" href="../index.html">Contacto</a></li>
                </ul>
                <button class="btn btn-login ms-3" id="btnLogin">
                    <i class="fas fa-user"></i> Iniciar Sesión
                </button>
            </div>
        </div>
    </nav>

    <!-- Modal de Login -->
    <div class="login-overlay" id="loginOverlay">
        <div class="login-modal">
            <button class="btn-close-modal" id="btnCloseLogin">
                <i class="fas fa-times"></i>
            </button>
            <h2 class="text-center mb-4">Iniciar Sesión</h2>
            <form>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" placeholder="tu@email.com">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" placeholder="••••••••">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Recordarme</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                <div class="text-center mt-3">
                    <a href="#" class="text-muted">¿Olvidaste tu contraseña?</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero-small">
        <div class="container">
            <div class="row align-items-center justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="hero-title">Nuestra Esencia</h1>
                    <p class="hero-subtitle">Conoce quiénes somos y hacia dónde vamos</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Misión y Visión -->
    <section class="py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Misión -->
                <div class="col-lg-6">
                    <div class="mision-vision-card">
                        <div class="icon-container mb-4">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h2 class="card-title mb-4">Nuestra Misión</h2>
                        <p class="card-text">
                            En Aruma Spa nos dedicamos a proporcionar experiencias de bienestar excepcionales que
                            revitalizan el cuerpo, calman la mente y nutren el espíritu. Nuestro compromiso es ofrecer
                            servicios de la más alta calidad, utilizando productos premium y técnicas innovadoras, en un
                            ambiente de tranquilidad y armonía.
                        </p>
                        <p class="card-text">
                            Nos esforzamos por crear un refugio donde cada cliente pueda escapar del estrés diario y
                            reconectar consigo mismo, promoviendo un estilo de vida saludable y equilibrado a través de
                            tratamientos personalizados y un servicio excepcional.
                        </p>
                        <div class="values-list mt-4">
                            <div class="value-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Excelencia en el servicio</span>
                            </div>
                            <div class="value-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Atención personalizada</span>
                            </div>
                            <div class="value-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Productos de alta calidad</span>
                            </div>
                            <div class="value-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Ambiente de paz y armonía</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visión -->
                <div class="col-lg-6">
                    <div class="mision-vision-card">
                        <div class="icon-container mb-4">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h2 class="card-title mb-4">Nuestra Visión</h2>
                        <p class="card-text">
                            Ser el spa de referencia en bienestar integral, reconocido por nuestra excelencia,
                            innovación y compromiso con la salud holística de nuestros clientes. Aspiramos a expandir
                            nuestra presencia para llevar nuestros servicios de calidad a más personas que buscan
                            mejorar su calidad de vida.
                        </p>
                        <p class="card-text">
                            Visualizamos un futuro donde Aruma Spa sea sinónimo de transformación personal, donde cada
                            visita sea una experiencia memorable que inspire a nuestros clientes a mantener un
                            equilibrio permanente entre cuerpo, mente y espíritu, convirtiéndose en un estilo de vida.
                        </p>
                        <div class="goals-list mt-4">
                            <div class="goal-item">
                                <i class="fas fa-star"></i>
                                <div>
                                    <h5>Innovación Constante</h5>
                                    <p>Incorporar las últimas tendencias y tecnologías en bienestar</p>
                                </div>
                            </div>
                            <div class="goal-item">
                                <i class="fas fa-users"></i>
                                <div>
                                    <h5>Comunidad Saludable</h5>
                                    <p>Crear una comunidad comprometida con el bienestar integral</p>
                                </div>
                            </div>
                            <div class="goal-item">
                                <i class="fas fa-globe-americas"></i>
                                <div>
                                    <h5>Expansión Responsable</h5>
                                    <p>Crecer manteniendo nuestros valores y calidad de servicio</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Valores -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center mb-5">Nuestros Valores</h2>
            <div class="row g-4">
                <div class="col-md-3 col-sm-6">
                    <div class="value-card">
                        <i class="fas fa-heart"></i>
                        <h4>Pasión</h4>
                        <p>Amamos lo que hacemos y lo reflejamos en cada servicio</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="value-card">
                        <i class="fas fa-award"></i>
                        <h4>Calidad</h4>
                        <p>Compromiso con la excelencia en cada detalle</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="value-card">
                        <i class="fas fa-handshake"></i>
                        <h4>Confianza</h4>
                        <p>Construimos relaciones duraderas con nuestros clientes</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="value-card">
                        <i class="fas fa-leaf"></i>
                        <h4>Sostenibilidad</h4>
                        <p>Respeto por el medio ambiente en todos nuestros procesos</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Equipo -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center mb-5">Nuestro Equipo</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-3 col-md-6">
                    <div class="team-card">
                        <div class="team-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h4>Terapeutas Certificados</h4>
                        <p>Profesionales con amplia experiencia y certificaciones internacionales</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="team-card">
                        <div class="team-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h4>Capacitación Continua</h4>
                        <p>Actualización constante en las mejores técnicas y tratamientos</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="team-card">
                        <div class="team-icon">
                            <i class="fas fa-smile"></i>
                        </div>
                        <h4>Atención Personalizada</h4>
                        <p>Cada cliente es único y recibe un trato especial</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section py-5">
        <div class="container text-center">
            <h2 class="text-white mb-4">¿Listo para comenzar tu viaje de bienestar?</h2>
            <p class="text-white mb-4">Agenda tu primera sesión y descubre la experiencia Aruma</p>
            <a href="index.html#contacto" class="btn btn-hero">Contactar Ahora</a>
        </div>
    </section>



    <!-- Ubicación / Mapa -->
    <section class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="section-title mb-4">Ubicación</h2>
            <p class="mb-4">
                Encuéntranos en <strong>Manuel Altamirano 7-A, Ex Hacienda Santa Mónica, Tlalnepantla, Edo.
                    Méx.</strong>
            </p>

            <!-- Contenedor del mapa -->
            <div class="map-container mb-4" style="border-radius: 15px; overflow: hidden;">
                <iframe
                    src="https://www.google.com/maps?q=Manuel+Altamirano+7-A,+Ex+Hacienda+Santa+Mónica,+Tlalnepantla,+Estado+de+México&output=embed"
                    width="100%" height="400" style="border:0;" allowfullscreen loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <!-- Botón para abrir Google Maps -->
            <a href="https://www.google.com/maps/dir/?api=1&destination=Manuel+Altamirano+7-A,+Ex+Hacienda+Santa+Mónica,+Tlalnepantla,+Estado+de+México"
                target="_blank" class="btn btn-success">
                <i class="fas fa-map-marked-alt"></i> Cómo llegar
            </a>
        </div>
    </section>
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-spa"></i> ARUMA SPA</h5>
                    <p>Tu espacio de bienestar donde cuerpo y mente encuentran armonía perfecta.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="footer-links">
                        <li><a href="../views/home.php">Inicio</a></li>
                        <li><a href="../views/home.php#servicios">Servicios</a></li>
                        <li><a href="../views/home.php#productos">Productos</a></li>
                        <li><a href="../views/mision-vision.php">Nosotros</a></li>
                        <li><a href="../views/home.php#galeria">Galería</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Síguenos</h5>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center pt-3 border-top">
                <p>&copy; 2024 Aruma Spa. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="../public/scripts/bootstrap.bundle.min.js></script>
    <script src=" ../public/scripts/script.js"></script>
</body>

</html>