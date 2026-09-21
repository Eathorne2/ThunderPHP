const button = root.querySelector('[data-product-favorite]');
button?.addEventListener('click', () => {
    button.classList.toggle('is-active');
    button.textContent = button.classList.contains('is-active') ? '♥': '♡';
});
