const message = root.querySelector('[data-message]');
root.querySelectorAll('[data-action]').forEach(button => button.addEventListener('click', () => {
    message.textContent = `${button.dataset.action} selected.`;
}));
