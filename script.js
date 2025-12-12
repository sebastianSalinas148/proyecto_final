

let slideIndex = 0;
let autoSlideTimer;

function nextSlide() {
    slideIndex++;
    showSlide(slideIndex);
    resetAutoSlide();
}

function prevSlide() {
    slideIndex--;
    showSlide(slideIndex);
    resetAutoSlide();
}

function currentSlide(n) {
    slideIndex = n;
    showSlide(slideIndex);
    resetAutoSlide();
}

function showSlide(n) {
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.dot');
    
    if (!slides.length) return;

   
    if (n >= slides.length) {
        slideIndex = 0;
    }
    if (n < 0) {
        slideIndex = slides.length - 1;
    }

    
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));

   
    if (slides[slideIndex]) {
        slides[slideIndex].classList.add('active');
    }
    if (dots[slideIndex]) {
        dots[slideIndex].classList.add('active');
    }
}

function autoSlide() {
    slideIndex++;
    showSlide(slideIndex);
}

function resetAutoSlide() {
    clearInterval(autoSlideTimer);
    startAutoSlide();
}

function startAutoSlide() {
    autoSlideTimer = setInterval(autoSlide, 5000); 
}

// Inicializar el carrusel cuando carga la página
document.addEventListener('DOMContentLoaded', function() {
    showSlide(slideIndex);
    startAutoSlide();
});



function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (!sidebar) return;
    sidebar.classList.toggle('open');
    if (backdrop) backdrop.classList.toggle('visible');
}


document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const sidebar = document.querySelector('.sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (sidebar && sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
            if (backdrop) backdrop.classList.remove('visible');
        }
    }
});
