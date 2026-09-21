const searchInput = root.querySelector('[data-search]');
const categorySelect = root.querySelector('[data-category]');
const talentCards = Array.from(root.querySelectorAll('[data-talent]'));
const emptyState = root.querySelector('[data-empty]');

const filterTalent = () => {
    const query = (searchInput?.value || '').trim().toLowerCase();
    const category = categorySelect?.value || 'all';
    let visibleCount = 0;

    talentCards.forEach(card => {
        const name = (card.dataset.name || '').toLowerCase();
        const cardCategory = card.dataset.category || '';
        const visible =
            (!query || name.includes(query)) &&
            (category === 'all' || cardCategory === category);

        card.hidden = !visible;

        if (visible) {
            visibleCount += 1;
        }
    });

    if (emptyState) {
        emptyState.hidden = visibleCount > 0;
    }
};

searchInput?.addEventListener('input', filterTalent);
categorySelect?.addEventListener('change', filterTalent);
