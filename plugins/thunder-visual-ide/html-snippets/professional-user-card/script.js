const follow = root.querySelector('[data-user-follow]');
follow?.addEventListener('click', () => {
    const active = follow.classList.toggle('is-active');
    follow.textContent = active ? 'Following': 'Follow';
});
