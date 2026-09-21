const weddingDate = new Date(root.dataset.weddingDate);
const fields = {
    days: root.querySelector('[data-days]'),
    hours: root.querySelector('[data-hours]'),
    minutes: root.querySelector('[data-minutes]'),
    seconds: root.querySelector('[data-seconds]')
};

function updateCountdown() {
    const remaining = Math.max(0, weddingDate.getTime() - Date.now());
    const day = 1000 * 60 * 60 * 24;
    const hour = 1000 * 60 * 60;
    const minute = 1000 * 60;

    fields.days.textContent = String(Math.floor(remaining / day)).padStart(3, '0');
    fields.hours.textContent = String(Math.floor((remaining % day) / hour)).padStart(2, '0');
    fields.minutes.textContent = String(Math.floor((remaining % hour) / minute)).padStart(2, '0');
    fields.seconds.textContent = String(Math.floor((remaining % minute) / 1000)).padStart(2, '0');
}

updateCountdown();
setInterval(updateCountdown, 1000);
