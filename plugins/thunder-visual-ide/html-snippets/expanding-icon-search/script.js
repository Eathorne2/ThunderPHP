const form = root.querySelector('[data-eis-search]');
const input = form?.querySelector('input');
root.querySelector('[data-eis-toggle]')?.addEventListener('click', () => {
    form.classList.toggle('is-open');
    if (form.classList.contains('is-open')) {
        input.tabIndex = 0;
        input.focus();
    } else input.tabIndex = - 1;
});
form?.addEventListener('submit', event => event.preventDefault());
