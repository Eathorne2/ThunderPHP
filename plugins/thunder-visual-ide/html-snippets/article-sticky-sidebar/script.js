const copyButton = root.querySelector('[data-copy-article]');

copyButton?.addEventListener('click', async () => {
    try {
        await navigator.clipboard.writeText(window.location.href);
        copyButton.textContent = 'Link copied';
    } catch (error) {
        copyButton.textContent = 'Copy unavailable';
    }
});
