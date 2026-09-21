const tabs = root.querySelectorAll('[data-tab]');
const panels = root.querySelectorAll('[data-panel]');

tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        const selected = tab.dataset.tab;

        tabs.forEach(item => item.setAttribute('aria-selected', String(item === tab)));
        panels.forEach(panel => {
            panel.hidden = panel.dataset.panel !== selected;
        });
    });
});
