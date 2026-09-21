const choices = root.querySelectorAll('[data-style]');
const title = root.querySelector('[data-result-title]');
const copy = root.querySelector('[data-result-copy]');
choices.forEach(choice => choice.addEventListener('click', () => {
    choices.forEach(item => item.classList.toggle('is-selected', item === choice));
    title.textContent = choice.dataset.style;
    copy.textContent = `We will focus on ${choice.dataset.copy}.`;
}));
