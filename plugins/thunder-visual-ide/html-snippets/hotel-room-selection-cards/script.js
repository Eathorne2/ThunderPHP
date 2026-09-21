const rateMode = root.querySelector('[data-rate-mode]');
const rates = Array.from(root.querySelectorAll('[data-rate]'));

rateMode?.addEventListener('change', () => {
    const multiplier = rateMode.value === 'three' ? 3 : 1;

    rates.forEach(rate => {
        const nightlyRate = Number(rate.dataset.rate || 0);
        rate.textContent = `$${(nightlyRate * multiplier).toLocaleString()}`;
    });
});

root.querySelectorAll('.hrsc button').forEach(button => {
    button.addEventListener('click', () => {
        button.textContent = 'Selected';
    });
});
