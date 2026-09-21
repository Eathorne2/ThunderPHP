root.querySelectorAll('.thfaq article').forEach(item => {
    const button = item.querySelector('button');
    const panel = item.querySelector('p');
    const icon = item.querySelector('i');
    button?.addEventListener('click', () => {
        const open = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', String(!open));
        if (panel) panel.hidden = open;
        if (icon) icon.textContent = open ? '+' : '−';
        item.classList.toggle('is-open', !open);
    });
});
