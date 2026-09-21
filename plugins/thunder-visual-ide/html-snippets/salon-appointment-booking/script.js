const form = root.querySelector('form');
form?.addEventListener('submit', (event) => {
    event.preventDefault();
    root.querySelector('[data-sab-booking-message]').textContent = 'Your appointment request is ready to send.';
});
