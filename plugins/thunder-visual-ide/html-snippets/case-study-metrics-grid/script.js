const filterButtons = Array.from(root.querySelectorAll('[data-csmg-filter]'));
const caseStudies = Array.from(root.querySelectorAll('[data-csmg-card]'));

filterButtons.forEach(function (button) {
    button.addEventListener('click', function () {
        const filter = button.dataset.csmgFilter;

        filterButtons.forEach(function (item) {
            item.classList.toggle('is-active', item === button);
        });

        caseStudies.forEach(function (card) {
            card.hidden = filter !== 'all' && card.dataset.csmgCard !== filter;
        });
    });
});
