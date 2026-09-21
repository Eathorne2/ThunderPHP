const selectAll = root.querySelector('[data-select-all]');
const checks = Array.from(root.querySelectorAll('[data-row-check]'));
const count = root.querySelector('[data-selected-count]');
function updateCount() {
    const selected = checks.filter(function (check) {
        return check.checked;
    }).length;
    if (count) count.textContent = selected + ' selected';
    if (selectAll) {
        selectAll.checked = selected === checks.length && checks.length > 0;
        selectAll.indeterminate = selected > 0 && selected < checks.length;
    }
}
selectAll?.addEventListener('change', function () {
    checks.forEach(function (check) {
        check.checked = selectAll.checked;
    });
    updateCount();
});
checks.forEach(function (check) {
    check.addEventListener('change', updateCount);
});
