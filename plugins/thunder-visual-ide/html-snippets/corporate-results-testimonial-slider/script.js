const resultSlides = Array.from(root.querySelectorAll('[data-crts-slide]'));
const resultDots = root.querySelector('[data-crts-dots]');
let resultIndex = 0;

resultSlides.forEach(function (_, index) {
    const dot = document.createElement('i');
    dot.classList.toggle('is-active', index === 0);
    resultDots?.appendChild(dot);
});

function showResultSlide(nextIndex) {
    resultIndex = (nextIndex + resultSlides.length) % resultSlides.length;

    resultSlides.forEach(function (slide, index) {
        slide.hidden = index !== resultIndex;
    });

    Array.from(resultDots?.children || []).forEach(function (dot, index) {
        dot.classList.toggle('is-active', index === resultIndex);
    });
}

root.querySelector('[data-crts-prev]')?.addEventListener('click', function () {
    showResultSlide(resultIndex - 1);
});

root.querySelector('[data-crts-next]')?.addEventListener('click', function () {
    showResultSlide(resultIndex + 1);
});
