root.querySelector('[data-elc-save]')?.addEventListener('click', function (event) {
    const button = event.currentTarget;
    const saved = button.classList.toggle('is-saved');
    button.textContent = saved ? '★' : '☆';
    button.setAttribute('aria-label', saved ? 'Remove saved profile' : 'Save profile');
});
