const status = root.querySelector('[data-status]');
root.querySelectorAll('form').forEach(form => form.addEventListener('submit', event => {
    event.preventDefault();
    status.textContent = 'Thank you. Your request has been recorded.';
}));

root.querySelector('[data-copy]')?.addEventListener('click', async () => {
    const input = root.querySelector('input');
    try { await navigator.clipboard.writeText(input.value); } catch { input.select(); }
    status.textContent = 'Referral link copied.';
});

const announcements = [...root.querySelectorAll('[data-message]')];
let announcementIndex = 0;
function showAnnouncement() { announcements.forEach((item, index) => item.classList.toggle('is-active', index === announcementIndex)); }
root.querySelector('[data-next]')?.addEventListener('click', () => { announcementIndex = (announcementIndex + 1) % announcements.length; showAnnouncement(); });
showAnnouncement();

const end = Date.now() + 12 * 24 * 60 * 60 * 1000;
const timer = setInterval(() => {
    const remaining = Math.max(0, end - Date.now());
    const values = {
        days: Math.floor(remaining / 86400000),
        hours: Math.floor(remaining / 3600000) % 24,
        minutes: Math.floor(remaining / 60000) % 60,
        seconds: Math.floor(remaining / 1000) % 60
    };
    Object.entries(values).forEach(([key, value]) => {
        const node = root.querySelector(`[data-${key}]`);
        if (node) node.textContent = String(value).padStart(2, '0');
    });
    if (!remaining) clearInterval(timer);
}, 1000);
