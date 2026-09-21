root.querySelector('[data-blcaf-toggle]')?.addEventListener('click', function () {
    root.querySelector('[data-blcaf-nav]')?.classList.toggle('is-open');
});
root.querySelector('[data-blcaf-form]')?.addEventListener('submit', function (event) {
    event.preventDefault();
    const message = root.querySelector('[data-blcaf-message]');
    if (message) message.textContent = 'Thank you. Your subscription has been received.';
});
