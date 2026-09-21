root.querySelector('[data-gchf-toggle]')?.addEventListener('click', function () {
    root.querySelector('[data-gchf-nav]')?.classList.toggle('is-open');
});

root.querySelector('.gchf-footer form')?.addEventListener('submit', function (event) {
    event.preventDefault();
    const message = root.querySelector('[data-gchf-message]');
    if (message) {
        message.textContent = 'Thank you. Your briefing request has been received.';
    }
});
