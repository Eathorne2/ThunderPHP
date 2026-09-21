const buttons = root.querySelectorAll('[data-srs-routine-tab]');
const panels = root.querySelectorAll('[data-srs-routine-panel]');
buttons.forEach((button) => {
    button.addEventListener('click', () => {
        const target = button.getAttribute('data-srs-routine-tab');
        buttons.forEach((item) => item.classList.toggle('is-active', item === button));
        panels.forEach((panel) => {
            const active = panel.getAttribute('data-srs-routine-panel') === target;
            panel.hidden = !active;
            panel.classList.toggle('is-active', active);
        });
    });
});
