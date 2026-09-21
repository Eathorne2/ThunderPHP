const modal = root.querySelector('[data-cam-modal]');
const open = () => {
    modal.hidden = false;
    modal.querySelector('[data-cam-close]')?.focus();
};
const close = () => {
    modal.hidden = true;
    root.querySelector('[data-cam-open]')?.focus();
};
root.querySelector('[data-cam-open]')?.addEventListener('click', open);
root.querySelectorAll('[data-cam-close],[data-cam-confirm]').forEach(button =>
    button.addEventListener('click', close));
modal?.addEventListener('click', event => {
    if (event.target === modal) close();
});
