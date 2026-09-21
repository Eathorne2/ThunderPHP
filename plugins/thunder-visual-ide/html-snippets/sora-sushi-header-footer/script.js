const menuButton = root.querySelector('[data-sshf-toggle]');
const navigation = root.querySelector('[data-sshf-nav]');

menuButton?.addEventListener('click', function () {
    const isOpen = navigation.classList.toggle('is-open');
    menuButton.setAttribute('aria-expanded', String(isOpen));
});
