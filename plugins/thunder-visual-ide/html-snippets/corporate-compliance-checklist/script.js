const items = Array.from(root.querySelectorAll('[data-blcc-item]'));
const score = root.querySelector('[data-blcc-score]');
const bar = root.querySelector('[data-blcc-bar]');
function updateComplianceScore() {
    const completed = items.filter(function (item) { return item.checked; }).length;
    const percent = Math.round((completed / items.length) * 100);
    if (score) score.textContent = percent + '%';
    if (bar) bar.style.width = percent + '%';
}
items.forEach(function (item) { item.addEventListener('change', updateComplianceScore); });
