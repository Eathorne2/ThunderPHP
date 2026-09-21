const menuButton = root.querySelector('[data-mfhf-toggle]');
const navigation = root.querySelector('[data-mfhf-nav]');

menuButton?.addEventListener('click', function () {
    const isOpen = navigation.classList.toggle('is-open');
    menuButton.setAttribute('aria-expanded', String(isOpen));
});
