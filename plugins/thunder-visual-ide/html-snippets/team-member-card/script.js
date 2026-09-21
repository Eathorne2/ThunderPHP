const follow = root.querySelector('[data-follow-button]');
follow?.addEventListener('click', function () {
    const active = follow.classList.toggle('is-following');
    follow.textContent = active ? 'Following': 'Follow';
});
