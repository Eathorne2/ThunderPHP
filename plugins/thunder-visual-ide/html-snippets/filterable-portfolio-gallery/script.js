const filters = Array.from(root.querySelectorAll('[data-gallery-filter]'));
const items = Array.from(root.querySelectorAll('[data-gallery-category]'));
filters.forEach(function (button) {
    button.addEventListener('click', function () {
        const category = button.dataset.galleryFilter || 'all';
        filters.forEach(function (item) {
            item.classList.toggle('is-active', item === button);
        });
        items.forEach(function (item) {
            item.hidden = category !== 'all' && item.dataset.galleryCategory !== category;
        });
    });
});
