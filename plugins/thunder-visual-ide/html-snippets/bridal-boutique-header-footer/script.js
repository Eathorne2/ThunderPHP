const menuButton = root.querySelector('[data-menu]');
const navigation = root.querySelector('[data-nav]');

menuButton?.addEventListener('click', () => {
    const open = navigation?.classList.toggle('is-open') || false;
    menuButton.setAttribute('aria-expanded', String(open));
});

root.querySelector('.bbhf__newsletter')?.addEventListener('submit', event => {
    event.preventDefault();

    const status = event.currentTarget.querySelector('[data-status]');
    status.textContent = 'Thank you. You are on the list.';
});
