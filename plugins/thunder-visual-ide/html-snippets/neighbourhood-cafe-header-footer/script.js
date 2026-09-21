const menuButton = root.querySelector('[data-nchf-toggle]');
const navigation = root.querySelector('[data-nchf-nav]');

menuButton?.addEventListener('click', function () {
    const isOpen = navigation.classList.toggle('is-open');
    menuButton.setAttribute('aria-expanded', String(isOpen));
});
