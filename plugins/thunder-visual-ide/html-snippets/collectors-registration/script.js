const password = root.querySelector('[data-collectors-password]');
const toggle = root.querySelector('[data-collectors-password-toggle]');

toggle?.addEventListener('click', () => {
    if (!password) {
        return;
    }

    const isVisible = password.type === 'text';
    password.type = isVisible ? 'password' : 'text';
    toggle.textContent = isVisible ? 'Show' : 'Hide';
});
