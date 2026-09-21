const diningTabs = Array.from(root.querySelectorAll('[data-dining-tab]'));
const diningPanels = Array.from(root.querySelectorAll('[data-dining-panel]'));
diningTabs.forEach(button => button.addEventListener('click', () => {
    diningTabs.forEach(item => item.classList.toggle('is-active', item === button));
    diningPanels.forEach(panel => panel.hidden = panel.dataset.diningPanel !== button.dataset.diningTab);
}));
