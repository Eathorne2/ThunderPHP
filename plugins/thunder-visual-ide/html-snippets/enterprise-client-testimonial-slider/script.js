const enterpriseSlides = Array.from(root.querySelectorAll('[data-ects-slide]'));
const enterpriseCurrent = root.querySelector('[data-ects-current]');
let enterpriseIndex = 0;

function showEnterpriseSlide(nextIndex) {
    enterpriseIndex = (nextIndex + enterpriseSlides.length) % enterpriseSlides.length;

    enterpriseSlides.forEach(function (slide, index) {
        slide.hidden = index !== enterpriseIndex;
    });

    if (enterpriseCurrent) {
        enterpriseCurrent.textContent = String(enterpriseIndex + 1).padStart(2, '0');
    }
}

root.querySelector('[data-ects-prev]')?.addEventListener('click', function () {
    showEnterpriseSlide(enterpriseIndex - 1);
});

root.querySelector('[data-ects-next]')?.addEventListener('click', function () {
    showEnterpriseSlide(enterpriseIndex + 1);
});
