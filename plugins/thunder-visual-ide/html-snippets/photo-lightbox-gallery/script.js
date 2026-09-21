const dialog = root.querySelector('[data-photo-dialog]');
const dialogImage = root.querySelector('[data-photo-dialog-image]');
const dialogCaption = root.querySelector('[data-photo-dialog-caption]');
root.querySelectorAll('[data-photo-item]').forEach(function (item) {
    item.addEventListener('click', function () {
        if (dialogImage) {
            dialogImage.src = item.dataset.src || '';
            dialogImage.alt = item.dataset.caption || '';
        }
        if (dialogCaption) dialogCaption.textContent = item.dataset.caption || '';
        dialog?.showModal();
    });
});
root.querySelector('[data-photo-close]')?.addEventListener('click', function () {
    dialog?.close();
});
dialog?.addEventListener('click', function (event) {
    if (event.target === dialog) dialog.close();
});
