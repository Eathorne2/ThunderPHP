const menuButton = root.querySelector('[data-tchf-toggle]');
const navigation = root.querySelector('[data-tchf-nav]');

menuButton?.addEventListener('click', function () {
    const isOpen = navigation.classList.toggle('is-open');
    menuButton.setAttribute('aria-expanded', String(isOpen));
});
