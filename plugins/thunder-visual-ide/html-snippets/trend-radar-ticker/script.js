const ticker = root.querySelector('[data-trend-radar-ticker]');
const toggle = root.querySelector('[data-trend-radar-toggle]');

function setPaused(paused) {
    ticker?.classList.toggle('is-paused', paused);
    toggle?.setAttribute('aria-pressed', paused ? 'true' : 'false');

    if (toggle) {
        toggle.textContent = paused ? 'Resume ticker' : 'Pause ticker';
    }
}

toggle?.addEventListener('click', () => {
    setPaused(!ticker?.classList.contains('is-paused'));
});
