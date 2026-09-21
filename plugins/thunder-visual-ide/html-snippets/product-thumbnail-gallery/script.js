const mainImage = root.querySelector('[data-main-image]');
const buttons = [...root.querySelectorAll('[data-image]')];

buttons.forEach(button => {
    button.addEventListener('click', () => {
        buttons.forEach(item => item.classList.remove('is-active'));
        button.classList.add('is-active');
        mainImage.src = button.dataset.image;
        mainImage.alt = button.dataset.alt;
    });
});
