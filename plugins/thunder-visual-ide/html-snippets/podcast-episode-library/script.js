const topicButtons = root.querySelectorAll('[data-topic]');
const episodes = root.querySelectorAll('[data-episode]');

root.querySelectorAll('.pel__filters [data-topic]').forEach(button => {
    button.addEventListener('click', () => {
        const topic = button.dataset.topic || 'all';

        root.querySelectorAll('.pel__filters [data-topic]').forEach(item => {
            item.classList.toggle('is-active', item === button);
        });

        episodes.forEach(episode => {
            episode.hidden = topic !== 'all' && episode.dataset.topic !== topic;
        });
    });
});

root.querySelectorAll('.pel__episodes button').forEach(button => {
    button.addEventListener('click', () => {
        const wasPlaying = button.classList.contains('is-playing');

        root.querySelectorAll('.pel__episodes button').forEach(item => {
            item.classList.remove('is-playing');
            item.textContent = '▶';
        });

        if (!wasPlaying) {
            button.classList.add('is-playing');
            button.textContent = 'Ⅱ';
        }
    });
});

root.querySelector('[data-episode-play]')?.addEventListener('click', event => {
    const button = event.currentTarget;
    const active = button.classList.toggle('is-playing');
    const icon = button.querySelector('i');

    if (icon) {
        icon.textContent = active ? 'Ⅱ' : '▶';
    }
});
