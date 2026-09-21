const contactButton = root.querySelector('[data-director-contact]');
const message = root.querySelector('[data-director-message]');

contactButton?.addEventListener('click', () => {
    if (message) {
        message.textContent = 'Availability request started. Connect this action to your preferred contact flow.';
    }
});
