const note = root.querySelector('[data-note]');
const noteButton = root.querySelector('[data-note-toggle]');
noteButton?.addEventListener('click', () => {
    note.hidden = !note.hidden;
    noteButton.textContent = note.hidden ? 'Read curatorial note' : 'Hide curatorial note';
});
const dialog = root.querySelector('[data-dialog]');
root.querySelector('[data-fullscreen]')?.addEventListener('click', () => dialog.showModal());
root.querySelector('[data-close]')?.addEventListener('click', () => dialog.close());
