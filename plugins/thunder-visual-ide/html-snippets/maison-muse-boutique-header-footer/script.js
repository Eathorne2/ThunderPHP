const menuButton = root.querySelector('[data-menu]');
const navigation = root.querySelector('[data-nav]');
menuButton?.addEventListener('click', () => {
    const open = navigation?.classList.toggle('is-open') || false;
    menuButton.setAttribute('aria-expanded', String(open));
});
root.querySelector('footer form')?.addEventListener('submit', event => {
    event.preventDefault();
    const button = event.currentTarget.querySelector('button');
    if (button)
        button.textContent = 'Joined';
});
