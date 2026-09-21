const buttons = [...root.querySelectorAll('[data-filter]')];
const cards = [...root.querySelectorAll('[data-category]')];

buttons.forEach(button => {
    button.addEventListener('click', () => {
        buttons.forEach(item => item.classList.remove('is-active'));
        button.classList.add('is-active');

        cards.forEach(card => {
            card.hidden = button.dataset.filter !== 'all'
                && card.dataset.category !== button.dataset.filter;
        });
    });
});
