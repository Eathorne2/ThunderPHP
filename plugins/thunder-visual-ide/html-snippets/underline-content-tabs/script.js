const tabs = [...root.querySelectorAll('[data-uct-tab]')];
const panels = [...root.querySelectorAll('[data-uct-panel]')];
tabs.forEach(tab => tab.addEventListener('click', () => {
    tabs.forEach(item => {
        const active = item === tab;
        item.classList.toggle('is-active', active);
        item.setAttribute('aria-selected', String(active));
    });
    panels.forEach(panel => panel.hidden = panel.dataset.uctPanel !== tab.dataset.uctTab);
}));
