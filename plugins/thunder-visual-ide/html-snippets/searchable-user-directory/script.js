const input = root.querySelector('[data-user-search]');
const cards = Array.from(root.querySelectorAll('[data-user-card]'));
const empty = root.querySelector('[data-user-empty]');
input?.addEventListener('input', function () {
    const query = input.value.trim().toLowerCase();
    let visible = 0;
    cards.forEach(function (card) {
        const matches = !query || card.textContent.toLowerCase().includes(query);
        card.hidden = !matches;
        if (matches) visible += 1;
    });
    if (empty) empty.hidden = visible > 0;
});
