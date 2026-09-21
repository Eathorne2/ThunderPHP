const menuButton = root.querySelector('[data-ghhf-toggle]');
const navigation = root.querySelector('[data-ghhf-nav]');
const newsletter = root.querySelector('[data-ghhf-form]');
const message = root.querySelector('[data-ghhf-message]');

menuButton?.addEventListener('click', function () {
    const isOpen = navigation.classList.toggle('is-open');
    menuButton.setAttribute('aria-expanded', String(isOpen));
});

newsletter?.addEventListener('submit', function (event) {
    event.preventDefault();
    message.textContent = 'Welcome to the Saturday loaf letter.';
    newsletter.reset();
});
