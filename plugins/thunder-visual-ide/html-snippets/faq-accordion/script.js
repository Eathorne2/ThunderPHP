const items = Array.from(root.querySelectorAll('.faq-list article'));
items.forEach(function (item) {
    const button = item.querySelector('button');
    const icon = item.querySelector('button i');
    button?.addEventListener('click', function () {
        const opening = !item.classList.contains('is-open');
        items.forEach(function (entry) {
            entry.classList.remove('is-open');
            entry.querySelector('button')?.setAttribute('aria-expanded', 'false');
            const entryIcon = entry.querySelector('button i');
            if (entryIcon) entryIcon.textContent = '＋';
        });
        if (opening) {
            item.classList.add('is-open');
            button.setAttribute('aria-expanded', 'true');
            if (icon) icon.textContent = '−';
        }
    });
});
