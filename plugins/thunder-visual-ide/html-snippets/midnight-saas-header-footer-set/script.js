const menuButton = root.querySelector('[data-midnight-menu-button]');
const navigation = root.querySelector('[data-midnight-navigation]');

menuButton?.addEventListener('click', function () {
    const isOpen = navigation?.classList.toggle('is-open') ?? false;
    menuButton.setAttribute('aria-expanded', String(isOpen));
});

navigation?.addEventListener('click', function (event) {
    if (!event.target.closest('a')) {
        return;
    }

    navigation.classList.remove('is-open');
    menuButton?.setAttribute('aria-expanded', 'false');
});
