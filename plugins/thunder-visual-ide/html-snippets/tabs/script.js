const tabs = Array.from(root.querySelectorAll('[data-tab]'));
const panels = Array.from(root.querySelectorAll('[data-panel]'));
function activate(name) {
    tabs.forEach(function (tab) {
        const active = tab.dataset.tab === name;
        tab.classList.toggle('is-active', active);
        tab.setAttribute('aria-selected', active ? 'true': 'false');
    });
    panels.forEach(function (panel) {
        const active = panel.dataset.panel === name;
        panel.classList.toggle('is-active', active);
        panel.hidden = !active;
    });
}
tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
        activate(tab.dataset.tab || '');
    });
});
