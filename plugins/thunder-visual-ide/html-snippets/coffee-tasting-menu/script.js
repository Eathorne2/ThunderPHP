const coffeeFilters = Array.from(root.querySelectorAll('[data-ctm-filter]'));
const coffeeItems = Array.from(root.querySelectorAll('[data-ctm-item]'));

coffeeFilters.forEach(function (button) {
    button.addEventListener('click', function () {
        const filter = button.dataset.ctmFilter;

        coffeeFilters.forEach(function (item) {
            item.classList.toggle('is-active', item === button);
        });

        coffeeItems.forEach(function (item) {
            item.hidden = filter !== 'all' && item.dataset.ctmItem !== filter;
        });
    });
});
