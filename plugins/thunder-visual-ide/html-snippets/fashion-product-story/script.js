const mainImage = root.querySelector('[data-main]');
root.querySelectorAll('[data-image]').forEach(button => button.addEventListener('click', () => {
    mainImage.src = button.dataset.image;
}));
root.querySelector('[data-buy]')?.addEventListener('click', () => {
    root.querySelector('[data-message]').textContent = 'Product added to cart.';
});
