const tabs = Array.from(root.querySelectorAll('[data-profile-tab]'));
const panels = Array.from(root.querySelectorAll('[data-profile-panel]'));
tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
        const name = tab.dataset.profileTab;
        tabs.forEach(function (item) {
            item.classList.toggle('is-active', item === tab);
        });
        panels.forEach(function (panel) {
            const active = panel.dataset.profilePanel === name;
            panel.classList.toggle('is-active', active);
            panel.hidden = !active;
        });
    });
});
