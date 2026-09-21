const marketForm = root.querySelector('[data-ompg-form]');
const marketMessage = root.querySelector('[data-ompg-message]');

marketForm?.addEventListener('submit', function (event) {
    event.preventDefault();
    marketMessage.textContent = 'You are on the market list.';
    marketForm.reset();
});
