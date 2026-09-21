const billingToggle = root.querySelector('[data-billing]');
const prices = root.querySelectorAll('[data-monthly][data-yearly]');
const periods = root.querySelectorAll('[data-period]');

billingToggle?.addEventListener('change', () => {
    const yearly = billingToggle.checked;

    prices.forEach(price => {
        price.textContent = yearly ? price.dataset.yearly || '' : price.dataset.monthly || '';
    });

    periods.forEach(period => {
        period.textContent = yearly ? '/ year' : '/ month';
    });
});
