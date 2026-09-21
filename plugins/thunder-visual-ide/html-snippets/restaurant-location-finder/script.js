const locationTabs = Array.from(root.querySelectorAll('[data-rlf-tab]'));
const locationPanels = Array.from(root.querySelectorAll('[data-rlf-panel]'));

locationTabs.forEach(function (button) {
    button.addEventListener('click', function () {
        const target = button.dataset.rlfTab;

        locationTabs.forEach(function (item) {
            item.classList.toggle('is-active', item === button);
        });

        locationPanels.forEach(function (panel) {
            panel.hidden = panel.dataset.rlfPanel !== target;
        });
    });
});
