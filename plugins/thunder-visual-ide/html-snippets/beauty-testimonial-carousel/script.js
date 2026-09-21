const slides = [...root.querySelectorAll('.btc-slider__slides article')];
const current = root.querySelector('[data-current]');
let index = 0;
function show(next) {
    index = (next + slides.length) % slides.length;
    slides.forEach((slide, i) => slide.hidden = i !== index);
    current.textContent = String(index + 1).padStart(2, '0');
}
root.querySelector('[data-prev]')?.addEventListener('click', () => show(index - 1));
root.querySelector('[data-next]')?.addEventListener('click', () => show(index + 1));
