// Variables globales
const loginOverlay = document.getElementById('loginOverlay');
const btnLogin = document.getElementById('btnLogin');
const btnCloseLogin = document.getElementById('btnCloseLogin');
const galleryOverlay = document.getElementById('galleryOverlay');
const btnCloseGallery = document.getElementById('btnCloseGallery');
const pinterestItems = document.querySelectorAll('.pinterest-item');

// Función para abrir modal de login
function openLoginModal() {
    loginOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Función para cerrar modal de login
function closeLoginModal() {
    loginOverlay.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Función para abrir galería
function openGallery(item) {
    const title = item.getAttribute('data-title');
    const icon = item.querySelector('.gallery-icon').className;
    
    document.querySelector('.gallery-modal-icon').className = 'gallery-modal-icon ' + icon;
    document.querySelector('.gallery-modal-title').textContent = title;
    
    galleryOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Función para cerrar galería
function closeGallery() {
    galleryOverlay.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Event Listeners para Login Modal
btnLogin.addEventListener('click', openLoginModal);
btnCloseLogin.addEventListener('click', closeLoginModal);

// Cerrar modal al hacer clic fuera de él
loginOverlay.addEventListener('click', (e) => {
    if (e.target === loginOverlay) {
        closeLoginModal();
    }
});

// Event Listeners para Galería
pinterestItems.forEach(item => {
    item.addEventListener('click', () => {
        openGallery(item);
    });
});

btnCloseGallery.addEventListener('click', closeGallery);

// Cerrar galería al hacer clic fuera de la imagen
galleryOverlay.addEventListener('click', (e) => {
    if (e.target === galleryOverlay) {
        closeGallery();
    }
});

// Cerrar modales con tecla ESC
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        if (loginOverlay.classList.contains('active')) {
            closeLoginModal();
        }
        if (galleryOverlay.classList.contains('active')) {
            closeGallery();
        }
    }
});

// Prevenir envío de formularios (solo frontend)
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Gracias por tu mensaje. En un entorno real, esto se enviaría al servidor.');
        form.reset();
        if (loginOverlay.classList.contains('active')) {
            closeLoginModal();
        }
    });
});

// Navbar transparente/sólido al hacer scroll
const navbar = document.querySelector('.navbar');
let lastScroll = 0;

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 100) {
        navbar.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.2)';
    } else {
        navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
    }
    
    lastScroll = currentScroll;
});

// Smooth scroll para enlaces del navbar
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        
        if (target) {
            const navbarHeight = navbar.offsetHeight;
            const targetPosition = target.offsetTop - navbarHeight;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
            
            // Cerrar menú móvil si está abierto
            const navbarCollapse = document.querySelector('.navbar-collapse');
            if (navbarCollapse.classList.contains('show')) {
                navbarCollapse.classList.remove('show');
            }
        }
    });
});

// Animación de aparición para elementos al hacer scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Aplicar animación a cards
document.querySelectorAll('.service-card, .product-card, .pinterest-item').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'all 0.6s ease';
    observer.observe(el);
});

// Auto-play de carruseles más lento
const carousels = document.querySelectorAll('.carousel');
carousels.forEach(carousel => {
    const bsCarousel = new bootstrap.Carousel(carousel, {
        interval: 5000,
        wrap: true
    });
});

// Precargar imágenes de iconos (optimización)
window.addEventListener('load', () => {
    console.log('Aruma Spa - Página cargada correctamente');
});

// Añadir efecto parallax al hero
window.addEventListener('scroll', () => {
    const hero = document.querySelector('.hero');
    if (hero) {
        const scrolled = window.pageYOffset;
        hero.style.transform = `translateY(${scrolled * 0.5}px)`;
    }
});

// Animación de números (si se añaden estadísticas)
function animateNumber(element, target, duration) {
    let start = 0;
    const increment = target / (duration / 16);
    
    const timer = setInterval(() => {
        start += increment;
        if (start >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(start);
        }
    }, 16);
}

// Hover effect mejorado para tarjetas
document.querySelectorAll('.service-card, .product-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.zIndex = '10';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.zIndex = '1';
    });
});

// Detectar si el usuario está en móvil para ajustar interacciones
const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

if (isMobile) {
    // Ajustar comportamiento táctil
    pinterestItems.forEach(item => {
        item.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.95)';
        });
        
        item.addEventListener('touchend', function() {
            setTimeout(() => {
                this.style.transform = '';
            }, 100);
        });
    });
}

// Prevenir zoom en doble tap en iOS
let lastTouchEnd = 0;
document.addEventListener('touchend', (e) => {
    const now = Date.now();
    if (now - lastTouchEnd <= 300) {
        e.preventDefault();
    }
    lastTouchEnd = now;
}, false);

console.log('Aruma Spa - Scripts cargados correctamente ✨');
