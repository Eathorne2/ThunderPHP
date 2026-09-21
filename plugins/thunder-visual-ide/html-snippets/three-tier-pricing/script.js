const toggle = root.querySelector('[data-billing-toggle]');
const prices = Array.from(root.querySelectorAll('[data-monthly]'));
toggle?.addEventListener('change', function () {
    prices.forEach(function (price) {
        price.textContent = toggle.checked ? price.dataset.yearly: price.dataset.monthly;
        const suffix = price.parentElement?.querySelector('em');
        if (suffix) suffix.textContent = toggle.checked ? '/year': '/month';
    });
});
