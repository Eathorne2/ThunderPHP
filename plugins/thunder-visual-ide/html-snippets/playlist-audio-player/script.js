const player = root.querySelector('[data-media-player]');
const media = player?.querySelector('audio');
const playButton = player?.querySelector('[data-media-play]');
const progress = player?.querySelector('[data-media-progress]');
const time = player?.querySelector('[data-media-time]');
const muteButton = player?.querySelector('[data-media-mute]');

function formatTime(seconds) {
    if (!Number.isFinite(seconds)) {
        return '0:00';
    }

    const minutes = Math.floor(seconds / 60);
    const remainder = Math.floor(seconds % 60).toString().padStart(2, '0');

    return `${minutes}:${remainder}`;
}

function updatePlayer() {
    if (!media) {
        return;
    }

    const percent = media.duration ? (media.currentTime / media.duration) * 100 : 0;

    if (progress) {
        progress.value = String(percent);
    }

    if (time) {
        time.textContent = `${formatTime(media.currentTime)} / ${formatTime(media.duration)}`;
    }

    if (playButton) {
        playButton.textContent = media.paused ? 'Play' : 'Pause';
    }

    if (muteButton) {
        muteButton.textContent = media.muted ? 'Unmute' : 'Mute';
    }
}

playButton?.addEventListener('click', () => {
    if (!media) {
        return;
    }

    media.paused ? media.play() : media.pause();
});

progress?.addEventListener('input', () => {
    if (media?.duration) {
        media.currentTime = (Number(progress.value) / 100) * media.duration;
    }
});

muteButton?.addEventListener('click', () => {
    if (media) {
        media.muted = !media.muted;
        updatePlayer();
    }
});

media?.addEventListener('click', () => {
    media.paused ? media.play() : media.pause();
});

media?.addEventListener('timeupdate', updatePlayer);
media?.addEventListener('loadedmetadata', updatePlayer);
media?.addEventListener('play', updatePlayer);
media?.addEventListener('pause', updatePlayer);
updatePlayer();


const trackButtons = [...root.querySelectorAll('[data-audio-source]')];
const title = root.querySelector('[data-track-title]');
const artist = root.querySelector('[data-track-artist]');

trackButtons.forEach((button) => {
    button.addEventListener('click', () => {
        if (!media) {
            return;
        }

        media.src = button.dataset.audioSource || '';
        media.load();
        media.play();

        if (title) {
            title.textContent = button.dataset.audioTitle || 'Untitled track';
        }

        if (artist) {
            artist.textContent = button.dataset.audioArtist || 'Unknown artist';
        }

        trackButtons.forEach((item) => {
            item.classList.toggle('is-active', item === button);
        });
    });
});
