root.querySelectorAll('.mmg__options button').forEach(button => {
    button.addEventListener('click', () => {
        const options = button.closest('.mmg__options');

        options?.querySelectorAll('button').forEach(item => {
            item.classList.toggle('is-selected', item === button);
        });
    });
});

root.querySelectorAll('[data-add]').forEach(button => {
    button.addEventListener('click', () => {
        button.classList.add('is-added');
        button.textContent = 'Added';

        window.setTimeout(() => {
            button.classList.remove('is-added');
            button.textContent = button.closest('.mmg__featured') ? 'Add to bag' : 'Add';
        }, 1600);
    });
});
