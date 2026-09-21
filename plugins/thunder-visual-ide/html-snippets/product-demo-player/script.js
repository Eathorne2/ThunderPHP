const video = root.querySelector('video');
const playButton = root.querySelector('[data-play]');
const progress = root.querySelector('[data-progress]');
const current = root.querySelector('[data-current]');
let playing = false;
let demoSeconds = 0;
let timer;

function renderTime() {
    current.textContent = `${Math.floor(demoSeconds / 60)}:${String(demoSeconds % 60).padStart(2, '0')}`;
    progress.value = Math.min(100, demoSeconds / 11.04);
}
playButton?.addEventListener('click', () => {
    playing = !playing;
    playButton.textContent = playing ? '❚❚' : '▶';
    clearInterval(timer);
    if (playing) timer = setInterval(() => { demoSeconds = Math.min(1104, demoSeconds + 1); renderTime(); }, 1000);
});
root.querySelectorAll('[data-time]').forEach(button => button.addEventListener('click', () => {
    root.querySelectorAll('[data-time]').forEach(item => item.classList.remove('is-active'));
    button.classList.add('is-active');
    demoSeconds = Number(button.dataset.time);
    renderTime();
}));
progress?.addEventListener('input', () => { demoSeconds = Math.round(Number(progress.value) * 11.04); renderTime(); });
renderTime();
