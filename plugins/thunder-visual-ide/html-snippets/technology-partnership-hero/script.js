root.querySelector('[data-tph-demo]')?.addEventListener('click', function () {
    root.querySelector('.tph-hero__panel')?.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });
});
