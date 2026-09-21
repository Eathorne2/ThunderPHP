const dismissButton = root.querySelector('[data-dismiss]');

dismissButton?.addEventListener('click', () => {
    root.remove();
});
