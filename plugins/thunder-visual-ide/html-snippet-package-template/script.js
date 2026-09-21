const button = root.querySelector('[data-action="learn-more"]');
button?.addEventListener('click', () => {
    button.textContent = 'Selected';
});
