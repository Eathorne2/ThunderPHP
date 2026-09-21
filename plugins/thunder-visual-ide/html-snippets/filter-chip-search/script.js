root.querySelectorAll('.fcs-search__filters button').forEach(button => {
    button.addEventListener('click', () => {
        button.remove();
    });
});
