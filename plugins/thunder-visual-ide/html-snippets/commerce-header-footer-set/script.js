const menuButton = root.querySelector('[data-commerce-menu-button]');
const navigation = root.querySelector('[data-commerce-navigation]');

function setNavigationState(isOpen) {
    if (!menuButton || !navigation) {
        return;
    }

    navigation.classList.toggle('is-open', isOpen);
    menuButton.setAttribute('aria-expanded', String(isOpen));
}

menuButton?.addEventListener('click', function () {
    const isOpen = !navigation?.classList.contains('is-open');
    setNavigationState(isOpen);
});

navigation?.addEventListener('click', function (event) {
    if (event.target.closest('a')) {
        setNavigationState(false);
    }
});
