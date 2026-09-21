const dialog = root.querySelector('dialog');
const dialogImage = dialog?.querySelector('img');
const dialogText = dialog?.querySelector('p');
const items = [...root.querySelectorAll('[data-item]')];

items.forEach((item, index) => {
    item.addEventListener('click', () => {
        dialogImage.src = item.querySelector('img').src;
        dialogText.textContent = item.querySelector('strong').textContent;
        dialog.showModal();
    });
    item.dataset.group = index % 2 ? 'odd' : 'even';
});

root.querySelector('[data-close]')?.addEventListener('click', () => dialog.close());
root.querySelectorAll('[data-filter]').forEach(button => button.addEventListener('click', () => {
    root.querySelectorAll('[data-filter]').forEach(item => item.classList.remove('is-active'));
    button.classList.add('is-active');
    items.forEach(item => item.hidden = button.dataset.filter !== 'all' && item.dataset.group !== button.dataset.filter);
}));
