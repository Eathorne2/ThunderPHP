const pastryFilters = Array.from(root.querySelectorAll('[data-pg-filter]'));
const pastries = Array.from(root.querySelectorAll('[data-pg-item]'));

pastryFilters.forEach(function (button) {
    button.addEventListener('click', function () {
        const filter = button.dataset.pgFilter;

        pastryFilters.forEach(function (item) {
            item.classList.toggle('is-active', item === button);
        });

        pastries.forEach(function (item) {
            item.hidden = filter !== 'all' && item.dataset.pgItem !== filter;
        });
    });
});
