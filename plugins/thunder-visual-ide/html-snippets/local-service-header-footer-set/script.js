const menuButton = root.querySelector('[data-service-menu-button]');
const navigation = root.querySelector('[data-service-navigation]');

menuButton?.addEventListener('click', function () {
    const isOpen = navigation?.classList.toggle('is-open') ?? false;
    menuButton.setAttribute('aria-expanded', String(isOpen));
    menuButton.textContent = isOpen ? 'Close' : 'Menu';
});

navigation?.addEventListener('click', function (event) {
    if (!event.target.closest('a')) {
        return;
    }

    navigation.classList.remove('is-open');

    if (menuButton) {
        menuButton.setAttribute('aria-expanded', 'false');
        menuButton.textContent = 'Menu';
    }
});
