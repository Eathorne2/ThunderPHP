const tracks = Array.from(root.querySelectorAll('[data-track]'));
const title = root.querySelector('[data-title]:not([data-track])');
const artist = root.querySelector('[data-artist]:not([data-track])');
const playButton = root.querySelector('[data-play]');
let currentIndex = 0;
let playing = false;

const activateTrack = index => {
    currentIndex = (index + tracks.length) % tracks.length;

    tracks.forEach((track, trackIndex) => {
        track.classList.toggle('is-active', trackIndex === currentIndex);
    });

    const selected = tracks[currentIndex];

    if (title) {
        title.textContent = selected.dataset.title || '';
    }

    if (artist) {
        artist.textContent = selected.dataset.artist || '';
    }
};

tracks.forEach((track, index) => {
    track.querySelector('button')?.addEventListener('click', () => {
        activateTrack(index);
        playing = true;

        if (playButton) {
            playButton.textContent = 'Ⅱ';
            playButton.setAttribute('aria-label', 'Pause');
        }
    });
});

root.querySelector('[data-previous]')?.addEventListener('click', () => {
    activateTrack(currentIndex - 1);
});

root.querySelector('[data-next]')?.addEventListener('click', () => {
    activateTrack(currentIndex + 1);
});

playButton?.addEventListener('click', () => {
    playing = !playing;
    playButton.textContent = playing ? 'Ⅱ' : '▶';
    playButton.setAttribute('aria-label', playing ? 'Pause' : 'Play');
});
