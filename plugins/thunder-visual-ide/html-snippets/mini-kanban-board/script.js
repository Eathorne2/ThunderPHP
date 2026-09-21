let dragged = null;
const drops = Array.from(root.querySelectorAll('[data-drop]'));
function refreshCounts() {
    root.querySelectorAll('[data-column]').forEach(function (column) {
        const count = column.querySelectorAll('article').length;
        const label = column.querySelector('[data-count]');
        if (label) label.textContent = String(count);
    });
}
root.addEventListener('dragstart', function (event) {
    const card = event.target.closest('article[draggable="true"]');
    if (card) dragged = card;
});
drops.forEach(function (drop) {
    drop.addEventListener('dragover', function (event) {
        event.preventDefault();
        drop.classList.add('is-over');
    });
    drop.addEventListener('dragleave', function () {
        drop.classList.remove('is-over');
    });
    drop.addEventListener('drop', function (event) {
        event.preventDefault();
        drop.classList.remove('is-over');
        if (dragged) drop.appendChild(dragged);
        refreshCounts();
    });
});
root.querySelector('[data-add-task]')?.addEventListener('click', function () {
    const task = document.createElement('article');
    task.draggable = true;
    task.innerHTML = '<small>New</small><strong>Untitled task</strong><p>Not scheduled</p>';
    drops[0]?.appendChild(task);
    refreshCounts();
});
