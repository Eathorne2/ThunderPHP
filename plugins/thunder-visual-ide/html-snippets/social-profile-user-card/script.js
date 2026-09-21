const followButton = root.querySelector('[data-follow-user]');

followButton?.addEventListener('click', () => {
    const isFollowing = followButton.classList.toggle('is-following');

    followButton.textContent = isFollowing ? 'Following' : 'Follow';
    followButton.setAttribute('aria-pressed', String(isFollowing));
});
