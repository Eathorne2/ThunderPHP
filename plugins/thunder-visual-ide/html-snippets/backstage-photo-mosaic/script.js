const lightbox = root.querySelector('[data-lightbox]');
const lightboxImage = root.querySelector('[data-lightbox-image]');
const lightboxCaption = root.querySelector('[data-lightbox-caption]');

const closeLightbox = () => {
    if (lightbox) {
        lightbox.hidden = true;
    }
};

root.querySelectorAll('[data-gallery-item]').forEach(item => {
    item.addEventListener('click', () => {
        const image = item.querySelector('img');

        if (lightboxImage && image) {
            lightboxImage.src = image.src;
            lightboxImage.alt = image.alt;
        }

        if (lightboxCaption) {
            lightboxCaption.textContent = item.dataset.caption || '';
        }

        if (lightbox) {
            lightbox.hidden = false;
        }
    });
});

root.querySelector('[data-close]')?.addEventListener('click', closeLightbox);

lightbox?.addEventListener('click', event => {
    if (event.target === lightbox) {
        closeLightbox();
    }
});

root.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
        closeLightbox();
    }
});
