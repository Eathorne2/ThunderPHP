root.querySelector('[data-blflf-toggle]')?.addEventListener('click', function () {
    root.querySelector('[data-blflf-nav]')?.classList.toggle('is-open');
});
root.querySelector('[data-blflf-form]')?.addEventListener('submit', function (event) {
    event.preventDefault();
    const message = root.querySelector('[data-blflf-message]');
    if (message) message.textContent = 'Thank you. Your subscription has been received.';
});
