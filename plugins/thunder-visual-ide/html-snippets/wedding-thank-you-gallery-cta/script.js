const copyButton = root.querySelector('[data-copy]');
const status = root.querySelector('[data-status]');

copyButton?.addEventListener('click', async () => {
    try {
        await navigator.clipboard.writeText('WILLOW14');
        status.textContent = 'Gallery password copied: WILLOW14';
    } catch (error) {
        status.textContent = 'Gallery password: WILLOW14';
    }
});
