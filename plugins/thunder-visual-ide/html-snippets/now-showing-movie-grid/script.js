const genreButtons = root.querySelectorAll('[data-genre]');
const films = root.querySelectorAll('[data-film]');

root.querySelectorAll('.nsmg__filters [data-genre]').forEach(button => {
    button.addEventListener('click', () => {
        const genre = button.dataset.genre || 'all';

        root.querySelectorAll('.nsmg__filters [data-genre]').forEach(item => {
            item.classList.toggle('is-active', item === button);
        });

        films.forEach(film => {
            film.hidden = genre !== 'all' && film.dataset.genre !== genre;
        });
    });
});

root.querySelectorAll('.nsmg__times button').forEach(button => {
    button.addEventListener('click', () => {
        const card = button.closest('[data-film]');

        card?.querySelectorAll('.nsmg__times button').forEach(item => {
            item.classList.toggle('is-selected', item === button);
        });
    });
});
