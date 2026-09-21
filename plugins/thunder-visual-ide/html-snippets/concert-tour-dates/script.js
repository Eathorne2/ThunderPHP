const filterButtons = root.querySelectorAll('[data-filter]');
const tourDates = root.querySelectorAll('[data-region]');

filterButtons.forEach(button => {
    button.addEventListener('click', () => {
        const filter = button.dataset.filter || 'all';

        filterButtons.forEach(item => {
            item.classList.toggle('is-active', item === button);
        });

        tourDates.forEach(item => {
            item.hidden = filter !== 'all' && item.dataset.region !== filter;
        });
    });
});
