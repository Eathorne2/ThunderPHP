const slider = root.querySelector('[data-editorial-slider]');
const slides = [...root.querySelectorAll('[data-slide]')];
const dots = root.querySelector('[data-slider-dots]');
let index = 0;

function show(next) {
    index = (next + slides.length) % slides.length;

    slides.forEach((slide, i) => {
        slide.classList.toggle('is-active', i === index);
    });

    dots?.querySelectorAll('button').forEach((dot, i) => {
        dot.classList.toggle('is-active', i === index);
    });
}

slides.forEach((_, i) => {
    const dot = document.createElement('button');

    dot.type = 'button';
    dot.setAttribute('aria-label', `Show slide ${i + 1}`);
    dot.addEventListener('click', () => show(i));
    dots?.appendChild(dot);
});

root.querySelector('[data-slider-prev]')?.addEventListener('click', () => {
    show(index - 1);
});

root.querySelector('[data-slider-next]')?.addEventListener('click', () => {
    show(index + 1);
});

show(0);
