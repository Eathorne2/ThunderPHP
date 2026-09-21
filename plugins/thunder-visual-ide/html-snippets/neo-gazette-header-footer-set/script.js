const menuButton = root.querySelector('[data-neo-menu]');
const navigation = root.querySelector('.neo-gazette__nav');

menuButton?.addEventListener('click', () => {
    const isOpen = menuButton.getAttribute('aria-expanded') === 'true';

    menuButton.setAttribute('aria-expanded', String(!isOpen));
    navigation?.classList.toggle('is-open', !isOpen);
});
