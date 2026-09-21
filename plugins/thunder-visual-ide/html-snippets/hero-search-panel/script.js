const input = root.querySelector('[data-input]');
const suggestions = root.querySelector('[data-suggestions]');
const form = root.querySelector('form');
const status = root.querySelector('[data-status]');
input?.addEventListener('focus', () => suggestions.classList.add('is-open'));
input?.addEventListener('input', () => suggestions.classList.toggle('is-open', input.value.length < 3));
root.querySelectorAll('[data-suggestion]').forEach(button => button.addEventListener('click', () => {
    input.value = button.firstChild.textContent.trim();
    suggestions.classList.remove('is-open');
}));
form?.addEventListener('submit', event => { event.preventDefault(); status.textContent = `Searching for “${input.value || 'all resources'}”…`; });
