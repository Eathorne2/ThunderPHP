const slides = [...root.querySelectorAll('[data-slide]')];
const count = root.querySelector('[data-count]');
let active = 0;

function show(index) {
    active = (index + slides.length) % slides.length;
    slides.forEach((slide, slideIndex) => slide.classList.toggle('is-active', slideIndex === active));
    count.textContent = `${active + 1} / ${slides.length}`;
}

root.querySelector('[data-prev]')?.addEventListener('click', () => show(active - 1));
root.querySelector('[data-next]')?.addEventListener('click', () => show(active + 1));
show(0);
