const form = root.querySelector('[data-rsvp-form]');
const feedback = root.querySelector('[data-feedback]');

form?.addEventListener('submit', event => {
    event.preventDefault();

    const data = new FormData(form);
    const name = data.get('name') || 'Guest';
    const attending = data.get('attendance') === 'yes';

    feedback.textContent = attending
        ? `Thank you, ${name}. We cannot wait to celebrate with you.`
        : `Thank you for letting us know, ${name}. You will be missed.`;
});
