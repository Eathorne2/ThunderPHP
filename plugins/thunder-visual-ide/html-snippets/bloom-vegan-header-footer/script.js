const menuButton = root.querySelector('[data-bvhf-toggle]');
const navigation = root.querySelector('[data-bvhf-nav]');
const newsletter = root.querySelector('[data-bvhf-form]');
const message = root.querySelector('[data-bvhf-message]');

menuButton?.addEventListener('click', function () {
    const isOpen = navigation.classList.toggle('is-open');
    menuButton.setAttribute('aria-expanded', String(isOpen));
});

newsletter?.addEventListener('submit', function (event) {
    event.preventDefault();
    message.textContent = 'You are officially part of Bloom.';
    newsletter.reset();
});
