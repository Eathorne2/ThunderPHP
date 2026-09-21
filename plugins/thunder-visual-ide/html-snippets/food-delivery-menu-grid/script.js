const filterButtons = Array.from(root.querySelectorAll('[data-fdmg-filter]'));
const foodItems = Array.from(root.querySelectorAll('[data-fdmg-item]'));
const notice = root.querySelector('[data-fdmg-notice]');

filterButtons.forEach(function (button) {
    button.addEventListener('click', function () {
        const filter = button.dataset.fdmgFilter;

        filterButtons.forEach(function (item) {
            item.classList.toggle('is-active', item === button);
        });

        foodItems.forEach(function (item) {
            const categories = item.dataset.fdmgItem.split(' ');
            item.hidden = filter !== 'all' && !categories.includes(filter);
        });
    });
});

root.querySelectorAll('[data-fdmg-add]').forEach(function (button) {
    button.addEventListener('click', function () {
        const title = button.closest('article').querySelector('h3').textContent.trim();
        notice.textContent = title + ' added to your order.';
    });
});
