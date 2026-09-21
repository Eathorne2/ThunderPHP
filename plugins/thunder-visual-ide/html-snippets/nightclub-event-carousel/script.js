const slides = Array.from(root.querySelectorAll('[data-slide]'));
const counter = root.querySelector('[data-current]');
let current = 0;

const showSlide = index => {
    current = (index + slides.length) % slides.length;

    slides.forEach((slide, slideIndex) => {
        slide.classList.toggle('is-active', slideIndex === current);
    });

    if (counter) {
        counter.textContent = String(current + 1).padStart(2, '0');
    }
};

root.querySelector('[data-previous]')?.addEventListener('click', () => {
    showSlide(current - 1);
});

root.querySelector('[data-next]')?.addEventListener('click', () => {
    showSlide(current + 1);
});
