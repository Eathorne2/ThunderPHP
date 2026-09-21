const dialog = root.querySelector('[data-lightbox]');
const dialogImage = root.querySelector('[data-lightbox-image]');
const dialogCaption = root.querySelector('[data-lightbox-caption]');

root.querySelectorAll('[data-photo]').forEach(button => {
    button.addEventListener('click', () => {
        dialogImage.src = button.dataset.large;
        dialogCaption.textContent = button.dataset.caption;
        dialog.showModal();
    });
});

root.querySelector('[data-close]')?.addEventListener('click', () => dialog.close());
dialog?.addEventListener('click', event => {
    if (event.target === dialog) dialog.close();
});
