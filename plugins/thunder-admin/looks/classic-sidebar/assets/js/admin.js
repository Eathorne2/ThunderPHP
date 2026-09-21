(() => {
    'use strict';

    const shell = document.querySelector('[data-ta-shell]');
    if (!shell) return;

    const sidebar = shell.querySelector('[data-ta-sidebar]');
    const overlay = shell.querySelector('[data-ta-mobile-overlay]');
    const desktopToggle = shell.querySelector('[data-ta-sidebar-collapse]');
    const openButtons = shell.querySelectorAll('[data-ta-sidebar-open]');
    const closeButtons = shell.querySelectorAll('[data-ta-sidebar-close]');
    const storageKey = 'thunderAdmin.sidebarCollapsed';

    const setCollapsed = (collapsed) => {
        shell.classList.toggle('ta-shell--collapsed', collapsed);
        if (desktopToggle) {
            desktopToggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            desktopToggle.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
        }
    };

    const savedCollapsed = localStorage.getItem(storageKey) === '1';
    setCollapsed(savedCollapsed);

    desktopToggle?.addEventListener('click', () => {
        const collapsed = !shell.classList.contains('ta-shell--collapsed');
        setCollapsed(collapsed);
        localStorage.setItem(storageKey, collapsed ? '1' : '0');
    });

    const openMobileSidebar = () => {
        shell.classList.add('ta-shell--mobile-open');
        if (overlay) overlay.hidden = false;
        document.documentElement.classList.add('ta-html--locked');
    };

    const closeMobileSidebar = () => {
        shell.classList.remove('ta-shell--mobile-open');
        if (overlay) overlay.hidden = true;
        document.documentElement.classList.remove('ta-html--locked');
    };

    openButtons.forEach((button) => button.addEventListener('click', openMobileSidebar));
    closeButtons.forEach((button) => button.addEventListener('click', closeMobileSidebar));
    overlay?.addEventListener('click', closeMobileSidebar);

    shell.querySelectorAll('[data-ta-submenu-toggle]').forEach((button) => {
        button.addEventListener('click', (event) => {
            if (button.tagName === 'A') event.preventDefault();

            const item = button.closest('[data-ta-nav-item]');
            const submenu = item?.querySelector(':scope > [data-ta-submenu]');
            if (!item || !submenu) return;

            const open = !item.classList.contains('ta-nav__item--open');
            item.classList.toggle('ta-nav__item--open', open);
            submenu.hidden = !open;

            item.querySelectorAll(':scope > .ta-nav__row [data-ta-submenu-toggle]').forEach((toggle) => {
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
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
            dropdown.classList.toggle('ta-dropdown--open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (panel) panel.hidden = !open;
        });

        panel?.addEventListener('click', (event) => event.stopPropagation());
    });

    document.addEventListener('click', () => closeDropdowns());

    const searchInput = shell.querySelector('[data-ta-search-input]');
    document.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            searchInput?.focus();
        }

        if (event.key === 'Escape') {
            closeDropdowns();
            closeMobileSidebar();
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
        localStorage.removeItem(storageKey);
        setCollapsed(false);
    });
})();
