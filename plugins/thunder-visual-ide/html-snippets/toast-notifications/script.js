const stack = root.querySelector('[data-toast-stack]');
const messages = {
    success: ['Saved successfully', 'Your latest changes are ready.'],
    warning: ['Check this item', 'Some optional fields are still empty.'],
    error: ['Action failed', 'Please review the form and try again.']
};

root.querySelectorAll('[data-toast]').forEach(button => {
    button.addEventListener('click', () => {
        if (!stack) {
            return;
        }

        const type = button.dataset.toast || 'success';
        const content = messages[type] || messages.success;
        const toast = document.createElement('article');

        toast.className = `toast-message toast-message--${type}`;
        toast.innerHTML = [
            '<span>●</span>',
            '<div>',
            `<strong>${content[0]}</strong>`,
            `<small>${content[1]}</small>`,
            '</div>',
            '<button type="button" aria-label="Dismiss">×</button>'
        ].join('');

        toast.querySelector('button')?.addEventListener('click', () => {
            toast.remove();
        });

        stack.prepend(toast);

        window.setTimeout(() => {
            toast.remove();
        }, 4500);
    });
});
