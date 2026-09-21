const rows = root.querySelectorAll('[data-contents-row]');

rows.forEach((row) => {
    row.addEventListener('mouseenter', () => {
        rows.forEach((item) => item.classList.remove('is-active'));
        row.classList.add('is-active');
    });

    row.addEventListener('focus', () => {
        rows.forEach((item) => item.classList.remove('is-active'));
        row.classList.add('is-active');
    });
});
