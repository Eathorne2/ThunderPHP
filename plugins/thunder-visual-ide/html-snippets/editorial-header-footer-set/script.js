const menuButton = root.querySelector('[data-editorial-menu]');
const navigation = root.querySelector('.editorial-shell__nav');

menuButton?.addEventListener('click', () => {
    const isOpen = menuButton.getAttribute('aria-expanded') === 'true';

    menuButton.setAttribute('aria-expanded', String(!isOpen));
    navigation?.classList.toggle('is-open', !isOpen);
});
