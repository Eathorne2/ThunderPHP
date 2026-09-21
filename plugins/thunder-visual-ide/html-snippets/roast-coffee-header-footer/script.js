const menuButton = root.querySelector('[data-rchf-toggle]');
const navigation = root.querySelector('[data-rchf-nav]');
const newsletter = root.querySelector('[data-rchf-form]');
const message = root.querySelector('[data-rchf-message]');

menuButton?.addEventListener('click', function () {
    const isOpen = navigation.classList.toggle('is-open');
    menuButton.setAttribute('aria-expanded', String(isOpen));
});

newsletter?.addEventListener('submit', function (event) {
    event.preventDefault();
    message.textContent = 'Subscribed. Your next coffee note is coming soon.';
    newsletter.reset();
});
