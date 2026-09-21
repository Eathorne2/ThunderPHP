const transferTotal = root.querySelector('[data-transfer-total]');
root.querySelectorAll('input[name="vehicle"]').forEach(input => input.addEventListener('change', () => {
    if (input.checked && transferTotal) transferTotal.textContent = `$${input.value}`;
}));
root.querySelector('form')?.addEventListener('submit', event => {
    event.preventDefault();
    const button = event.currentTarget.querySelector('button[type="submit"]');
    if (button) button.textContent = 'Transfer requested';
});
