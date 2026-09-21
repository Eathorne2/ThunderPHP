const form = root.querySelector('[data-form]');
const message = root.querySelector('[data-message]');

form?.addEventListener('submit', event => {
    event.preventDefault();

    const emailInput = form.querySelector('input[type="email"]');
    const email = emailInput?.value.trim() || '';

    if (!email) {
        return;
    }

    if (message) {
        message.textContent = `Thanks — updates will be sent to ${email}.`;
    }

    form.reset();
});
