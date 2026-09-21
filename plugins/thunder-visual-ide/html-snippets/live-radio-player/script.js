const playButton = root.querySelector('[data-play]');
const currentTime = root.querySelector('[data-current]');
let playing = false;
let seconds = 0;
let timer;

playButton?.addEventListener('click', () => {
    playing = !playing;
    root.classList.toggle('is-playing', playing);
    playButton.textContent = playing ? '❚❚' : '▶';

    clearInterval(timer);
    if (playing) {
        timer = setInterval(() => {
            seconds += 1;
            currentTime.textContent = `${Math.floor(seconds / 60)}:${String(seconds % 60).padStart(2, '0')}`;
        }, 1000);
    }
});
