(() => {
    const search = document.querySelector('.docs-search input[type="search"]');
    document.addEventListener('keydown', event => {
        if (event.key === '/' && !event.ctrlKey && !event.metaKey && !event.altKey) {
            const target = event.target;
            if (target instanceof HTMLInputElement || target instanceof HTMLTextAreaElement || target?.isContentEditable) return;
            event.preventDefault();
            search?.focus();
        }
    });

    document.querySelectorAll('.docs-copy-code').forEach(button => {
        button.addEventListener('click', async () => {
            const code = button.closest('.docs-code-block')?.querySelector('code')?.textContent ?? '';
            try {
                await navigator.clipboard.writeText(code);
                const old = button.textContent;
                button.textContent = 'Copied';
                setTimeout(() => button.textContent = old, 1300);
            } catch (_) {
                button.textContent = 'Select & copy';
            }
        });
    });

    const navButton = document.querySelector('.docs-mobile-nav');
    const sidebar = document.querySelector('#docs-sidebar');
    navButton?.addEventListener('click', () => {
        const open = sidebar?.classList.toggle('is-open') ?? false;
        navButton.setAttribute('aria-expanded', String(open));
    });

    const tocLinks = [...document.querySelectorAll('.docs-toc a')];
    const sections = tocLinks.map(link => document.querySelector(link.getAttribute('href'))).filter(Boolean);
    if ('IntersectionObserver' in window && sections.length) {
        const observer = new IntersectionObserver(entries => {
            entries.filter(entry => entry.isIntersecting).forEach(entry => {
                tocLinks.forEach(link => link.classList.toggle('is-active', link.getAttribute('href') === '#' + entry.target.id));
            });
        }, {rootMargin: '-95px 0px -70% 0px'});
        sections.forEach(section => observer.observe(section));
    }
})();
