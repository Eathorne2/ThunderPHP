const reviews = Array.from(root.querySelectorAll('.hgrs__stage article'));
const currentReview = root.querySelector('[data-review-current]');
let reviewIndex = 0;

function showReview(index) {
    reviewIndex = (index + reviews.length) % reviews.length;
    reviews.forEach((review, itemIndex) => review.hidden = itemIndex !== reviewIndex);
    if (currentReview) currentReview.textContent = String(reviewIndex + 1);
}

root.querySelector('[data-review-prev]')?.addEventListener('click', () => showReview(reviewIndex - 1));
root.querySelector('[data-review-next]')?.addEventListener('click', () => showReview(reviewIndex + 1));
