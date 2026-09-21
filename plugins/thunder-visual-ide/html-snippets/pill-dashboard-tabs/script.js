const panel = root.querySelector('[data-pdt-panel]');

root.querySelectorAll('[data-pdt-tab]').forEach(button => {
    button.addEventListener('click', () => {
        root.querySelectorAll('[data-pdt-tab]').forEach(item => {
            item.classList.toggle('is-active', item === button);
        });

        const label = button.childNodes[0].textContent.trim();
        panel.querySelector('strong').textContent = `${label} projects`;
    });
});
