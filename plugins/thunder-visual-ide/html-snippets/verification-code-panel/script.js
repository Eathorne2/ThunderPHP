const inputs = [...root.querySelectorAll('[data-vcp-code] input')];
inputs.forEach((input, index) => input.addEventListener('input', () => {
    input.value = input.value.replace(/\D/g, '').slice(0, 1);
    if (input.value) inputs[index + 1]?.focus();
}));
root.querySelector('form')?.addEventListener('submit', event => event.preventDefault());
