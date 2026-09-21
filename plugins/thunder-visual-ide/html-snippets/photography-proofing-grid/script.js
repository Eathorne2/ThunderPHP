const boxes = [...root.querySelectorAll('[data-select]')];
const count = root.querySelector('[data-count]');
const update = () => count.textContent = boxes.filter(box => box.checked).length;
boxes.forEach(box => box.addEventListener('change', update));
root.querySelector('[data-clear]')?.addEventListener('click', () => {
    boxes.forEach(box => box.checked = false);
    update();
});
