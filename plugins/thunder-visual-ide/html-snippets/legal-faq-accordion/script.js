root.querySelectorAll('.blfq-list button').forEach(function (button) {
    button.addEventListener('click', function () {
        const panel = button.nextElementSibling;
        const expanded = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', String(!expanded));
        button.querySelector('b').textContent = expanded ? '+' : '−';
        if (panel) panel.hidden = expanded;
    });
});
