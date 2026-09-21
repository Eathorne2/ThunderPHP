const tabs = [...root.querySelectorAll('[data-tab]')];
const panels = [...root.querySelectorAll('[data-panel]')];
function activate(index) {
    tabs.forEach((tab, tabIndex) => tab.classList.toggle('is-active', tabIndex === index));
    panels.forEach((panel, panelIndex) => panel.classList.toggle('is-active', panelIndex === index));
}
tabs.forEach((tab, index) => tab.addEventListener('click', () => activate(index)));
activate(0);
