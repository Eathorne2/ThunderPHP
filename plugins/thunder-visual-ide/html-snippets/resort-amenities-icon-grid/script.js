root.querySelectorAll('.raig article button').forEach(button => {
    button.addEventListener('click', () => {
        const card = button.closest('article');
        const open = card?.classList.toggle('is-open') || false;
        button.textContent = open ? 'Hide details' : 'Details';
    });
});
