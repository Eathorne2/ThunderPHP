root.querySelector('[data-blhfl-toggle]')?.addEventListener('click', function () {
    root.querySelector('[data-blhfl-nav]')?.classList.toggle('is-open');
});
root.querySelector('[data-blhfl-form]')?.addEventListener('submit', function (event) {
    event.preventDefault();
    const message = root.querySelector('[data-blhfl-message]');
    if (message) message.textContent = 'Thank you. Your subscription has been received.';
});
