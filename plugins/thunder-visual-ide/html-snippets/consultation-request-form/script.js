root.querySelector('[data-crf-form]')?.addEventListener('submit', function (event) {
    event.preventDefault();
    const message = root.querySelector('[data-crf-message]');

    if (message) {
        message.textContent = 'Thank you. A member of our team will contact you shortly.';
    }
});
