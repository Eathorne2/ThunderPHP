root.querySelector('[data-bltaf-toggle]')?.addEventListener('click', function () {
    root.querySelector('[data-bltaf-nav]')?.classList.toggle('is-open');
});
root.querySelector('[data-bltaf-form]')?.addEventListener('submit', function (event) {
    event.preventDefault();
    const message = root.querySelector('[data-bltaf-message]');
    if (message) message.textContent = 'Thank you. Your subscription has been received.';
});
