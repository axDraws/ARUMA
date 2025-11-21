console.log("Script cargado correctamente");

// ===============================
// VARIABLES GLOBALES
// ===============================
const loginOverlay = document.getElementById('loginOverlay');
const registerOverlay = document.getElementById('registerOverlay');
const btnLogin = document.getElementById('btnLogin');
const btnCloseLogin = document.getElementById('btnCloseLogin');
const btnCloseRegister = document.getElementById('btnCloseRegister');

const galleryOverlay = document.getElementById('galleryOverlay');
const btnCloseGallery = document.getElementById('btnCloseGallery');
const pinterestItems = document.querySelectorAll('.pinterest-item');


// ===============================
// MODALES LOGIN / REGISTRO
// ===============================
function openLoginModal() {
    loginOverlay.classList.add('active');
    registerOverlay?.classList.remove('active');
    document.body.style.overflow = 'hidden';
}

function closeLoginModal() {
    loginOverlay.classList.remove('active');
    document.body.style.overflow = 'auto';
}

function openRegisterModal() {
    registerOverlay.classList.add('active');
    loginOverlay?.classList.remove('active');
    document.body.style.overflow = 'hidden';
}

function closeRegisterModal() {
    registerOverlay.classList.remove('active');
    document.body.style.overflow = 'auto';
}


// ===============================
// GALERÍA
// ===============================
function openGallery(item) {
    const imgSrc = item.querySelector('img').src;

    document.getElementById('galleryModalImg').src = imgSrc;

    galleryOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeGallery() {
    galleryOverlay.classList.remove('active');
    document.body.style.overflow = 'auto';
}


// ===============================
// EVENTOS DE MODALES
// ===============================
btnLogin?.addEventListener('click', openLoginModal);
btnCloseLogin?.addEventListener('click', closeLoginModal);
btnCloseRegister?.addEventListener('click', closeRegisterModal);

document.querySelectorAll('.link-register').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        openRegisterModal();
    });
});

document.querySelectorAll('.link-login').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        openLoginModal();
    });
});

loginOverlay?.addEventListener('click', e => {
    if (e.target === loginOverlay) closeLoginModal();
});

registerOverlay?.addEventListener('click', e => {
    if (e.target === registerOverlay) closeRegisterModal();
});

galleryOverlay?.addEventListener('click', e => {
    if (e.target === galleryOverlay) closeGallery();
});

pinterestItems.forEach(item => {
    item.addEventListener('click', () => openGallery(item));
});

btnCloseGallery?.addEventListener('click', closeGallery);


// ===============================
// LOGIN / REGISTRO – CORRECCIÓN
// ===============================
// ⚠ IMPORTANTE ⚠
// Ya NO bloqueamos el submit del login/registro
// Solo bloqueamos formularios que NO tengan action hacia el backend.
document.querySelectorAll('form').forEach(form => {
    const action = form.getAttribute("action") || "";

    // Permitir envío al backend MVC
    if (action.includes("/login") || action.includes("/register")) {
        console.log("Enviando formulario al backend:", action);
        return;
    }

    // Formularios que son de demostración
    form.addEventListener('submit', e => {
        e.preventDefault();
        alert('Gracias por tu mensaje. (Formulario local)');
        form.reset();
        closeLoginModal();
        closeRegisterModal();
    });
});


// ===============================
// NAVBAR SOMBRA
// ===============================
const navbar = document.querySelector('.navbar');
window.addEventListener('scroll', () => {
    navbar.style.boxShadow =
        window.pageYOffset > 100
            ? '0 5px 20px rgba(0,0,0,0.2)'
            : '0 2px 10px rgba(0,0,0,0.1)';
});


// ===============================
// SMOOTH SCROLL
// ===============================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (!target) return;

        const navbarHeight = navbar.offsetHeight;

        window.scrollTo({
            top: target.offsetTop - navbarHeight,
            behavior: 'smooth'
        });

        document.querySelector('.navbar-collapse')?.classList.remove('show');
    });
});


// ===============================
// ANIMACIONES AL SCROLL
// ===============================
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

document.querySelectorAll('.service-card, .product-card, .pinterest-item').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'all 0.6s ease';
    observer.observe(el);
});


// ===============================
// CARRUSELES
// ===============================
document.querySelectorAll('.carousel').forEach(carousel => {
    new bootstrap.Carousel(carousel, { interval: 5000, wrap: true });
});


// ===============================
// PARALLAX
// ===============================
window.addEventListener('scroll', () => {
    const hero = document.querySelector('.hero');
    if (hero) hero.style.transform = `translateY(${window.pageYOffset * 0.5}px)`;
});


// ===============================
// HOVER Z-INDEX
// ===============================
document.querySelectorAll('.service-card, .product-card').forEach(card => {
    card.addEventListener('mouseenter', () => card.style.zIndex = '10');
    card.addEventListener('mouseleave', () => card.style.zIndex = '1');
});


// ===============================
// MOBILE FIXES
// ===============================
const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

if (isMobile) {
    pinterestItems.forEach(item => {
        item.addEventListener('touchstart', () => item.style.transform = 'scale(0.95)');
        item.addEventListener('touchend', () => setTimeout(() => item.style.transform = '', 100));
    });
}


// ===============================
// PREVENIR DOBLE TAP ZOOM (iOS)
// ===============================
let lastTouchEnd = 0;
document.addEventListener('touchend', e => {
    const now = Date.now();
    if (now - lastTouchEnd <= 300) e.preventDefault();
    lastTouchEnd = now;
}, false);


console.log('Aruma Spa - Scripts cargados correctamente ✨');
