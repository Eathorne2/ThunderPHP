const message = root.querySelector('[data-support-message]');
const count = root.querySelector('[data-support-count]');
function updateCount() {
    if (count) count.textContent = String(message?.value.length || 0);
}
message?.addEventListener('input', updateCount);
updateCount();
