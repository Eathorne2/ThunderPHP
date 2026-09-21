const modal = root.querySelector('[data-modal]');

const openTrailer = () => {
    if (modal) {
        modal.hidden = false;
    }
};

const closeTrailer = () => {
    if (modal) {
        modal.hidden = true;
    }
};

root.querySelector('[data-trailer]')?.addEventListener('click', openTrailer);
root.querySelector('[data-close]')?.addEventListener('click', closeTrailer);

modal?.addEventListener('click', event => {
    if (event.target === modal) {
        closeTrailer();
    }
});

root.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
        closeTrailer();
    }
});
