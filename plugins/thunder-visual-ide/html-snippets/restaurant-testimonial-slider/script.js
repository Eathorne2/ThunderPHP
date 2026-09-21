const reviewSlides = Array.from(root.querySelectorAll('[data-rts-slide]'));
const reviewCurrent = root.querySelector('[data-rts-current]');
let reviewIndex = 0;

function showReview(index) {
    reviewIndex = (index + reviewSlides.length) % reviewSlides.length;

    reviewSlides.forEach(function (slide, slideIndex) {
        slide.hidden = slideIndex !== reviewIndex;
    });

    reviewCurrent.textContent = String(reviewIndex + 1).padStart(2, '0');
}

root.querySelector('[data-rts-prev]')?.addEventListener('click', function () {
    showReview(reviewIndex - 1);
});

root.querySelector('[data-rts-next]')?.addEventListener('click', function () {
    showReview(reviewIndex + 1);
});
