const track = root.querySelector('[data-track]');
const slides = [...root.querySelectorAll('[data-slide]')];
const count = root.querySelector('[data-count]');
const pauseButton = root.querySelector('[data-pause]');
let index = 0;
let paused = false;
let timer;

function show(next) {
    index = (next + slides.length) % slides.length;
    track.style.transform = `translateX(-${index * 100}%)`;
    count.textContent = `${index + 1} / ${slides.length}`;
}
function autoStart() {
    clearInterval(timer);
    timer = setInterval(() => { if (!paused) show(index + 1); }, 4500);
}
root.querySelector('[data-prev]')?.addEventListener('click', () => show(index - 1));
root.querySelector('[data-next]')?.addEventListener('click', () => show(index + 1));
pauseButton?.addEventListener('click', () => { paused = !paused; pauseButton.textContent = paused ? 'Play' : 'Pause'; });
show(0);
autoStart();
