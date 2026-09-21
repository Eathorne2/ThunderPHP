const offerTabs = Array.from(root.querySelectorAll('[data-offer]'));
const offerPanels = Array.from(root.querySelectorAll('[data-offer-panel]'));
offerTabs.forEach(button => button.addEventListener('click', () => {
    offerTabs.forEach(item => item.classList.toggle('is-active', item === button));
    offerPanels.forEach(panel => panel.hidden = panel.dataset.offerPanel !== button.dataset.offer);
}));
