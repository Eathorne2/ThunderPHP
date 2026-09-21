const password = root.querySelector('[data-members-password]');
const toggle = root.querySelector('[data-members-password-toggle]');

toggle?.addEventListener('click', () => {
    if (!password) {
        return;
    }

    const isVisible = password.type === 'text';
    password.type = isVisible ? 'password' : 'text';
    toggle.textContent = isVisible ? 'Show' : 'Hide';
});
