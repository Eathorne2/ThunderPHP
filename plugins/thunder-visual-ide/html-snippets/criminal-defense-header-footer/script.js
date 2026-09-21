root.querySelector('[data-blcdf-toggle]')?.addEventListener('click', function () {
    root.querySelector('[data-blcdf-nav]')?.classList.toggle('is-open');
});
root.querySelector('[data-blcdf-form]')?.addEventListener('submit', function (event) {
    event.preventDefault();
    const message = root.querySelector('[data-blcdf-message]');
    if (message) message.textContent = 'Thank you. Your subscription has been received.';
});
