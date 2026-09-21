const dialog = root.querySelector('[data-dialog]');
root.querySelector('[data-expand]')?.addEventListener('click', () => dialog.showModal());
root.querySelector('[data-close]')?.addEventListener('click', () => dialog.close());
const finishButtons = [...root.querySelectorAll('[data-finish]')];
const finishLabel = root.querySelector('[data-finish-label]');
finishButtons.forEach(button => button.addEventListener('click', () => {
    finishButtons.forEach(item => item.classList.remove('is-active'));
    button.classList.add('is-active');
    finishLabel.textContent = button.dataset.finish;
}));
