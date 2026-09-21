const menuButton = root.querySelector('[data-ephf-toggle]');
const navigation = root.querySelector('[data-ephf-nav]');

menuButton?.addEventListener('click', function () {
    const isOpen = navigation.classList.toggle('is-open');
    menuButton.setAttribute('aria-expanded', String(isOpen));
});
