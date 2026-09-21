const image = root.querySelector('[data-image]');
const label = root.querySelector('[data-zoom]');
let zoom = 100;
const update = () => { image.style.width = `${zoom}%`; label.textContent = `${zoom}%`; };
root.querySelector('[data-zoom-in]')?.addEventListener('click', () => { zoom = Math.min(180, zoom + 20); update(); });
root.querySelector('[data-zoom-out]')?.addEventListener('click', () => { zoom = Math.max(60, zoom - 20); update(); });
