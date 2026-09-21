root.addEventListener('keydown', event => {
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        root.querySelector('input')?.focus();
    }
});
root.querySelector('form')?.addEventListener('submit', event => event.preventDefault());
