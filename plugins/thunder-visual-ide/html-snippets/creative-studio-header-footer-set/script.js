const menuButton = root.querySelector('[data-studio-menu-button]');
const navigation = root.querySelector('[data-studio-navigation]');

menuButton?.addEventListener('click', function () {
    const isOpen = navigation?.classList.toggle('is-open') ?? false;
    menuButton.setAttribute('aria-expanded', String(isOpen));
    menuButton.textContent = isOpen ? 'Close' : 'Menu';
});

navigation?.addEventListener('click', function (event) {
    if (!event.target.closest('a') || !menuButton) {
        return;
    }

    navigation.classList.remove('is-open');
    menuButton.setAttribute('aria-expanded', 'false');
    menuButton.textContent = 'Menu';
});
