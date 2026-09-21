const slider = root.querySelector('[data-testimonial-slider]');
const slides = [...root.querySelectorAll('[data-testimonial-slide]')];
const previousButton = root.querySelector('[data-testimonial-previous]');
const nextButton = root.querySelector('[data-testimonial-next]');
const counter = root.querySelector('[data-testimonial-counter]');
let currentIndex = 0;

function showSlide(index) {
    currentIndex = (index + slides.length) % slides.length;

    slides.forEach((slide, slideIndex) => {
        slide.hidden = slideIndex !== currentIndex;
    });

    if (counter) {
        counter.textContent = `${currentIndex + 1} / ${slides.length}`;
    }
}

previousButton?.addEventListener('click', () => {
    showSlide(currentIndex - 1);
});

nextButton?.addEventListener('click', () => {
    showSlide(currentIndex + 1);
});

slider?.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowLeft') {
        showSlide(currentIndex - 1);
    }

    if (event.key === 'ArrowRight') {
        showSlide(currentIndex + 1);
    }
});

showSlide(0);
