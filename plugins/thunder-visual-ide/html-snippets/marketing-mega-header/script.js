root.querySelector('[data-mmh-products]')?.addEventListener('click', () => {
    const panel = root.querySelector('[data-mmh-panel]');
    panel.hidden = !panel.hidden;
});
root.querySelector('[data-mmh-toggle]')?.addEventListener('click', () =>
    root.querySelector('[data-mmh-nav]')?.classList.toggle('is-open'));
