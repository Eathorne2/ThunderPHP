root.querySelectorAll('[data-link]').forEach(link => link.addEventListener('click', () => {
    root.querySelectorAll('[data-link]').forEach(item => item.classList.remove('is-active'));
    link.classList.add('is-active');
}));
root.querySelector('[data-link]')?.classList.add('is-active');
root.querySelector('[data-action]')?.addEventListener('click', () => {
    root.querySelector('[data-message]').textContent = 'The next step is ready.';
});
