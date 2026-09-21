const steps = Array.from(root.querySelectorAll('[data-step]'));
const labels = Array.from(root.querySelectorAll('[data-step-label]'));
const backButton = root.querySelector('[data-back]');
const nextButton = root.querySelector('[data-next]');
let activeStep = 1;

function showStep(step) {
    activeStep = Math.max(1, Math.min(3, step));
    steps.forEach(panel => panel.hidden = Number(panel.dataset.step) !== activeStep);
    labels.forEach(label => label.classList.toggle('is-active', Number(label.dataset.stepLabel) === activeStep));

    if (backButton) backButton.hidden = activeStep === 1;
    if (nextButton) nextButton.textContent = activeStep === 3 ? 'Request booking' : 'Continue';
}

backButton?.addEventListener('click', () => showStep(activeStep - 1));
nextButton?.addEventListener('click', () => {
    if (activeStep < 3) {
        showStep(activeStep + 1);
    } else {
        nextButton.textContent = 'Request sent';
    }
});
