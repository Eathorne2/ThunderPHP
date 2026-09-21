const image = root.querySelector('[data-stage-image]');
const number = root.querySelector('[data-stage-number]');
const title = root.querySelector('[data-stage-title]');
const meta = root.querySelector('[data-stage-meta]');
const buttons = [...root.querySelectorAll('[data-src]')];

buttons.forEach(button => {
    button.addEventListener('click', () => {
        buttons.forEach(item => item.classList.remove('is-active'));
        button.classList.add('is-active');
        image.src = button.dataset.src;
        image.alt = button.dataset.alt;
        number.textContent = button.dataset.number;
        title.textContent = button.dataset.title;
        meta.textContent = button.dataset.meta;
    });
});
