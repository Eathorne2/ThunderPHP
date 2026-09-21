const slides = Array.from(root.querySelectorAll('[data-blts-slide]'));
const count = root.querySelector('[data-blts-count]');
let active = 0;
function showSlide(next) {
    active = (next + slides.length) % slides.length;
    slides.forEach(function (slide, index) { slide.hidden = index !== active; slide.classList.toggle('is-active', index === active); });
    if (count) count.textContent = (active + 1) + ' / ' + slides.length;
}
root.querySelector('[data-blts-prev]')?.addEventListener('click', function () { showSlide(active - 1); });
root.querySelector('[data-blts-next]')?.addEventListener('click', function () { showSlide(active + 1); });
