const buttons = root.querySelectorAll('[data-period]');
const prices = root.querySelectorAll('[data-monthly][data-annual]');
const suffixes = root.querySelectorAll('[data-suffix]');
buttons.forEach(button => button.addEventListener('click', () => {
    const period = button.dataset.period;
    buttons.forEach(item => item.classList.toggle('is-active', item === button));
    prices.forEach(price => price.textContent = price.dataset[period]);
    suffixes.forEach(item => item.textContent = period === 'annual' ? '/ year' : '/ month');
}));
