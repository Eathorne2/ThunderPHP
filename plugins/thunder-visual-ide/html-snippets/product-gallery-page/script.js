root.querySelectorAll('.product-gallery-page__color button').forEach(button => {
    button.addEventListener('click', () => {
        root.querySelectorAll('.product-gallery-page__color button').forEach(item => {
            item.classList.toggle('is-active', item === button);
        });
    });
});

root.querySelectorAll('.product-gallery-page__sizes button').forEach(button => {
    button.addEventListener('click', () => {
        root.querySelectorAll('.product-gallery-page__sizes button').forEach(item => {
            item.classList.toggle('is-active', item === button);
        });
    });
});
