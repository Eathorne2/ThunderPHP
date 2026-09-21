const message = root.querySelector('[data-message]');
root.querySelectorAll('[data-bundle]').forEach(button => {
    button.addEventListener('click', () => {
        root.querySelectorAll('[data-bundle]').forEach(item => item.closest('article').classList.remove('is-selected'));
        button.closest('article').classList.add('is-selected');
        message.textContent = `${button.closest('article').querySelector('h3').textContent} selected.`;
    });
});
