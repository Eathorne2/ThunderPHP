const conciergeChannels = Array.from(root.querySelectorAll('[data-channel]'));
conciergeChannels.forEach(button => button.addEventListener('click', () => conciergeChannels.forEach(item => item.classList.toggle('is-active', item === button))));
root.querySelector('form')?.addEventListener('submit', event => {
    event.preventDefault();
    const status = root.querySelector('[data-concierge-status]');
    if (status) status.textContent = 'Thank you. The concierge team will reply shortly.';
});
