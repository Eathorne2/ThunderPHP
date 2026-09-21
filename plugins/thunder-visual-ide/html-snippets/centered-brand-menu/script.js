const toggle = root.querySelector('[data-toggle]');
const menu = root.querySelector('[data-menu]');

toggle?.addEventListener('click', () => {
    if (menu) menu.classList.toggle('is-open');
    else if (root.classList.contains('cbm-men') && root.querySelector('.cbm-men__account')) root.classList.toggle('is-collapsed');
    else root.classList.toggle('is-open');
});
