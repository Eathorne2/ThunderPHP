const guestCount = root.querySelector('[data-fdr-count]');
const message = root.querySelector('[data-fdr-message]');
let guests = 2;

function renderGuests() {
    guestCount.textContent = String(guests);
}

root.querySelector('[data-fdr-minus]')?.addEventListener('click', function () {
    guests = Math.max(1, guests - 1);
    renderGuests();
});

root.querySelector('[data-fdr-plus]')?.addEventListener('click', function () {
    guests = Math.min(12, guests + 1);
    renderGuests();
});

root.querySelector('[data-fdr-form]')?.addEventListener('submit', function (event) {
    event.preventDefault();
    message.textContent = 'Availability checked for ' + guests + ' guest' + (guests === 1 ? '' : 's') + '.';
});
