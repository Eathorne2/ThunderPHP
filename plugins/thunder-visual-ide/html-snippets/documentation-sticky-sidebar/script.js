const links = [...root.querySelectorAll('.docs-sidebar-layout__sidebar a')];
const sections = [...root.querySelectorAll('[data-doc-section]')];

if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
        const visibleEntry = entries
            .filter((entry) => entry.isIntersecting)
            .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

        if (!visibleEntry) {
            return;
        }

        links.forEach((link) => {
            link.classList.toggle('is-active', link.hash === `#${visibleEntry.target.id}`);
        });
    }, {
        rootMargin: '-15% 0px -65% 0px',
        threshold: [0.1, 0.5],
    });

    sections.forEach((section) => observer.observe(section));
}
