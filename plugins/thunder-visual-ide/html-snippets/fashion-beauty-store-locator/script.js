const buttons = root.querySelectorAll('[data-location]');
const panels = root.querySelectorAll('[data-panel]');
buttons.forEach(button => button.addEventListener('click', () => {
    const target = button.dataset.location;
    buttons.forEach(item => item.classList.toggle('is-active', item === button));
    panels.forEach(panel => panel.hidden = panel.dataset.panel !== target);
}));
