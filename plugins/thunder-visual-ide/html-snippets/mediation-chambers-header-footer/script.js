root.querySelector('[data-blmcf2-toggle]')?.addEventListener('click', function () {
    root.querySelector('[data-blmcf2-nav]')?.classList.toggle('is-open');
});
root.querySelector('[data-blmcf2-form]')?.addEventListener('submit', function (event) {
    event.preventDefault();
    const message = root.querySelector('[data-blmcf2-message]');
    if (message) message.textContent = 'Thank you. Your subscription has been received.';
});
