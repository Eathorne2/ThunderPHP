const period = root.querySelector('[data-period]');
const revenue = root.querySelector('[data-revenue]');
const values = { 'Last 7 days': '$28,460', 'Last 30 days': '$104,820', 'This year': '$842,600' };
period?.addEventListener('change', () => revenue.textContent = values[period.value]);
