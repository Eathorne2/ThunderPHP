const main = root.querySelector('[data-main]');
const place = root.querySelector('[data-place]');
const buttons = [...root.querySelectorAll('[data-src]')];
buttons.forEach(button => button.addEventListener('click', () => {
    buttons.forEach(item => item.classList.remove('is-active'));
    button.classList.add('is-active');
    main.src = button.dataset.src;
    main.alt = button.dataset.alt;
    place.textContent = button.dataset.place;
}));
