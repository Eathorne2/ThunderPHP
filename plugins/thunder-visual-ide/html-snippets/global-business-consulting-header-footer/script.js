root.querySelector('[data-blgbf-toggle]')?.addEventListener('click', function () {
    root.querySelector('[data-blgbf-nav]')?.classList.toggle('is-open');
});
root.querySelector('[data-blgbf-form]')?.addEventListener('submit', function (event) {
    event.preventDefault();
    const message = root.querySelector('[data-blgbf-message]');
    if (message) message.textContent = 'Thank you. Your subscription has been received.';
});
