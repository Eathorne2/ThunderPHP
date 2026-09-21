const dialog = root.querySelector('[data-lightbox]');
const image = root.querySelector('[data-lightbox-image]');

root.querySelectorAll('[data-image]').forEach(button => {
    button.addEventListener('click', () => {
        image.src = button.dataset.image;
        dialog.showModal();
    });
});

root.querySelector('[data-close]')?.addEventListener('click', () => dialog.close());

dialog?.addEventListener('click', event => {
    if (event.target === dialog) {
        dialog.close();
    }
});
