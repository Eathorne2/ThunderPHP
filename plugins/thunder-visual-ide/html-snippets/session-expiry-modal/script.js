const dialog = root.querySelector('[data-dialog]');
const openButton = root.querySelector('[data-open]');
const closeButtons = root.querySelectorAll('[data-close], [data-confirm]');

function setDialog(open) {
    dialog.hidden = !open;
    document.body.style.overflow = open ? 'hidden' : '';
}

openButton?.addEventListener('click', () => setDialog(true));
closeButtons.forEach(button => button.addEventListener('click', () => setDialog(false)));
dialog?.addEventListener('click', event => {
    if (event.target === dialog) setDialog(false);
});
