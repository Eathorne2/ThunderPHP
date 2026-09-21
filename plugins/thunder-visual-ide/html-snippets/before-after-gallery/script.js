root.querySelectorAll('[data-compare]').forEach(compare => {
    const slider = compare.querySelector('[data-slider]');
    const after = compare.querySelector('[data-after]');
    const update = () => {
        after.style.width = `${slider.value}%`;
        const frameWidth = compare.querySelector('.ig-before-after__frame').clientWidth;
        after.querySelector('img').style.width = `${frameWidth}px`;
    };
    slider.addEventListener('input', update);
    window.addEventListener('resize', update);
    update();
});
