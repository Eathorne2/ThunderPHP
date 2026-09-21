const packageButtons = Array.from(root.querySelectorAll('[data-duration]'));
const packageCards = Array.from(root.querySelectorAll('[data-trip-duration]'));

packageButtons.forEach(button => {
    button.addEventListener('click', () => {
        const duration = button.dataset.duration;

        packageButtons.forEach(item => item.classList.toggle('is-active', item === button));
        packageCards.forEach(card => {
            card.hidden = duration !== 'all' && card.dataset.tripDuration !== duration;
        });
    });
});
