const essay = root.querySelector('[data-photo-essay]');
const stage = root.querySelector('.photo-essay__stage');
const image = root.querySelector('[data-photo-essay-image]');
const count = root.querySelector('[data-photo-essay-count]');
const kicker = root.querySelector('[data-photo-essay-kicker]');
const title = root.querySelector('[data-photo-essay-title]');
const caption = root.querySelector('[data-photo-essay-caption]');
const progress = root.querySelector('[data-photo-essay-progress]');

const chapters = [
    {
        image: 'https://picsum.photos/id/1036/1600/980',
        alt: 'Mountain road stretching through a dramatic landscape',
        kicker: 'Chapter One · Departure',
        title: 'The road begins before the map.',
        caption: 'Morning light, western route. Photograph by Lena P.'
    },
    {
        image: 'https://picsum.photos/id/1015/1600/980',
        alt: 'River cutting through a mountain valley',
        kicker: 'Chapter Two · Distance',
        title: 'Every horizon edits the story behind it.',
        caption: 'High valley, late afternoon. Photograph by Lena P.'
    },
    {
        image: 'https://picsum.photos/id/1019/1600/980',
        alt: 'Cloudy forest landscape viewed from above',
        kicker: 'Chapter Three · Return',
        title: 'Home is the detail you notice twice.',
        caption: 'Forest edge, evening. Photograph by Lena P.'
    }
];

let chapterIndex = 0;

function showChapter(nextIndex) {
    chapterIndex = (nextIndex + chapters.length) % chapters.length;
    const chapter = chapters[chapterIndex];

    stage?.classList.add('is-changing');

    window.setTimeout(() => {
        if (image) {
            image.src = chapter.image;
            image.alt = chapter.alt;
        }

        if (count) count.textContent = `${String(chapterIndex + 1).padStart(2, '0')} / ${String(chapters.length).padStart(2, '0')}`;
        if (kicker) kicker.textContent = chapter.kicker;
        if (title) title.textContent = chapter.title;
        if (caption) caption.textContent = chapter.caption;
        if (progress) progress.style.width = `${((chapterIndex + 1) / chapters.length) * 100}%`;

        stage?.classList.remove('is-changing');
    }, 180);
}

essay?.querySelector('[data-photo-essay-prev]')?.addEventListener('click', () => {
    showChapter(chapterIndex - 1);
});

essay?.querySelector('[data-photo-essay-next]')?.addEventListener('click', () => {
    showChapter(chapterIndex + 1);
});
