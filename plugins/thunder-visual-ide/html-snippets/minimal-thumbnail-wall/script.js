const dialog = root.querySelector('[data-dialog]');
const image = root.querySelector('[data-dialog-image]');
const title = root.querySelector('[data-dialog-title]');

root.querySelectorAll('[data-src]').forEach(button => {
    button.addEventListener('click', () => {
        image.src = button.dataset.src;
        title.textContent = button.dataset.title;
        dialog.showModal();
    });
});

root.querySelector('[data-close]')?.addEventListener('click', () => dialog.close());
