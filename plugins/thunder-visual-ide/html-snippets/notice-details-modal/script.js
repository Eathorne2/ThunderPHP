const modal = root.querySelector('[data-ndm-modal]');

const close = () => {
    modal.hidden = true;
};

root.querySelector('[data-ndm-open]')?.addEventListener('click', () => {
    modal.hidden = false;
});

root.querySelectorAll('[data-ndm-close]').forEach(button => {
    button.addEventListener('click', close);
});

modal?.addEventListener('click', event => {
    if (event.target === modal) {
        close();
    }
});
