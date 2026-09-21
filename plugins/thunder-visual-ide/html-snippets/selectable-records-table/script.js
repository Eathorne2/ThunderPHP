const all = root.querySelector('[data-srt-all]');
const rows = [...root.querySelectorAll('[data-srt-row]')];
const count = root.querySelector('[data-srt-count]');
const update = () => {
    const selected = rows.filter(row => row.checked).length;
    count.textContent = `${selected} selected`;
    all.indeterminate = selected > 0 && selected < rows.length;
    all.checked = selected === rows.length;
};
all?.addEventListener('change', () => {
    rows.forEach(row => row.checked = all.checked);
    update();
});
rows.forEach(row => row.addEventListener('change', update));
