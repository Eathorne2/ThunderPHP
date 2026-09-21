const buttons = root.querySelectorAll('[data-unit]');
const cells = root.querySelectorAll('[data-cm][data-in]');
buttons.forEach(button => button.addEventListener('click', () => {
    const unit = button.dataset.unit;
    buttons.forEach(item => item.classList.toggle('is-active', item === button));
    cells.forEach(cell => cell.textContent = cell.dataset[unit] || '');
}));
