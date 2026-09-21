const stage = root.querySelector('[data-carousel-stage]');
const caption = root.querySelector('[data-carousel-caption]');

root.querySelectorAll('[data-carousel-thumb]').forEach(button => {
    button.addEventListener('click', () => {
        root.querySelectorAll('[data-carousel-thumb]').forEach(item => {
            item.classList.toggle('is-active', item === button);
        });

        if (stage) {
            stage.src = button.dataset.src || '';
        }

        if (caption) {
            caption.textContent = button.dataset.caption || '';
        }
    });
});
