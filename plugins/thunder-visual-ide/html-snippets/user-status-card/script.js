const button = root.querySelector('[data-user-menu-button]');
const menu = root.querySelector('[data-user-menu]');
button?.addEventListener('click', () => {
    const open = menu.hasAttribute('hidden');
    menu.toggleAttribute('hidden', !open);
    button.setAttribute('aria-expanded', String(open));
});
