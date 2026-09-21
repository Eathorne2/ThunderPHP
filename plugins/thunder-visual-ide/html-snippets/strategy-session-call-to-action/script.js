root.querySelector('[data-sscta-form]')?.addEventListener('submit', function (event) {
    event.preventDefault();
    const message = root.querySelector('[data-sscta-message]');

    if (message) {
        message.textContent = 'Thank you. We will contact you to confirm a suitable appointment.';
    }
});
