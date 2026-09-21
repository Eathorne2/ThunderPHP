const insightButtons = Array.from(root.querySelectorAll('[data-blai-filter]'));
const insightCards = Array.from(root.querySelectorAll('[data-blai-card]'));
insightButtons.forEach(function (button) {
    button.addEventListener('click', function () {
        const filter = button.dataset.blaiFilter;
        insightButtons.forEach(function (item) { item.classList.toggle('is-active', item === button); });
        insightCards.forEach(function (card) { card.hidden = filter !== 'all' && card.dataset.blaiCard !== filter; });
    });
});
