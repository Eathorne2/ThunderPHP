const form = root.querySelector('[data-print-club-form]');
const message = root.querySelector('[data-print-club-message]');

form?.addEventListener('submit', (event) => {
    event.preventDefault();

    if (message) {
        message.textContent = 'Your invitation request has been recorded.';
        message.classList.add('is-success');
    }

    form.reset();
});
