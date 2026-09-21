const output = root.querySelector('[data-qty]');
let value = 1;
root.querySelector('[data-qty-minus]')?.addEventListener('click', () => {
    value = Math.max(1, value - 1);
    output.value = output.textContent = String(value);
});
root.querySelector('[data-qty-plus]')?.addEventListener('click', () => {
    value += 1;
    output.value = output.textContent = String(value);
});
