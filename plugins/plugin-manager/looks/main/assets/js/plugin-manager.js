document.documentElement.classList.add('pm-motion-ready');
document.addEventListener('DOMContentLoaded', function(){
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const validateButton = document.querySelector('#pm-validate-json');
    const configArea = document.querySelector('#pm-config-json');
    const configResult = document.querySelector('#pm-config-result');
    if(validateButton && configArea && configResult){
        validateButton.addEventListener('click', function(){
            try{
                JSON.parse(configArea.value);
                configResult.textContent = 'JSON is valid.';
                configResult.className = 'pm-config-result pm-good';
            }catch(error){
                configResult.textContent = 'Invalid JSON: ' + error.message;
                configResult.className = 'pm-config-result pm-bad';
            }
        });
    }

    const revealItems = Array.from(document.querySelectorAll('.pm-reveal'));
    if(reduceMotion || !('IntersectionObserver' in window)){
        revealItems.forEach(function(item){ item.classList.add('is-visible'); });
    }else{
        const revealObserver = new IntersectionObserver(function(entries, observer){
            entries.forEach(function(entry){
                if(!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, {threshold: 0.08, rootMargin: '0px 0px -24px'});
        revealItems.forEach(function(item){ revealObserver.observe(item); });
    }

    const interactiveSelector = 'a,button,input,select,textarea,label,form,details,summary,[data-pm-no-card-link]';
    document.querySelectorAll('[data-pm-href]').forEach(function(card){
        const visit = function(){
            const href = card.getAttribute('data-pm-href');
            if(href) window.location.assign(href);
        };

        card.addEventListener('click', function(event){
            if(event.target.closest(interactiveSelector)) return;
            visit();
        });

        card.addEventListener('keydown', function(event){
            if(event.target !== card || (event.key !== 'Enter' && event.key !== ' ')) return;
            event.preventDefault();
            visit();
        });
    });

    const searchInput = document.querySelector('#pm-plugin-search');
    const pluginCards = Array.from(document.querySelectorAll('.pm-plugin-card'));
    const filterButtons = Array.from(document.querySelectorAll('[data-pm-filter]'));
    const noResults = document.querySelector('#pm-no-results');
    let currentFilter = 'all';

    const filterPlugins = function(){
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        let visible = 0;

        pluginCards.forEach(function(card){
            const matchesSearch = query === '' || (card.dataset.pmSearch || '').includes(query);
            const matchesFilter = currentFilter === 'all'
                || (currentFilter === 'issues' && card.dataset.pmHealth === 'issues')
                || card.dataset.pmStatus === currentFilter;
            const show = matchesSearch && matchesFilter;
            card.hidden = !show;
            if(show) visible++;
        });

        if(noResults) noResults.hidden = visible !== 0;
    };

    if(searchInput){
        searchInput.addEventListener('input', filterPlugins);
        document.addEventListener('keydown', function(event){
            const target = event.target;
            const typing = target && (target.matches('input, textarea, select') || target.isContentEditable);
            if(event.key === '/' && !typing){
                event.preventDefault();
                searchInput.focus();
            }
            if(event.key === 'Escape' && document.activeElement === searchInput){
                searchInput.value = '';
                searchInput.blur();
                filterPlugins();
            }
        });
    }

    filterButtons.forEach(function(button){
        button.addEventListener('click', function(){
            currentFilter = button.dataset.pmFilter || 'all';
            filterButtons.forEach(function(item){ item.classList.toggle('is-active', item === button); });
            filterPlugins();
        });
    });

    const copyReadmeButton = document.querySelector('[data-pm-copy-readme]');
    const readmeSource = document.querySelector('#pm-readme-source');
    if(copyReadmeButton && readmeSource){
        copyReadmeButton.addEventListener('click', async function(){
            const original = copyReadmeButton.innerHTML;
            try{
                await navigator.clipboard.writeText(readmeSource.value);
                copyReadmeButton.innerHTML = '<i class="fa-solid fa-check"></i> Copied';
            }catch(error){
                readmeSource.hidden = false;
                readmeSource.select();
                document.execCommand('copy');
                readmeSource.hidden = true;
                copyReadmeButton.innerHTML = '<i class="fa-solid fa-check"></i> Copied';
            }
            window.setTimeout(function(){ copyReadmeButton.innerHTML = original; }, 1600);
        });
    }

    document.querySelectorAll('.pm-wrap form').forEach(function(form){
        form.addEventListener('submit', function(event){
            window.setTimeout(function(){
                if(event.defaultPrevented || form.matches(':invalid')) return;
                const button = form.querySelector('button[type="submit"], button:not([type])');
                if(!button || button.disabled) return;
                button.dataset.pmOriginalHtml = button.innerHTML;
                button.classList.add('is-loading');
                button.innerHTML = '<i class="fa-solid fa-circle-notch"></i> ' + (button.textContent.trim() || 'Working');
            }, 0);
        });
    });

    const anchorLinks = Array.from(document.querySelectorAll('.pm-anchor-nav a[href^="#"]'));
    if(anchorLinks.length && 'IntersectionObserver' in window){
        const sections = anchorLinks.map(function(link){ return document.querySelector(link.getAttribute('href')); }).filter(Boolean);
        const sectionObserver = new IntersectionObserver(function(entries){
            entries.forEach(function(entry){
                if(!entry.isIntersecting) return;
                anchorLinks.forEach(function(link){
                    link.classList.toggle('is-active', link.getAttribute('href') === '#' + entry.target.id);
                });
            });
        }, {rootMargin: '-15% 0px -70% 0px', threshold: 0});
        sections.forEach(function(section){ sectionObserver.observe(section); });
    }
});
