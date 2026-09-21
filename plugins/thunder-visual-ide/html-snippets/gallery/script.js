const dialog = root.querySelector('[data-gallery-dialog]');
const dialogArt = root.querySelector('[data-gallery-dialog-art]');
const dialogLabel = root.querySelector('[data-gallery-dialog-label]');

root.querySelectorAll('[data-gallery-item]').forEach(item => {
    item.addEventListener('click', () => {
        const art = item.querySelector('.mosaic-gallery__art');

        if (dialogArt && art) {
            const modifierClasses = Array.from(art.classList)
                .filter(name => name.startsWith('mosaic-gallery__art--'))
                .join(' ');

            dialogArt.className = `mosaic-gallery__dialog-art ${modifierClasses}`;
        }

        if (dialogLabel) {
            dialogLabel.textContent = item.dataset.label || '';
        }

        dialog?.showModal();
    });
});

root.querySelector('[data-gallery-close]')?.addEventListener('click', () => {
    dialog?.close();
});

dialog?.addEventListener('click', event => {
    if (event.target === dialog) {
        dialog.close();
    }
});
