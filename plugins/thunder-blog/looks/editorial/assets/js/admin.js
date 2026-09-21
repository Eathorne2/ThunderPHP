(() => {
    'use strict';

    document.querySelectorAll('[data-tb-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.getAttribute('data-tb-confirm') || 'Continue?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    const categoryForm = document.getElementById('tb-category-form');
    if (categoryForm) {
        const title = document.getElementById('tb-category-form-title');
        const id = document.getElementById('tb-category-id');
        const name = document.getElementById('tb-category-name');
        const slug = document.getElementById('tb-category-slug');
        const description = document.getElementById('tb-category-description');
        const reset = document.getElementById('tb-category-reset');

        const resetForm = () => {
            categoryForm.reset();
            id.value = '0';
            title.textContent = 'Add category';
        };

        document.querySelectorAll('.tb-category-edit').forEach((button) => {
            button.addEventListener('click', () => {
                id.value = button.dataset.id || '0';
                name.value = button.dataset.name || '';
                slug.value = button.dataset.slug || '';
                description.value = button.dataset.description || '';
                title.textContent = 'Edit category';
                name.focus();
                categoryForm.scrollIntoView({behavior: 'smooth', block: 'center'});
            });
        });

        reset?.addEventListener('click', resetForm);
    }

    document.querySelectorAll('.tb-admin__look-card').forEach((card) => {
        const radio = card.querySelector('.tb-admin__look-radio');
        radio?.addEventListener('change', () => {
            document.querySelectorAll('.tb-admin__look-card').forEach((item) => item.classList.remove('tb-admin__look-card--active'));
            card.classList.add('tb-admin__look-card--active');
        });
    });

    document.querySelectorAll('.tb-admin__color-field').forEach((field) => {
        const picker = field.querySelector('.tb-admin__color-input');
        const text = field.querySelector('[data-tb-color-text]');
        if (!picker || !text) return;
        picker.addEventListener('input', () => { text.value = picker.value; });
        text.addEventListener('change', () => {
            if (/^#[0-9a-f]{6}$/i.test(text.value.trim())) picker.value = text.value.trim();
        });
    });

    document.querySelector('[data-tb-reset-colors]')?.addEventListener('click', () => {
        document.querySelectorAll('.tb-admin__color-field').forEach((field) => {
            const picker = field.querySelector('.tb-admin__color-input');
            const text = field.querySelector('[data-tb-color-text]');
            if (!picker) return;
            picker.value = picker.dataset.default || '#000000';
            if (text) text.value = picker.value;
        });
        const paletteSelect = document.getElementById('tb-settings-palette');
        if (paletteSelect) paletteSelect.value = 'custom';
    });
})();

// Author schema mapping and shortcode generator extensions.
(() => {
    'use strict';

    const mapping = document.querySelector('[data-tb-author-mapping]');
    if (mapping) {
        const table = mapping.querySelector('[data-tb-user-table]');
        const columns = [...mapping.querySelectorAll('[data-tb-user-column]')];
        const api = mapping.dataset.schemaApi || '';
        const fillColumns = (items) => {
            columns.forEach((select) => {
                const selected = select.dataset.selected || select.value || '';
                const optional = select.dataset.optional === '1';
                select.replaceChildren();
                if (optional) select.add(new Option('None / use authentication filter', ''));
                items.forEach((column) => select.add(new Option(`${column.name}${column.type ? ` · ${column.type}` : ''}`, column.name)));
                const available = [...select.options].some((option) => option.value === selected);
                if (available) select.value = selected;
                else if (!optional && select.options.length) select.selectedIndex = 0;
                select.dataset.selected = select.value;
            });
        };
        table?.addEventListener('change', async () => {
            columns.forEach((select) => { select.disabled = true; });
            try {
                const url = new URL(api, window.location.href);
                url.searchParams.set('table', table.value);
                const response = await fetch(url, {headers: {'Accept': 'application/json'}});
                const payload = await response.json();
                if (!response.ok || !payload.ok) throw new Error(payload.message || 'Unable to load columns.');
                columns.forEach((select) => { select.dataset.selected = ''; });
                fillColumns(payload.data.columns || []);
            } catch (error) {
                window.alert(error.message);
            } finally {
                columns.forEach((select) => { select.disabled = false; });
            }
        });
        columns.forEach((select) => select.addEventListener('change', () => { select.dataset.selected = select.value; }));
    }

    const generator = document.querySelector('[data-tb-shortcode-generator]');
    if (generator) {
        const controls = {
            layout: generator.querySelector('[data-tb-sc-layout]'),
            order: generator.querySelector('[data-tb-sc-order]'),
            limit: generator.querySelector('[data-tb-sc-limit]'),
            category: generator.querySelector('[data-tb-sc-category]'),
            tag: generator.querySelector('[data-tb-sc-tag]'),
            output: generator.querySelector('[data-tb-sc-output]'),
            copy: generator.querySelector('[data-tb-sc-copy]'),
            status: generator.querySelector('[data-tb-sc-status]'),
        };
        const clean = (value) => String(value || '').trim().replace(/["<>\[\]]/g, '');
        const update = () => {
            const attrs = [
                `layout="${clean(controls.layout.value)}"`,
                `order="${clean(controls.order.value)}"`,
                `limit="${Math.max(1, Math.min(50, Number(controls.limit.value) || 6))}"`,
            ];
            if (controls.category.value) attrs.push(`category="${clean(controls.category.value)}"`);
            if (controls.tag.value.trim()) attrs.push(`tag="${clean(controls.tag.value)}"`);
            controls.output.value = `[thunder-blog.posts ${attrs.join(' ')}]`;
        };
        [controls.layout, controls.order, controls.limit, controls.category, controls.tag].forEach((input) => {
            input?.addEventListener('input', update);
            input?.addEventListener('change', update);
        });
        controls.copy?.addEventListener('click', async () => {
            controls.output.select();
            try {
                await navigator.clipboard.writeText(controls.output.value);
                controls.status.textContent = 'Copied.';
            } catch {
                document.execCommand('copy');
                controls.status.textContent = 'Copied.';
            }
            window.setTimeout(() => { controls.status.textContent = ''; }, 1800);
        });
        update();
    }
})();
