const spaTabs = Array.from(root.querySelectorAll('[data-spa-tab]'));
const spaPanels = Array.from(root.querySelectorAll('[data-spa-panel]'));
spaTabs.forEach(button => button.addEventListener('click', () => {
    spaTabs.forEach(item => item.classList.toggle('is-active', item === button));
    spaPanels.forEach(panel => panel.hidden = panel.dataset.spaPanel !== button.dataset.spaTab);
}));
