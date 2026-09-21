const followButton = root.querySelector('[data-contributor-follow]');

followButton?.addEventListener('click', () => {
    const isFollowing = followButton.getAttribute('aria-pressed') === 'true';

    followButton.setAttribute('aria-pressed', String(!isFollowing));
    followButton.classList.toggle('is-following', !isFollowing);
    followButton.textContent = isFollowing ? 'Follow' : 'Following';
});
