const buttons = Array.from(root.querySelectorAll('[data-blst-tab]'));
const panels = Array.from(root.querySelectorAll('[data-blst-panel]'));
buttons.forEach(function (button) {
    button.addEventListener('click', function () {
        const index = button.dataset.blstTab;
        buttons.forEach(function (item) { item.classList.toggle('is-active', item === button); });
        panels.forEach(function (panel) { panel.hidden = panel.dataset.blstPanel !== index; });
    });
});
