const toggle = root.querySelector('[data-pill-toggle]');
const links = root.querySelector('[data-pill-links]');

toggle?.addEventListener('click', () => {
    const open = links.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', String(open));
});

root.querySelectorAll('.pill-menu__links a').forEach(link => {
    link.addEventListener('click', () => {
        root.querySelectorAll('.pill-menu__links a').forEach(item => {
            item.classList.toggle('is-active', item === link);
        });
    });
});
