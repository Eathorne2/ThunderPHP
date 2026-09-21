const compare = root.querySelector('[data-compare]');
const cart = root.querySelector('[data-cart]');
const message = root.querySelector('[data-message]');
compare?.addEventListener('change', () => message.textContent = compare.checked ? 'Added to comparison.' : 'Removed from comparison.');
cart?.addEventListener('click', () => message.textContent = 'Product added to cart.');
