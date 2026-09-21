const form = root.querySelector('form');
const topic = root.querySelector('[data-topic]');

root.querySelectorAll('[data-dept]').forEach(button => {
    button.addEventListener('click', () => {
        root.querySelectorAll('[data-dept]').forEach(item => item.classList.remove('is-active'));
        button.classList.add('is-active');
        if (topic) topic.value = button.dataset.dept === 'Sales' ? 'New project' : button.dataset.dept;
    });
});

form?.addEventListener('submit', event => {
    event.preventDefault();
    root.querySelector('[data-status]').textContent = 'Thank you. Your enquiry has been recorded.';
});
