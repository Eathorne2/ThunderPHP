const menuButton = root.querySelector('[data-culture-menu]');
const navigation = root.querySelector('.culture-shell__nav');

menuButton?.addEventListener('click', () => {
    const isOpen = menuButton.getAttribute('aria-expanded') === 'true';

    menuButton.setAttribute('aria-expanded', String(!isOpen));
    navigation?.classList.toggle('is-open', !isOpen);
});
