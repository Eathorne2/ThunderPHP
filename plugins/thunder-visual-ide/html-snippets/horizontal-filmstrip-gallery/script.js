const track = root.querySelector('[data-track]');
const scrollAmount = () => Math.max(280, track.clientWidth * 0.72);

root.querySelector('[data-prev]')?.addEventListener('click', () => {
    track.scrollBy({ left: -scrollAmount(), behavior: 'smooth' });
});

root.querySelector('[data-next]')?.addEventListener('click', () => {
    track.scrollBy({ left: scrollAmount(), behavior: 'smooth' });
});
