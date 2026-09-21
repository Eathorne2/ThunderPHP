const frame = root.querySelector('[data-frame]');
const slider = root.querySelector('[data-slider]');
const after = root.querySelector('[data-after]');
const label = root.querySelector('[data-value]');
const update = () => {
    const value = Number(slider.value);
    after.style.width = `${value}%`;
    after.querySelector('img').style.width = `${frame.clientWidth}px`;
    label.textContent = `${value}% after`;
};
slider.addEventListener('input', update);
window.addEventListener('resize', update);
update();
