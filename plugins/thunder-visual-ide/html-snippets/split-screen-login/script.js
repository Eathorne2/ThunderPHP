const password = root.querySelector('[data-login-password]');
const toggle = root.querySelector('[data-toggle-password]');
toggle?.addEventListener('click', function () {
    if (!password) return;
    const visible = password.type === 'text';
    password.type = visible ? 'password': 'text';
    toggle.textContent = visible ? 'Show': 'Hide';
});
