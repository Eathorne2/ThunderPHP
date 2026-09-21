const officeButtons = Array.from(root.querySelectorAll('[data-blol-office]'));
const officePanels = Array.from(root.querySelectorAll('[data-blol-panel]'));
officeButtons.forEach(function (button) {
    button.addEventListener('click', function () {
        const office = button.dataset.blolOffice;
        officeButtons.forEach(function (item) { item.classList.toggle('is-active', item === button); });
        officePanels.forEach(function (panel) { panel.hidden = panel.dataset.blolPanel !== office; });
    });
});
