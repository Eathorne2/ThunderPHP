const release = new Date(root.dataset.release || Date.now() + 604800000);
const out = {
    days: root.querySelector('[data-days]'), hours: root.querySelector('[data-hours]'), minutes: root.querySelector('[data-minutes]'), seconds: root.querySelector('[data-seconds]')
};
function tick() {
    const r = Math.max(0, release - Date.now());
    const values = {
        days: Math.floor(r / 86400000), hours: Math.floor(r % 86400000 / 3600000), minutes: Math.floor(r % 3600000 / 60000), seconds: Math.floor(r % 60000 / 1000)
    };
    Object.entries(values).forEach(([key, value]) => out[key].textContent = String(value).padStart(2, '0'));
}
tick();
setInterval(tick, 1000);
root.querySelector('form')?.addEventListener('submit', event => {
    event.preventDefault();
    root.querySelector('[data-message]').textContent = 'You are on the early-access list.';
});
