const message = root.querySelector('[data-message]');
root.querySelectorAll('[data-follow]').forEach(button => button.addEventListener('click', () => {
    button.classList.toggle('is-following');
    button.textContent = button.classList.contains('is-following') ? 'Following' : 'Follow';
    message.textContent = button.classList.contains('is-following') ? 'Community added to your feed.' : 'Community removed from your feed.';
}));
