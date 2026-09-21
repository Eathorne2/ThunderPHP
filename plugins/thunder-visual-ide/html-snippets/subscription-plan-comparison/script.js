const cycle = root.querySelector('[data-cycle]');
const prices = root.querySelectorAll('[data-price]');

cycle?.addEventListener('change', () => {
    prices.forEach(price => {
        price.textContent = cycle.checked ? price.dataset.year : price.dataset.month;
    });
});
