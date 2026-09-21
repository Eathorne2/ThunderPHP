const main = root.querySelector('[data-product-main]');

root.querySelectorAll('[data-product-thumb]').forEach(button => {
    button.addEventListener('click', () => {
        root.querySelectorAll('[data-product-thumb]').forEach(item => {
            item.classList.toggle('is-active', item === button);
        });

        if (main) {
            main.src = button.dataset.src || '';
        }
    });
});

root.querySelectorAll('.product-detail__option button').forEach(button => {
    button.addEventListener('click', () => {
        root.querySelectorAll('.product-detail__option button').forEach(item => {
            item.classList.toggle('is-active', item === button);
        });
    });
});

const qty = root.querySelector('[data-product-qty]');
let count = 1;

root.querySelector('[data-product-minus]')?.addEventListener('click', () => {
    count = Math.max(1, count - 1);
    qty.textContent = String(count);
});

root.querySelector('[data-product-plus]')?.addEventListener('click', () => {
    count++;
    qty.textContent = String(count);
});
