const track = root.querySelector('[data-preview]');
const playButton = root.querySelector('[data-play]');
const icon = root.querySelector('[data-icon]');

playButton?.addEventListener('click', () => {
    const playing = track?.classList.toggle('is-playing') || false;
    playButton.setAttribute('aria-label', playing ? 'Pause title track' : 'Play title track');

    if (icon) {
        icon.textContent = playing ? 'Ⅱ' : '▶';
    }
});
