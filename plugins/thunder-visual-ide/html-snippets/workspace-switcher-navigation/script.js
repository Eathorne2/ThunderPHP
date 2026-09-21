root.querySelectorAll('[data-step]').forEach(button => button.addEventListener('click', () => {
    root.querySelectorAll('[data-step]').forEach(item => item.classList.remove('is-active'));
    button.classList.add('is-active');
}));
root.querySelectorAll('[data-link]').forEach(link => link.addEventListener('click', () => {
    root.querySelectorAll('[data-link]').forEach(item => item.classList.remove('is-active'));
    link.classList.add('is-active');
}));
const workspaceButton = root.querySelector('[data-workspace]');
const workspaceMenu = root.querySelector('[data-menu]');
workspaceButton?.addEventListener('click', () => workspaceMenu.classList.toggle('is-open'));
workspaceMenu?.querySelectorAll('button').forEach(button => button.addEventListener('click', () => {
    workspaceButton.querySelector('strong').textContent = button.textContent;
    workspaceMenu.classList.remove('is-open');
}));
root.querySelector('[data-step]')?.classList.add('is-active');
root.querySelector('[data-link]')?.classList.add('is-active');
