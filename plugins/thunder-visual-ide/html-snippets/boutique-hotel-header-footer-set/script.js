const menuButton = root.querySelector('[data-hotel-menu-button]');
const navigation = root.querySelector('[data-hotel-navigation]');

function closeNavigation() {
    navigation?.classList.remove('is-open');
    menuButton?.setAttribute('aria-expanded', 'false');
}

menuButton?.addEventListener('click', function () {
    const isOpen = navigation?.classList.toggle('is-open') ?? false;
    menuButton.setAttribute('aria-expanded', String(isOpen));
});

navigation?.addEventListener('click', function (event) {
    if (event.target.closest('a')) {
        closeNavigation();
    }
});

root.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeNavigation();
    }
});
