const message = root.querySelector('[data-message]');
let rating = 0;

root.querySelectorAll('[data-rating]').forEach(button => {
    button.addEventListener('click', () => {
        rating = Number(button.dataset.rating);
        root.querySelectorAll('[data-rating]').forEach(star => star.classList.toggle('is-active', Number(star.dataset.rating) <= rating));
    });
});

root.querySelectorAll('[data-choice]').forEach(button => button.addEventListener('click', () => {
    message.textContent = `Thank you. You selected “${button.dataset.choice}”.`;
}));

root.querySelectorAll('[data-submit]').forEach(button => button.addEventListener('click', () => {
    message.textContent = rating ? `Thank you for your ${rating}-star rating.` : 'Thank you. Your feedback form is ready.';
}));
