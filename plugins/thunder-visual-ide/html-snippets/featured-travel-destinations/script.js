const destinationFilters = Array.from(root.querySelectorAll('[data-filter]'));
const destinationCards = Array.from(root.querySelectorAll('[data-kind]'));

destinationFilters.forEach(button => {
    button.addEventListener('click', () => {
        const filter = button.dataset.filter;

        destinationFilters.forEach(item => item.classList.toggle('is-active', item === button));
        destinationCards.forEach(card => {
            card.hidden = filter !== 'all' && card.dataset.kind !== filter;
        });
    });
});
