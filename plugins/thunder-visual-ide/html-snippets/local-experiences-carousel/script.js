const experienceTrack = root.querySelector('[data-lec-track]');
const scrollAmount = () => Math.max(280, (experienceTrack?.clientWidth || 600) * 0.7);
root.querySelector('[data-lec-prev]')?.addEventListener('click', () => experienceTrack?.scrollBy({ left: -scrollAmount(), behavior: 'smooth' }));
root.querySelector('[data-lec-next]')?.addEventListener('click', () => experienceTrack?.scrollBy({ left: scrollAmount(), behavior: 'smooth' }));
