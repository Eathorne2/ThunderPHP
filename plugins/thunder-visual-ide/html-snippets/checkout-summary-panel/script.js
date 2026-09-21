const applyButton = root.querySelector('[data-apply]');
const input = root.querySelector('input');
const discount = root.querySelector('[data-discount]');
const total = root.querySelector('[data-total]');
const message = root.querySelector('[data-message]');

applyButton?.addEventListener('click', () => {
    const valid = input.value.trim().toUpperCase() === 'SAVE20';
    discount.textContent = valid ? '−$24' : '—';
    total.textContent = valid ? '$96' : '$120';
    message.textContent = valid ? 'Promo code applied.' : 'Try the demo code SAVE20.';
});
