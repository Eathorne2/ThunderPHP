const biographyButton = root.querySelector('[data-bmp-toggle]');
const biography = root.querySelector('[data-bmp-bio]');

biographyButton?.addEventListener('click', function () {
    const willOpen = biography.hidden;
    biography.hidden = !willOpen;
    biographyButton.textContent = willOpen ? 'Hide biography' : 'Read full biography';
    biographyButton.setAttribute('aria-expanded', String(willOpen));
});
