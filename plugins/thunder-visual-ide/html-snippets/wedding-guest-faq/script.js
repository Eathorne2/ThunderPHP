root.querySelectorAll('.wgfa__questions article').forEach(item => {
    const button = item.querySelector('button');
    const answer = item.querySelector('.wgfa__answer');
    const icon = item.querySelector('i');

    button?.addEventListener('click', () => {
        const expanded = button.getAttribute('aria-expanded') === 'true';

        button.setAttribute('aria-expanded', String(!expanded));
        answer.hidden = expanded;
        icon.textContent = expanded ? '+' : '−';
    });
});
