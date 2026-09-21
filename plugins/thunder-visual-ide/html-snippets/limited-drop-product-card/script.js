const saveButton = root.querySelector('[data-limited-save]');
const saveLabel = root.querySelector('[data-limited-save-label]');

saveButton?.addEventListener('click', () => {
    const isSaved = saveButton.getAttribute('aria-pressed') === 'true';

    saveButton.setAttribute('aria-pressed', String(!isSaved));
    saveButton.classList.toggle('is-saved', !isSaved);

    if (saveLabel) {
        saveLabel.textContent = isSaved ? 'Save' : 'Saved';
    }
});
