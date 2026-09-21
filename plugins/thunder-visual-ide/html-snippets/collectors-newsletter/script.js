const form = root.querySelector('[data-collectors-form]');
const message = root.querySelector('[data-collectors-message]');

form?.addEventListener('submit', (event) => {
    event.preventDefault();

    if (message) {
        message.textContent = 'You are on the list. Watch your inbox for the next issue.';
        message.classList.add('is-success');
    }

    form.reset();
});
