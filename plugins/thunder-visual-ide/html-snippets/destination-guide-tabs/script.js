const guideTabs = Array.from(root.querySelectorAll('[data-guide-tab]'));
const guidePanels = Array.from(root.querySelectorAll('[data-guide-panel]'));
guideTabs.forEach(button => button.addEventListener('click', () => {
    guideTabs.forEach(item => item.classList.toggle('is-active', item === button));
    guidePanels.forEach(panel => panel.hidden = panel.dataset.guidePanel !== button.dataset.guideTab);
}));
