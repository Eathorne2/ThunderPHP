const menuButton = root.querySelector('[data-ffhf-toggle]');
const navigation = root.querySelector('[data-ffhf-nav]');
const newsletter = root.querySelector('[data-ffhf-form]');
const message = root.querySelector('[data-ffhf-message]');

menuButton?.addEventListener('click', function () {
    const isOpen = navigation.classList.toggle('is-open');
    menuButton.setAttribute('aria-expanded', String(isOpen));
});

newsletter?.addEventListener('submit', function (event) {
    event.preventDefault();
    message.textContent = 'Your first field note is on its way.';
    newsletter.reset();
});
