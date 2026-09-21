const search = root.querySelector('[data-table-search]');
const rows = Array.from(root.querySelectorAll('[data-table-row]'));
const empty = root.querySelector('[data-table-empty]');
search?.addEventListener('input', function () {
    const term = search.value.trim().toLowerCase();
    let visible = 0;
    rows.forEach(function (row) {
        const matches = !term || row.textContent.toLowerCase().includes(term);
        row.hidden = !matches;
        if (matches) visible += 1;
    });
    if (empty) empty.hidden = visible > 0;
});
