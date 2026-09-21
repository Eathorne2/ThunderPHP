const form = root.querySelector('[data-contact-form]');
const result = root.querySelector('[data-contact-result]');
form?.addEventListener('submit', function (event) {
    event.preventDefault();
    if (result) result.textContent = 'Thanks — your message is ready to be submitted.';
});
