const slides = Array.from(root.querySelectorAll('[data-slide]'));
const dotsHost = root.querySelector('[data-slider-dots]');
let index = 0;
function show(nextIndex) {
    index = (nextIndex + slides.length) % slides.length;
    slides.forEach(function (slide, slideIndex) {
        const active = slideIndex === index;
        slide.classList.toggle('is-active', active);
        slide.hidden = !active;
    });
    dotsHost?.querySelectorAll('button').forEach(function (dot, dotIndex) {
        dot.classList.toggle('is-active', dotIndex === index);
    });
}
slides.forEach(function (_, slideIndex) {
    const dot = document.createElement('button');
    dot.type = 'button';
    dot.className = 'quote-slider__dot';
    dot.setAttribute('aria-label', 'Show testimonial ' + (slideIndex + 1));
    dot.addEventListener('click', function () {
        show(slideIndex);
    });
    dotsHost?.appendChild(dot);
});
root.querySelector('[data-slider-prev]')?.addEventListener('click', function () {
    show(index - 1);
});
root.querySelector('[data-slider-next]')?.addEventListener('click', function () {
    show(index + 1);
});
show(0);
