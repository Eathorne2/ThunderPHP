const menuButton = root.querySelector('[data-tthf-toggle]');
const navigation = root.querySelector('[data-tthf-nav]');

menuButton?.addEventListener('click', function () {
    const isOpen = navigation.classList.toggle('is-open');
    menuButton.setAttribute('aria-expanded', String(isOpen));
});
