root.querySelector('[data-blplf-toggle]')?.addEventListener('click', function () {
    root.querySelector('[data-blplf-nav]')?.classList.toggle('is-open');
});
root.querySelector('[data-blplf-form]')?.addEventListener('submit', function (event) {
    event.preventDefault();
    const message = root.querySelector('[data-blplf-message]');
    if (message) message.textContent = 'Thank you. Your subscription has been received.';
});
