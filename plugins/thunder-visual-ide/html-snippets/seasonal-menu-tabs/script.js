const tabButtons = Array.from(root.querySelectorAll('[data-smt-tab]'));
const menuPanels = Array.from(root.querySelectorAll('[data-smt-panel]'));

tabButtons.forEach(function (button) {
    button.addEventListener('click', function () {
        const target = button.dataset.smtTab;

        tabButtons.forEach(function (item) {
            item.classList.toggle('is-active', item === button);
        });

        menuPanels.forEach(function (panel) {
            panel.hidden = panel.dataset.smtPanel !== target;
        });
    });
});
