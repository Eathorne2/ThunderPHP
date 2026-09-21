const galleryFilters = Array.from(root.querySelectorAll('[data-gallery-filter]'));
const galleryItems = Array.from(root.querySelectorAll('.hgm__grid [data-kind]'));
const lightbox = root.querySelector('.hgm__lightbox');
const lightboxImage = lightbox?.querySelector('img');
const lightboxCaption = lightbox?.querySelector('p');

galleryFilters.forEach(button => {
    button.addEventListener('click', () => {
        const filter = button.dataset.galleryFilter;
        galleryFilters.forEach(item => item.classList.toggle('is-active', item === button));
        galleryItems.forEach(item => item.hidden = filter !== 'all' && item.dataset.kind !== filter);
    });
});

galleryItems.forEach(item => {
    item.addEventListener('click', () => {
        const image = item.querySelector('img');
        const caption = item.querySelector('span');
        if (lightboxImage && image) {
            lightboxImage.src = image.src;
            lightboxImage.alt = image.alt;
        }
        if (lightboxCaption && caption) lightboxCaption.textContent = caption.textContent;
        lightbox?.showModal();
    });
});

root.querySelector('[data-gallery-close]')?.addEventListener('click', () => lightbox?.close());
