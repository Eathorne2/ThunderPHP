const contractToggle = root.querySelector('[data-ept-contract]');
const enterprisePrices = Array.from(root.querySelectorAll('[data-annual]'));

contractToggle?.addEventListener('change', function () {
    enterprisePrices.forEach(function (price) {
        price.textContent = contractToggle.checked ? price.dataset.long : price.dataset.annual;
    });
});
