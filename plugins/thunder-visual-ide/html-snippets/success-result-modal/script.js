const modal = root.querySelector('[data-srm-modal]');
const setOpen = open => {
    modal.hidden = !open;
    if (open) modal.querySelector('[data-srm-close]')?.focus();
};
root.querySelector('[data-srm-open]')?.addEventListener('click', () => setOpen(true));
root.querySelector('[data-srm-close]')?.addEventListener('click', () => setOpen(false));
modal?.addEventListener('click', event => {
    if (event.target === modal) setOpen(false);
});
