const tabs = [...root.querySelectorAll('[data-vst-tab]')];
const panels = [...root.querySelectorAll('[data-vst-panel]')];
tabs.forEach(tab => tab.addEventListener('click', () => {
    tabs.forEach(item => item.classList.toggle('is-active', item === tab));
    panels.forEach(panel => panel.hidden = panel.dataset.vstPanel !== tab.dataset.vstTab);
}));
