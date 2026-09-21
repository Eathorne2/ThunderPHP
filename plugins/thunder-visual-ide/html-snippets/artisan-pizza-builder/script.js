const sizeOptions = Array.from(root.querySelectorAll('[data-apb-size]'));
const extras = Array.from(root.querySelectorAll('[data-apb-extra]'));
const totalOutput = root.querySelector('[data-apb-total]');
const message = root.querySelector('[data-apb-message]');

function updatePizzaTotal() {
    const selectedSize = sizeOptions.find(function (input) {
        return input.checked;
    });
    let total = Number(selectedSize?.value || 0);

    extras.forEach(function (input) {
        if (input.checked) {
            total += Number(input.value);
        }
    });

    totalOutput.textContent = '$' + total.toFixed(2);
}

sizeOptions.concat(extras).forEach(function (input) {
    input.addEventListener('change', updatePizzaTotal);
});

root.querySelector('[data-apb-form]')?.addEventListener('submit', function (event) {
    event.preventDefault();
    message.textContent = 'Your custom pizza has been added.';
});
