const slides = [...root.querySelectorAll('[data-slide]')];
const current = root.querySelector('[data-current]');
const total = root.querySelector('[data-total]');
let active = 0;

total.textContent = String(slides.length).padStart(2, '0');

function showSlide(index) {
    active = (index + slides.length) % slides.length;
    slides.forEach((slide, position) => {
        slide.hidden = position !== active;
    });
    current.textContent = String(active + 1).padStart(2, '0');
}

root.querySelector('[data-prev]')?.addEventListener('click', () => showSlide(active - 1));
root.querySelector('[data-next]')?.addEventListener('click', () => showSlide(active + 1));
