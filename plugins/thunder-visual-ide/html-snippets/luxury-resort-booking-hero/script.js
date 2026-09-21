const bookingForm = root.querySelector('.lrbh__booking');

bookingForm?.addEventListener('submit', event => {
    event.preventDefault();
    const button = bookingForm.querySelector('button');

    if (button) {
        button.textContent = 'Availability requested';
    }
});
