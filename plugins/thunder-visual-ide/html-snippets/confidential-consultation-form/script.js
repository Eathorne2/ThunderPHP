root.querySelector('[data-blcf-form]')?.addEventListener('submit', function (event) {
    event.preventDefault();
    const message = root.querySelector('[data-blcf-message]');
    if (message) message.textContent = 'Thank you. Your confidential enquiry has been received.';
});
