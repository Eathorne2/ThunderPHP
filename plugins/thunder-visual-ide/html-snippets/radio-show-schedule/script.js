const dayButtons = root.querySelectorAll('[data-day]');
const shows = root.querySelectorAll('[data-show]');

dayButtons.forEach(button => {
    button.addEventListener('click', () => {
        const day = button.dataset.day || '';

        dayButtons.forEach(item => {
            item.classList.toggle('is-active', item === button);
        });

        shows.forEach(show => {
            const days = (show.dataset.days || '').split(',');
            show.hidden = !days.includes(day);
        });
    });
});
