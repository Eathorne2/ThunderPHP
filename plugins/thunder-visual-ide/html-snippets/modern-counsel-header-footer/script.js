root.querySelector('[data-blmcf-toggle]')?.addEventListener('click', function () {
    root.querySelector('[data-blmcf-nav]')?.classList.toggle('is-open');
});
root.querySelector('[data-blmcf-form]')?.addEventListener('submit', function (event) {
    event.preventDefault();
    const message = root.querySelector('[data-blmcf-message]');
    if (message) message.textContent = 'Thank you. Your subscription has been received.';
});
