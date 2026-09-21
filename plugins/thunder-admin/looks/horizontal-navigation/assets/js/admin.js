(() => {
    'use strict';

    const shell = document.querySelector('[data-ta-shell]');
    if (!shell) return;

    const navRoot = shell.querySelector('[data-ta-sidebar]');
    const overlay = shell.querySelector('[data-ta-mobile-overlay]');
    const openButtons = shell.querySelectorAll('[data-ta-sidebar-open]');
    const closeButtons = shell.querySelectorAll('[data-ta-sidebar-close]');
    const desktopQuery = window.matchMedia('(min-width: 901px)');

    const submenuFor = (item) => item?.querySelector(':scope > [data-ta-submenu]') || null;

    const setMenuOpen = (item, open) => {
        const submenu = submenuFor(item);
        if (!item || !submenu) return;

        item.classList.toggle('ta-nav__item--open', open);
        submenu.hidden = !open;

        item.querySelectorAll(':scope > .ta-nav__row [data-ta-submenu-toggle]').forEach((toggle) => {
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        if (!open) {
            item.querySelectorAll('[data-ta-nav-item]').forEach((child) => {
                const childSubmenu = submenuFor(child);
                if (!childSubmenu) return;
                child.classList.remove('ta-nav__item--open');
                childSubmenu.hidden = true;
                child.querySelectorAll(':scope > .ta-nav__row [data-ta-submenu-toggle]').forEach((toggle) => {
                    toggle.setAttribute('aria-expanded', 'false');
                });
            });
        }
    };

    const closeRootMenus = (except = null) => {
        shell.querySelectorAll('.ta-nav__item[data-ta-level="0"]').forEach((item) => {
            if (item === except) return;
            setMenuOpen(item, false);
        });
    };

    const prepareNavigation = () => {
        if (desktopQuery.matches) {
            closeRootMenus();
        }
    };

    prepareNavigation();
    desktopQuery.addEventListener?.('change', prepareNavigation);

    const hoverCloseTimers = new WeakMap();

    shell.querySelectorAll('[data-ta-nav-item]').forEach((item) => {
        if (!submenuFor(item)) return;

        item.addEventListener('mouseenter', () => {
            if (!desktopQuery.matches) return;

            const timer = hoverCloseTimers.get(item);
            if (timer) window.clearTimeout(timer);

            if (item.getAttribute('data-ta-level') === '0') {
                closeRootMenus(item);
            }

            setMenuOpen(item, true);
        });

        item.addEventListener('mouseleave', () => {
            if (!desktopQuery.matches) return;

            const timer = window.setTimeout(() => {
                setMenuOpen(item, false);
                hoverCloseTimers.delete(item);
            }, 160);

            hoverCloseTimers.set(item, timer);
        });
    });

    const openMobileNavigation = () => {
        shell.classList.add('ta-shell--mobile-open');
        if (overlay) overlay.hidden = false;
        document.documentElement.classList.add('ta-html--locked');
    };

    const closeMobileNavigation = () => {
        shell.classList.remove('ta-shell--mobile-open');
        if (overlay) overlay.hidden = true;
        document.documentElement.classList.remove('ta-html--locked');
    };

    openButtons.forEach((button) => button.addEventListener('click', openMobileNavigation));
    closeButtons.forEach((button) => button.addEventListener('click', closeMobileNavigation));
    overlay?.addEventListener('click', closeMobileNavigation);

    shell.querySelectorAll('[data-ta-submenu-toggle]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            const item = button.closest('[data-ta-nav-item]');
            if (!item || !submenuFor(item)) return;

            const isRoot = item.getAttribute('data-ta-level') === '0';
            const open = !item.classList.contains('ta-nav__item--open');

            if (isRoot && desktopQuery.matches) {
                closeRootMenus(item);
            }

            setMenuOpen(item, open);
        });
    });

    navRoot?.querySelectorAll('a.ta-nav__link').forEach((link) => {
        link.addEventListener('click', () => {
            if (!desktopQuery.matches) closeMobileNavigation();
        });
    });

    const closeDropdowns = (except = null) => {
        shell.querySelectorAll('[data-ta-dropdown]').forEach((dropdown) => {
            if (dropdown === except) return;
            const toggle = dropdown.querySelector('[data-ta-dropdown-toggle]');
            const panel = dropdown.querySelector('[data-ta-dropdown-panel]');
            dropdown.classList.remove('ta-dropdown--open');
            toggle?.setAttribute('aria-expanded', 'false');
            if (panel) panel.hidden = true;
        });
    };

    shell.querySelectorAll('[data-ta-dropdown]').forEach((dropdown) => {
        const toggle = dropdown.querySelector('[data-ta-dropdown-toggle]');
        const panel = dropdown.querySelector('[data-ta-dropdown-panel]');

        toggle?.addEventListener('click', (event) => {
            event.stopPropagation();
            const open = !dropdown.classList.contains('ta-dropdown--open');
            closeDropdowns(dropdown);
            closeRootMenus();
            dropdown.classList.toggle('ta-dropdown--open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (panel) panel.hidden = !open;
        });

        panel?.addEventListener('click', (event) => event.stopPropagation());
    });

    document.addEventListener('click', (event) => {
        closeDropdowns();

        if (desktopQuery.matches && !event.target.closest('.ta-nav__item[data-ta-level="0"]')) {
            closeRootMenus();
        }
    });

    const searchInput = shell.querySelector('[data-ta-search-input]');
    document.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            searchInput?.focus();
        }

        if (event.key === 'Escape') {
            closeDropdowns();
            closeRootMenus();
            closeMobileNavigation();
            searchInput?.blur();
        }
    });

    const settingsForm = shell.querySelector('[data-ta-admin-settings]');

    if (settingsForm) {
        const lookInputs = settingsForm.querySelectorAll('[data-ta-look-input]');
        const lookOptions = settingsForm.querySelectorAll('[data-ta-look-option]');
        const paletteGroups = settingsForm.querySelectorAll('[data-ta-look-palette-group]');

        const showLookPalettes = (lookId) => {
            lookOptions.forEach((option) => {
                const input = option.querySelector('[data-ta-look-input]');
                option.classList.toggle('ta-look-option--selected', input?.value === lookId);
            });

            paletteGroups.forEach((group) => {
                group.hidden = group.getAttribute('data-ta-look-palette-group') !== lookId;
            });
        };

        lookInputs.forEach((input) => {
            input.addEventListener('change', () => showLookPalettes(input.value));
        });

        settingsForm.querySelectorAll('[data-ta-palette-input]').forEach((input) => {
            input.addEventListener('change', () => {
                const group = input.closest('[data-ta-look-palette-group]');
                if (!group) return;

                group.querySelectorAll('[data-ta-palette-option]').forEach((option) => {
                    const optionInput = option.querySelector('[data-ta-palette-input]');
                    option.classList.toggle('ta-palette-option--selected', optionInput === input);
                });

                const customPanel = group.querySelector('[data-ta-custom-palette]');
                if (customPanel) customPanel.hidden = input.value !== 'custom';
            });
        });

        settingsForm.querySelectorAll('.ta-color-field__picker').forEach((picker) => {
            picker.addEventListener('input', () => {
                const value = picker.closest('.ta-color-field__control')?.querySelector('.ta-color-field__value');
                if (value) value.textContent = picker.value.toUpperCase();
            });
        });
    }

    shell.querySelector('[data-ta-reset-sidebar]')?.addEventListener('click', () => {
        closeRootMenus();
        closeMobileNavigation();
    });
})();
