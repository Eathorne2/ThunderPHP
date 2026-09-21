const toggle = root.querySelector('[data-saas-toggle]');
const nav = root.querySelector('[data-saas-nav]');
toggle?.addEventListener('click', () => nav.classList.toggle('is-open'));
