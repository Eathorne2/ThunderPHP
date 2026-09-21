const membershipToggle = root.querySelector('[data-membership-toggle]');
const membershipPrices = Array.from(root.querySelectorAll('[data-monthly]'));
const membershipPeriods = Array.from(root.querySelectorAll('[data-period]'));
membershipToggle?.addEventListener('change', () => {
    const annual = membershipToggle.checked;
    membershipPrices.forEach(price => price.textContent = `$${annual ? price.dataset.annual : price.dataset.monthly}`);
    membershipPeriods.forEach(period => period.textContent = annual ? '/ year' : '/ month');
});
