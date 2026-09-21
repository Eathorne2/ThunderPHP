const trigger = root.querySelector('[data-mega-trigger]');
const panel = root.querySelector('[data-mega-panel]');
trigger?.addEventListener('click', () => {
    const open = panel.hasAttribute('hidden');
    panel.toggleAttribute('hidden', !open);
    trigger.setAttribute('aria-expanded', String(open));
});
