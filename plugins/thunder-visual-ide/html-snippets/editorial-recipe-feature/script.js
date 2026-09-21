const servingOutput = root.querySelector('[data-erf-servings]');
const amounts = Array.from(root.querySelectorAll('[data-erf-amount]'));
let servings = 4;

function renderRecipeAmounts() {
    servingOutput.textContent = String(servings);

    amounts.forEach(function (amount) {
        const base = Number(amount.dataset.erfAmount);
        const adjusted = base * servings / 4;
        amount.textContent = Number.isInteger(adjusted) ? String(adjusted) : adjusted.toFixed(1);
    });
}

root.querySelector('[data-erf-minus]')?.addEventListener('click', function () {
    servings = Math.max(1, servings - 1);
    renderRecipeAmounts();
});

root.querySelector('[data-erf-plus]')?.addEventListener('click', function () {
    servings = Math.min(12, servings + 1);
    renderRecipeAmounts();
});
