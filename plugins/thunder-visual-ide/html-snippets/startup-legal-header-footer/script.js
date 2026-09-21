root.querySelector('[data-blslf-toggle]')?.addEventListener('click', function () {
    root.querySelector('[data-blslf-nav]')?.classList.toggle('is-open');
});
root.querySelector('[data-blslf-form]')?.addEventListener('submit', function (event) {
    event.preventDefault();
    const message = root.querySelector('[data-blslf-message]');
    if (message) message.textContent = 'Thank you. Your subscription has been received.';
});
