const image = root.querySelector('[data-gallery-product-image]');
const viewButtons = Array.from(root.querySelectorAll('[data-gallery-product-src]'));

viewButtons.forEach((button) => {
    button.addEventListener('click', () => {
        if (!image) {
            return;
        }

        viewButtons.forEach((item) => item.classList.remove('is-active'));
        button.classList.add('is-active');
        image.classList.add('is-changing');

        window.setTimeout(() => {
            image.src = button.dataset.galleryProductSrc || image.src;
            image.alt = button.dataset.galleryProductAlt || image.alt;
            image.classList.remove('is-changing');
        }, 160);
    });
});
