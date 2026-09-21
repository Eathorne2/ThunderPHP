const photos = [
    { src: 'https://picsum.photos/id/1019/1900/1300', alt: 'Rocky coast beneath a dramatic sky', title: 'Coastal Weather', copy: 'A study of changing light across the western cliffs.', location: 'Atlantic Coast' },
    { src: 'https://picsum.photos/id/1015/1900/1300', alt: 'River through a mountain valley', title: 'River Passage', copy: 'Morning light follows the water through the valley.', location: 'Highland Pass' },
    { src: 'https://picsum.photos/id/1036/1900/1300', alt: 'Lake surrounded by trees', title: 'Still Water', copy: 'The forest settles into the reflection before dusk.', location: 'Lake District' },
    { src: 'https://picsum.photos/id/1050/1900/1300', alt: 'Open ocean horizon', title: 'Western Edge', copy: 'The last frame before the horizon disappears.', location: 'West Coast' }
];
let current = 0;
const image = root.querySelector('[data-image]');
const index = root.querySelector('[data-index]');
const title = root.querySelector('[data-title]');
const copy = root.querySelector('[data-copy]');
const location = root.querySelector('[data-location]');
const panel = root.querySelector('[data-panel]');
const render = () => {
    const photo = photos[current];
    image.src = photo.src;
    image.alt = photo.alt;
    index.textContent = `${String(current + 1).padStart(2, '0')} / ${String(photos.length).padStart(2, '0')}`;
    title.textContent = photo.title;
    copy.textContent = photo.copy;
    location.textContent = photo.location;
};
const move = direction => { current = (current + direction + photos.length) % photos.length; render(); };
root.querySelector('[data-prev]')?.addEventListener('click', () => move(-1));
root.querySelector('[data-next]')?.addEventListener('click', () => move(1));
root.querySelector('[data-info]')?.addEventListener('click', event => {
    panel.hidden = !panel.hidden;
    event.currentTarget.setAttribute('aria-expanded', String(!panel.hidden));
});
root.addEventListener('keydown', event => {
    if (event.key === 'ArrowLeft') move(-1);
    if (event.key === 'ArrowRight') move(1);
});
root.tabIndex = 0;
