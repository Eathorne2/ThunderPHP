const button = root.querySelector('[data-toggle]');
const provenance = root.querySelector('[data-provenance]');
button?.addEventListener('click', () => {
    provenance.hidden = !provenance.hidden;
    button.textContent = provenance.hidden ? 'Show provenance' : 'Hide provenance';
});
