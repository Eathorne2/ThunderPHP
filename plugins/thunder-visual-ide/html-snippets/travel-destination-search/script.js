const searchTabs = Array.from(root.querySelectorAll('[data-search-tab]'));
const searchForm = root.querySelector('.tds__form');
const destinationInput = root.querySelector('input[type="search"]');
const dateLabel = root.querySelector('[data-date-label]');
const submitButton = searchForm?.querySelector('button[type="submit"]');

const labels = {
    stays: ['City, coast or country', 'Arrival', 'Search stays'],
    tours: ['Where do you want to explore?', 'Start date', 'Find tours'],
    escapes: ['Choose a weekend destination', 'Departure', 'Find escapes']
};

searchTabs.forEach(button => {
    button.addEventListener('click', () => {
        const type = button.dataset.searchTab;
        const copy = labels[type] || labels.stays;

        searchTabs.forEach(item => item.classList.toggle('is-active', item === button));
        destinationInput?.setAttribute('placeholder', copy[0]);
        if (dateLabel) dateLabel.textContent = copy[1];
        if (submitButton) submitButton.textContent = copy[2];
    });
});

root.querySelectorAll('.tds__popular button').forEach(button => {
    button.addEventListener('click', () => {
        if (destinationInput) destinationInput.value = button.textContent.trim();
    });
});

searchForm?.addEventListener('submit', event => event.preventDefault());
