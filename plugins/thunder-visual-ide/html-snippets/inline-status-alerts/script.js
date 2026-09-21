root.querySelectorAll('[data-isa-dismiss]').forEach(button => button.addEventListener('click', () =>
    button.closest('.isa-alert')?.remove()));
