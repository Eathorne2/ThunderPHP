const journalButtons = Array.from(root.querySelectorAll('[data-journal-filter]'));
const journalCards = Array.from(root.querySelectorAll('[data-journal-kind]'));
journalButtons.forEach(button => button.addEventListener('click', () => {
    const filter = button.dataset.journalFilter;
    journalButtons.forEach(item => item.classList.toggle('is-active', item === button));
    journalCards.forEach(card => card.hidden = filter !== 'all' && card.dataset.journalKind !== filter);
}));
