root.querySelectorAll('[data-polaroid]').forEach(item => {
    item.addEventListener('click', () => {
        root.querySelectorAll('[data-polaroid]').forEach(other => {
            other.classList.toggle('is-focused', other === item);
        });
    });
});
