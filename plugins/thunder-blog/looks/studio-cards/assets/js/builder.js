(() => {
    'use strict';

    const builder = document.getElementById('tb-builder');
    if (!builder) return;

    const elements = {
        blockList: builder.querySelector('[data-tb-block-list]'),
        category: builder.querySelector('[data-tb-block-category]'),
        search: builder.querySelector('[data-tb-block-search]'),
        pagination: builder.querySelector('[data-tb-library-pagination]'),
        resultCount: builder.querySelector('[data-tb-result-count]'),
        categoryList: builder.querySelector('[data-tb-category-list]'),
        browser: builder.querySelector('[data-tb-block-browser]'),
        browserPreview: builder.querySelector('[data-tb-browser-preview]'),
        browserPreviewShell: builder.querySelector('[data-tb-browser-preview-shell]'),
        browserPreviewTitle: builder.querySelector('[data-tb-browser-preview-title]'),
        browserPreviewCategory: builder.querySelector('[data-tb-browser-preview-category]'),
        browserPreviewDescription: builder.querySelector('[data-tb-browser-preview-description]'),
        addSelected: builder.querySelector('[data-tb-add-selected]'),
        openLibrary: builder.querySelector('[data-tb-open-library]'),
        closeLibrary: builder.querySelector('[data-tb-close-library]'),
        imageBrowser: builder.querySelector('[data-tb-image-browser]'),
        imageList: builder.querySelector('[data-tb-image-list]'),
        imageCategory: builder.querySelector('[data-tb-image-category]'),
        imageSearch: builder.querySelector('[data-tb-image-search]'),
        imagePagination: builder.querySelector('[data-tb-image-pagination]'),
        imageResultCount: builder.querySelector('[data-tb-image-result-count]'),
        imageCategoryList: builder.querySelector('[data-tb-image-category-list]'),
        imagePreview: builder.querySelector('[data-tb-image-preview]'),
        imagePreviewEmpty: builder.querySelector('[data-tb-image-preview-empty]'),
        imagePreviewName: builder.querySelector('[data-tb-image-preview-name]'),
        imagePreviewCategory: builder.querySelector('[data-tb-image-preview-category]'),
        imagePreviewSize: builder.querySelector('[data-tb-image-preview-size]'),
        useImage: builder.querySelector('[data-tb-use-image]'),
        closeImageBrowser: builder.querySelector('[data-tb-close-image-browser]'),
        featuredImage: builder.querySelector('[data-tb-featured-image]'),
        featuredImageBrowse: builder.querySelector('[data-tb-featured-image-browse]'),
        paletteImportButton: builder.querySelector('[data-tb-palette-import-button]'),
        paletteImport: builder.querySelector('[data-tb-palette-import]'),
        paletteExport: builder.querySelector('[data-tb-palette-export]'),
        paletteFileStatus: builder.querySelector('[data-tb-palette-file-status]'),
        canvas: builder.querySelector('[data-tb-canvas]'),
        frameShell: builder.querySelector('[data-tb-frame-shell]'),
        inspectorEmpty: builder.querySelector('[data-tb-inspector-empty]'),
        inspectorContent: builder.querySelector('[data-tb-inspector-content]'),
        paletteSelect: builder.querySelector('[data-tb-palette]'),
        saveState: builder.querySelector('[data-tb-save-state]'),
        blocksInput: document.getElementById('tb-blocks-json'),
        paletteInput: document.getElementById('tb-palette-json'),
        form: document.querySelector('[data-tb-editor-form]'),
        title: document.querySelector('[data-tb-post-title]'),
        slug: document.querySelector('[data-tb-post-slug]'),
    };

    const parseJson = (value, fallback) => {
        try {
            const parsed = JSON.parse(value || '');
            return parsed ?? fallback;
        } catch (_) {
            return fallback;
        }
    };

    const palettePresets = parseJson(builder.dataset.palettes, {});
    const initialBlocks = parseJson(builder.dataset.initialBlocks, []);
    const initialPalette = parseJson(builder.dataset.initialPalette, {});
    const menuLibrary = parseJson(builder.dataset.menus, []);
    const menuMap = new Map((Array.isArray(menuLibrary) ? menuLibrary : []).map((menu) => [String(menu.id), menu]));
    const manifestCache = new Map();
    const builderContext = ['post', 'page', 'header', 'footer'].includes(builder.dataset.context) ? builder.dataset.context : 'post';
    const contentLabel = builder.dataset.contentLabel || 'Post';

    const libraryState = {page: 1, pages: 1, category: '', search: '', items: [], categories: [], categoryCounts: {}, availableTotal: 0, selectedSlug: '', loaded: false};
    const imageState = {page: 1, pages: 1, category: '', search: '', items: [], categories: [], selectedUrl: '', loaded: false, target: null};
    let blocks = Array.isArray(initialBlocks) ? initialBlocks.filter((block) => block && block.slug) : [];
    let palette = {...initialPalette};
    const paletteTokenNames = Object.keys(palettePresets.indigo?.colors || initialPalette || {});
    let selectedId = null;
    let selectedElement = null;
    let selectedElementComputedFontSize = '';
    let renderSequence = 0;
    let searchTimer = 0;
    let imageSearchTimer = 0;
    let renderTimer = 0;
    let librarySelectionSequence = 0;
    let browserPreviewRenderToken = 0;
    let slugWasEdited = Boolean(elements.slug?.value.trim());

    const uid = () => `tb-${Date.now().toString(36)}-${Math.random().toString(36).slice(2, 8)}`;
    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const globalTokens = new Map([
        ['{{app_name}}', escapeHtml(builder.dataset.appName || 'Website')],
        ['{{app_description}}', escapeHtml(builder.dataset.appDescription || '')],
        ['{{root}}', escapeHtml(builder.dataset.root || '')],
        ['{{app_logo}}', escapeHtml(builder.dataset.appLogo || '')],
        ['{{year}}', escapeHtml(builder.dataset.year || String(new Date().getFullYear()))],
    ]);

    const sanitizeUrl = (value) => {
        const url = String(value ?? '').trim();
        if (!url) return '';
        if (/^(https?:\/\/|\/|#|mailto:|tel:|\{\{root\}\}(?:\/|$)|data:image\/(png|jpeg|gif|webp);base64,)/i.test(url)) return url;
        return '';
    };

    const sanitizeRichText = (value) => {
        const parser = new DOMParser();
        const documentValue = parser.parseFromString(`<div>${String(value ?? '')}</div>`, 'text/html');
        const root = documentValue.body.firstElementChild;
        if (!root) return '';
        const allowed = new Set(['P', 'BR', 'STRONG', 'B', 'EM', 'I', 'U', 'A', 'UL', 'OL', 'LI', 'SPAN']);
        [...root.querySelectorAll('*')].forEach((node) => {
            if (!allowed.has(node.tagName)) {
                node.replaceWith(...node.childNodes);
                return;
            }
            [...node.attributes].forEach((attribute) => {
                if (node.tagName === 'A' && attribute.name === 'href') {
                    node.setAttribute('href', sanitizeUrl(attribute.value));
                    return;
                }
                node.removeAttribute(attribute.name);
            });
        });
        return root.innerHTML;
    };

    const sanitizeColor = (value, fallback = '#000000') => {
        const color = String(value ?? '').trim();
        return /^#[0-9a-f]{3,8}$/i.test(color) || /^rgba?\([0-9.% ,]+\)$/i.test(color) || /^hsla?\([0-9.% ,]+\)$/i.test(color)
            ? color
            : fallback;
    };

    const paletteStyle = () => Object.entries(palette)
        .map(([key, value]) => `--tb-color-${key}:${sanitizeColor(value, '#000000')}`)
        .join(';');

    const fieldValue = (field, value) => {
        const type = field.type || 'text';
        const current = value ?? field.default ?? '';
        if (type === 'menu') return String(Math.max(0, Number.parseInt(current || '0', 10) || 0));
        if (type === 'url' || type === 'image') return escapeHtml(sanitizeUrl(current));
        if (type === 'richtext') return sanitizeRichText(current);
        if (type === 'html') return String(current ?? '').replaceAll('\0', '');
        if (type === 'shortcode') return escapeHtml(String(current ?? '').trim().replace(/[<>]/g, ''));
        if (type === 'textarea') return escapeHtml(current).replaceAll('\n', '<br>');
        if (type === 'color') return sanitizeColor(current, field.default || '#000000');
        if (type === 'number' || type === 'range') {
            let number = Number(current);
            if (!Number.isFinite(number)) number = Number(field.default || 0);
            if (field.min !== undefined) number = Math.max(Number(field.min), number);
            if (field.max !== undefined) number = Math.min(Number(field.max), number);
            return String(number);
        }
        if (type === 'select') {
            const options = Object.keys(field.options || {});
            const selected = options.includes(String(current)) ? String(current) : String(field.default || options[0] || '');
            return escapeHtml(selected);
        }
        return escapeHtml(current);
    };

    const deepClone = (value) => JSON.parse(JSON.stringify(value));

    const manifestSettings = (manifest) => manifest?.settings && typeof manifest.settings === 'object'
        ? manifest.settings
        : {defaults: {}, schema: []};

    const manifestElements = (manifest) => Array.isArray(manifest?.elements) ? manifest.elements : [];

    const normalizePaletteToken = (value) => {
        const token = String(value || '').trim().toLowerCase();
        return paletteTokenNames.includes(token) ? token : '';
    };

    const normalizeFontSize = (value) => {
        const raw = String(value ?? '').trim().toLowerCase().replace(/px$/, '');
        if (raw === '') return '';
        const number = Number.parseFloat(raw);
        if (!Number.isFinite(number)) return '';
        const clamped = Math.min(240, Math.max(6, number));
        const rounded = Math.round(clamped * 10) / 10;
        return `${rounded}px`;
    };

    const appearanceDefinition = (definition) => definition?.appearance && typeof definition.appearance === 'object'
        ? definition.appearance
        : {};

    const defaultElementAppearance = (definition, appearance = {}) => {
        const config = appearanceDefinition(definition);
        const defaults = config.defaults && typeof config.defaults === 'object' ? config.defaults : {};
        const result = {};
        if (config.color) result.color = normalizePaletteToken(appearance?.color ?? defaults.color ?? '');
        if (config.background_color) result.background_color = normalizePaletteToken(appearance?.background_color ?? defaults.background_color ?? '');
        result.font_size = normalizeFontSize(appearance?.font_size ?? defaults.font_size ?? '');
        return result;
    };

    const cleanCloneGroup = (value) => String(value || '').replace(/[^a-z0-9_-]/gi, '');

    const defaultElementValues = (definition) => {
        const defaults = definition?.defaults && typeof definition.defaults === 'object' ? definition.defaults : {};
        const source = Array.isArray(definition?.default_items) && definition.default_items.length
            ? definition.default_items
            : [defaults];
        const maximum = Number.isFinite(Number(definition?.max)) ? Math.max(1, Number(definition.max)) : source.length;
        const items = source.slice(0, maximum).map((values) => ({
            id: uid(),
            values: {...deepClone(defaults), ...(values && typeof values === 'object' ? deepClone(values) : {})},
            appearance: defaultElementAppearance(definition),
            clone_group: '',
        }));
        const minimum = Number.isFinite(Number(definition?.min)) ? Math.max(0, Number(definition.min)) : 1;
        while (items.length < minimum) items.push({id: uid(), values: deepClone(defaults), appearance: defaultElementAppearance(definition), clone_group: ''});
        return definition?.repeatable ? items : items.slice(0, 1);
    };

    const ensureBlockStructure = (manifest, block) => {
        block.settings = block.settings && typeof block.settings === 'object' ? block.settings : {};
        const settingsDefaults = manifestSettings(manifest).defaults || {};
        Object.entries(settingsDefaults).forEach(([key, value]) => {
            if (!(key in block.settings)) block.settings[key] = deepClone(value);
        });
        block.elements = block.elements && typeof block.elements === 'object' ? block.elements : {};
        manifestElements(manifest).forEach((definition) => {
            const key = String(definition.key || '');
            if (!key) return;
            if (!Array.isArray(block.elements[key])) block.elements[key] = defaultElementValues(definition);
            const defaults = definition.defaults && typeof definition.defaults === 'object' ? definition.defaults : {};
            block.elements[key] = block.elements[key]
                .filter((item) => item && typeof item === 'object')
                .map((item) => ({
                    id: item.id || uid(),
                    values: {...deepClone(defaults), ...(item.values && typeof item.values === 'object' ? item.values : {})},
                    appearance: defaultElementAppearance(definition, item.appearance),
                    clone_group: cleanCloneGroup(item.clone_group),
                }));
            const minimum = Number.isFinite(Number(definition.min)) ? Math.max(0, Number(definition.min)) : 1;
            const maximum = Number.isFinite(Number(definition.max)) ? Math.max(minimum, Number(definition.max)) : Infinity;
            while (block.elements[key].length < minimum) block.elements[key].push({id: uid(), values: deepClone(defaults), appearance: defaultElementAppearance(definition), clone_group: ''});
            if (!definition.repeatable) block.elements[key] = block.elements[key].slice(0, 1);
            else if (Number.isFinite(maximum)) block.elements[key] = block.elements[key].slice(0, maximum);
        });
        return block;
    };

    const createBlockFromManifest = (manifest, id = uid()) => ensureBlockStructure(manifest, {
        id,
        slug: manifest.slug,
        settings: deepClone(manifestSettings(manifest).defaults || {}),
        elements: {},
    });

    const injectElementAttributes = (markup, definition, item) => {
        const appearance = defaultElementAppearance(definition, item.appearance);
        const attributes = [
            `data-tb-element-group="${escapeHtml(definition.key)}"`,
            `data-tb-element-id="${escapeHtml(item.id)}"`,
            `data-tb-element-label="${escapeHtml(definition.label || definition.key)}"`,
        ];
        const styles = [];
        if (appearance.color) {
            attributes.push(`data-tb-element-color="${escapeHtml(appearance.color)}"`);
            styles.push(`--tb-element-color:var(--tb-color-${appearance.color})`);
        }
        if (appearance.background_color) {
            attributes.push(`data-tb-element-background="${escapeHtml(appearance.background_color)}"`);
            styles.push(`--tb-element-background:var(--tb-color-${appearance.background_color})`);
        }
        if (appearance.font_size) {
            attributes.push(`data-tb-element-font-size="${escapeHtml(appearance.font_size)}"`);
            styles.push(`--tb-element-font-size:${escapeHtml(appearance.font_size)}`);
        }
        if (item.clone_group) attributes.push(`data-tb-clone-group="${escapeHtml(item.clone_group)}"`);
        if (styles.length) attributes.push(`style="${escapeHtml(styles.join(';'))}"`);
        return String(markup || '').replace(/^(\s*<[a-zA-Z][\w:-]*)(?=[\s>])/, `$1 ${attributes.join(' ')}`);
    };

    const replaceTokens = (template, replacements) => {
        let output = String(template || '');
        replacements.forEach((value, token) => { output = output.split(token).join(value); });
        return output;
    };

    const renderMenuSource = (source, menuId, instanceId) => {
        let menu = menuMap.get(String(menuId || '0'));
        const isPreviewPlaceholder = !menu || !Array.isArray(menu.items) || !menu.items.length;
        if (isPreviewPlaceholder) {
            menu = {
                id: 0,
                name: 'Preview menu',
                items: [
                    {id: 'preview-home', title: 'Home', url: '#', target: '_self', children: []},
                    {id: 'preview-about', title: 'About', url: '#', target: '_self', children: []},
                    {id: 'preview-blog', title: 'Blog', url: '#', target: '_self', children: []},
                    {id: 'preview-contact', title: 'Contact', url: '#', target: '_self', children: []},
                ],
            };
        }
        const renderItems = (items, level = 0) => items.map((item) => {
            const children = renderItems(Array.isArray(item.children) ? item.children : [], level + 1);
            const template = children ? String(source.parent || source.item || '') : String(source.item || '');
            return replaceTokens(template, new Map([
                ['{{title}}', escapeHtml(item.title || '')],
                ['{{url}}', escapeHtml(sanitizeUrl(item.url || '#') || '#')],
                ['{{target}}', item.target === '_blank' ? '_blank' : '_self'],
                ['{{children}}', children],
                ['{{item_id}}', String(item.id || '')],
                ['{{level}}', String(level)],
                ['{{instance_id}}', escapeHtml(instanceId)],
            ]));
        }).join('');
        return replaceTokens(String(source.container || '{{items}}'), new Map([
            ['{{items}}', renderItems(menu.items)],
            ['{{menu_name}}', escapeHtml(menu.name || 'Menu')],
            ['{{instance_id}}', escapeHtml(instanceId)],
        ]));
    };

    const renderManifest = (manifest, block) => {
        ensureBlockStructure(manifest, block);
        const settings = manifestSettings(manifest);
        const settingValues = {...(settings.defaults || {}), ...(block.settings || {})};
        const replacements = new Map(globalTokens);
        (settings.schema || []).forEach((field) => replacements.set(`{{${field.key}}}`, fieldValue(field, settingValues[field.key])));
        replacements.set('{{instance_id}}', escapeHtml(block.id));

        manifestElements(manifest).forEach((definition) => {
            const items = block.elements?.[definition.key] || [];
            const renderedItems = items.map((item) => {
                const values = {...(definition.defaults || {}), ...(item.values || {})};
                const local = new Map();
                (definition.schema || []).forEach((field) => local.set(`{{${field.key}}}`, fieldValue(field, values[field.key])));
                local.set('{{element_id}}', escapeHtml(item.id));
                const markup = replaceTokens(definition.template || '', local);
                return injectElementAttributes(markup, definition, item);
            }).join('');
            replacements.set(`{{elements:${definition.key}}}`, renderedItems);
        });

        (Array.isArray(manifest.menu_sources) ? manifest.menu_sources : []).forEach((source) => {
            const token = String(source?.token || 'menu').replace(/[^a-z0-9_-]/gi, '') || 'menu';
            const setting = String(source?.setting || 'menu_id');
            replacements.set(`{{menu:${token}}}`, renderMenuSource(source, settingValues[setting], block.id));
        });

        let html = replaceTokens(manifest.html, replacements);
        const renderMode = String(manifest.render_mode || '');
        if (renderMode === 'html' || renderMode === 'shortcode') {
            const definition = manifestElements(manifest)[0] || {};
            const item = block.elements?.[definition.key]?.[0] || {};
            const key = renderMode === 'html' ? 'code' : 'shortcode';
            const raw = String(item.values?.[key] || '');
            html = renderMode === 'html'
                ? injectElementAttributes(`<div class="tb-raw-html-editor">${raw || '<div class="tb-raw-html-editor__empty">Add HTML in the inspector</div>'}</div>`, definition, item)
                : injectElementAttributes(`<div class="tb-shortcode-placeholder"><strong>Dynamic shortcode</strong><code>${escapeHtml(raw.replace(/[<>]/g, '').trim())}</code><span>Rendered on the public page</span></div>`, definition, item);
        }
        return {
            html: replaceTokens(html, globalTokens),
            css: replaceTokens(manifest.css, replacements),
            js: String(manifest.js || '').split('{{instance_id}}').join(block.id),
        };
    };

    const syncHidden = () => {
        if (elements.blocksInput) elements.blocksInput.value = JSON.stringify(blocks);
        if (elements.paletteInput) elements.paletteInput.value = JSON.stringify(palette);
        if (elements.saveState) elements.saveState.textContent = `${blocks.length} block${blocks.length === 1 ? '' : 's'} in ${contentLabel.toLowerCase()}`;
    };

    const fetchManifest = async (slug) => {
        if (manifestCache.has(slug)) return manifestCache.get(slug);
        const url = builder.dataset.apiBlock.replace('__SLUG__', encodeURIComponent(slug));
        const response = await fetch(url, {headers: {'Accept': 'application/json'}});
        if (!response.ok) throw new Error(`Unable to load block: ${slug}`);
        const payload = await response.json();
        if (!payload.ok || !payload.data) throw new Error(payload.message || `Unable to load block: ${slug}`);
        manifestCache.set(slug, payload.data);
        return payload.data;
    };

    const ensureManifests = async () => {
        const slugs = [...new Set(blocks.map((block) => block.slug))];
        await Promise.all(slugs.map(fetchManifest));
    };

    const selectedBlock = () => blocks.find((block) => block.id === selectedId) || null;

    const selectedElementContext = () => {
        const block = selectedBlock();
        if (!block || !selectedElement) return null;
        const manifest = manifestCache.get(block.slug);
        if (!manifest) return null;
        ensureBlockStructure(manifest, block);
        const definition = manifestElements(manifest).find((candidate) => candidate.key === selectedElement.group);
        const items = definition ? block.elements?.[definition.key] || [] : [];
        const index = items.findIndex((item) => item.id === selectedElement.itemId);
        if (!definition || index < 0) return null;
        return {block, manifest, definition, items, item: items[index], index};
    };

    const findElementTarget = (target) => {
        if (!target || target.type !== 'element') return null;
        const block = blocks.find((candidate) => candidate.id === target.blockId);
        const manifest = block ? manifestCache.get(block.slug) : null;
        if (!block || !manifest) return null;
        ensureBlockStructure(manifest, block);
        const definition = manifestElements(manifest).find((candidate) => candidate.key === target.group);
        const items = definition ? block.elements?.[target.group] || [] : [];
        const item = items.find((candidate) => candidate.id === target.itemId);
        return definition && item ? {block, manifest, definition, items, item} : null;
    };

    const cloneTargets = (sourceItem) => {
        const cloneGroup = cleanCloneGroup(sourceItem?.clone_group);
        if (!cloneGroup) return sourceItem ? [sourceItem] : [];
        const targets = [];
        blocks.forEach((block) => Object.values(block.elements || {}).forEach((items) => {
            if (!Array.isArray(items)) return;
            items.forEach((item) => {
                if (cleanCloneGroup(item.clone_group) === cloneGroup) targets.push(item);
            });
        }));
        return targets.length ? targets : [sourceItem];
    };

    const synchronizeCloneGroups = () => {
        const counts = new Map();
        blocks.forEach((block) => Object.values(block.elements || {}).forEach((items) => {
            if (!Array.isArray(items)) return;
            items.forEach((item) => {
                const cloneGroup = cleanCloneGroup(item.clone_group);
                if (cloneGroup) counts.set(cloneGroup, (counts.get(cloneGroup) || 0) + 1);
            });
        }));
        const canonical = new Map();
        blocks.forEach((block) => Object.values(block.elements || {}).forEach((items) => {
            if (!Array.isArray(items)) return;
            items.forEach((item) => {
                const cloneGroup = cleanCloneGroup(item.clone_group);
                if (!cloneGroup || (counts.get(cloneGroup) || 0) < 2) {
                    item.clone_group = '';
                    return;
                }
                item.clone_group = cloneGroup;
                if (!canonical.has(cloneGroup)) {
                    canonical.set(cloneGroup, {values:deepClone(item.values || {}), appearance:deepClone(item.appearance || {})});
                    return;
                }
                const source = canonical.get(cloneGroup);
                item.values = deepClone(source.values);
                item.appearance = deepClone(source.appearance);
            });
        }));
    };

    const updateElementValue = (target, value) => {
        const context = findElementTarget(target);
        if (!context) return false;
        cloneTargets(context.item).forEach((item) => {
            item.values ||= {};
            item.values[target.key] = value;
        });
        return true;
    };

    const updateElementAppearance = (target, key, value) => {
        const context = findElementTarget(target);
        if (!context) return false;
        const normalized = key === 'font_size' ? normalizeFontSize(value) : normalizePaletteToken(value);
        cloneTargets(context.item).forEach((item) => {
            item.appearance ||= {};
            item.appearance[key] = normalized;
        });
        return true;
    };

    const cloneCount = (item) => item?.clone_group ? cloneTargets(item).length : 1;

    const canvasDocument = () => {
        const styles = new Map();
        const scripts = new Map();
        const editorMap = {};
        const html = blocks.map((block) => {
            const manifest = manifestCache.get(block.slug);
            if (!manifest) return '';
            ensureBlockStructure(manifest, block);
            const rendered = renderManifest(manifest, block);
            styles.set(block.slug, rendered.css);
            if (rendered.js.trim()) scripts.set(block.slug, rendered.js);
            editorMap[block.id] = {};
            manifestElements(manifest).forEach((definition) => {
                editorMap[block.id][definition.key] = {
                    label: definition.label || definition.key,
                    fields: (definition.schema || [])
                        .filter((field) => field?.selector && ['text', 'textarea', 'richtext', 'image'].includes(field.type || 'text'))
                        .map((field) => ({key: field.key, type: field.type || 'text', selector: field.selector})),
                };
            });
            const selected = block.id === selectedId ? ' tb-canvas-block--selected' : '';
            return `<section class="tb-canvas-block${selected}" data-tb-canvas-block="${escapeHtml(block.id)}"><span class="tb-canvas-block__label">${escapeHtml(manifest.name || block.slug)}</span>${rendered.html}</section>`;
        }).join('\n');

        const editorMapJson = JSON.stringify(editorMap).replaceAll('</script', '<\\/script');
        const selectionJson = JSON.stringify({blockId: selectedId, element: selectedElement}).replaceAll('</script', '<\\/script');
        const runtime = `
            (() => {
                const documentNode = document.querySelector('.tb-canvas-document');
                const editorMap = ${editorMapJson};
                let activeSelection = ${selectionJson};
                const sendHeight = () => {
                    const height = Math.max(
                        document.documentElement?.scrollHeight || 0,
                        document.body?.scrollHeight || 0,
                        documentNode?.scrollHeight || 0,
                        documentNode?.getBoundingClientRect().height || 0
                    );
                    parent.postMessage({type:'tb-canvas-height',height:Math.ceil(height) + 4}, '*');
                };
                const insertPlainText = (text) => {
                    if (document.queryCommandSupported?.('insertText')) {
                        document.execCommand('insertText', false, text);
                        return;
                    }
                    const selection = window.getSelection();
                    if (!selection?.rangeCount) return;
                    selection.deleteFromDocument();
                    const node = document.createTextNode(text);
                    selection.getRangeAt(0).insertNode(node);
                    selection.collapseToEnd();
                };
                const selectContents = (element) => {
                    const range = document.createRange();
                    range.selectNodeContents(element);
                    const selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);
                };
                const fieldTarget = (root, selector) => selector === ':scope' ? root : root.querySelector(selector);
                const applySelection = (selection) => {
                    activeSelection = selection || {blockId:null,element:null};
                    document.querySelectorAll('[data-tb-canvas-block]').forEach((candidate) => {
                        candidate.classList.toggle('tb-canvas-block--selected', candidate.getAttribute('data-tb-canvas-block') === activeSelection.blockId);
                    });
                    document.querySelectorAll('[data-tb-element-id]').forEach((candidate) => {
                        const block = candidate.closest('[data-tb-canvas-block]');
                        const selected = block?.getAttribute('data-tb-canvas-block') === activeSelection.blockId
                            && candidate.getAttribute('data-tb-element-group') === activeSelection.element?.group
                            && candidate.getAttribute('data-tb-element-id') === activeSelection.element?.itemId;
                        candidate.classList.toggle('tb-canvas-element--selected', Boolean(selected));
                    });
                };
                const selectElement = (blockId, group, itemId) => {
                    const selection = {blockId,element:{group,itemId}};
                    applySelection(selection);
                    let fontSize = '';
                    try {
                        const selectedNode = [...document.querySelectorAll('[data-tb-element-id]')].find((candidate) => {
                            const owner = candidate.closest('[data-tb-canvas-block]');
                            return owner?.getAttribute('data-tb-canvas-block') === blockId
                                && candidate.getAttribute('data-tb-element-group') === group
                                && candidate.getAttribute('data-tb-element-id') === itemId;
                        });
                        const computed = selectedNode ? window.getComputedStyle(selectedNode).fontSize : '';
                        const numeric = Number.parseFloat(computed);
                        if (Number.isFinite(numeric)) fontSize = String(Math.round(numeric * 10) / 10);
                    } catch (error) {
                        fontSize = '';
                    }
                    parent.postMessage({type:'tb-select-element',id:blockId,group,itemId,fontSize}, '*');
                };
                const startTextEdit = (element, blockId, group, itemId, field, event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    if (!element || element.isContentEditable) return;
                    selectElement(blockId, group, itemId);
                    const original = field.type === 'richtext' ? element.innerHTML : element.innerText;
                    element.contentEditable = 'true';
                    element.classList.add('tb-canvas-inline-editing');
                    element.focus();
                    selectContents(element);
                    let cancelled = false;
                    const onPaste = (pasteEvent) => {
                        pasteEvent.preventDefault();
                        let text = pasteEvent.clipboardData?.getData('text/plain') || '';
                        if (field.type !== 'richtext') text = text.replace(/\\s*\\n+\\s*/g, ' ');
                        insertPlainText(text);
                    };
                    const onKeyDown = (keyEvent) => {
                        keyEvent.stopPropagation();
                        if (keyEvent.key === 'Escape') {
                            cancelled = true;
                            if (field.type === 'richtext') element.innerHTML = original;
                            else element.innerText = original;
                            element.blur();
                        } else if (keyEvent.key === 'Enter' && field.type === 'text') {
                            keyEvent.preventDefault();
                            element.blur();
                        }
                    };
                    const onBlur = () => {
                        element.removeEventListener('paste', onPaste);
                        element.removeEventListener('keydown', onKeyDown);
                        element.contentEditable = 'false';
                        element.classList.remove('tb-canvas-inline-editing');
                        if (!cancelled) {
                            const value = field.type === 'richtext' ? element.innerHTML : element.innerText.replace(/\\u00a0/g, ' ').trim();
                            parent.postMessage({type:'tb-inline-edit',id:blockId,group,itemId,key:field.key,fieldType:field.type,value}, '*');
                        }
                        sendHeight();
                    };
                    element.addEventListener('paste', onPaste);
                    element.addEventListener('keydown', onKeyDown);
                    element.addEventListener('blur', onBlur, {once:true});
                };

                document.querySelectorAll('[data-tb-canvas-block]').forEach((block) => {
                    const blockId = block.getAttribute('data-tb-canvas-block');
                    block.addEventListener('click', (event) => {
                        if (event.target.closest('[contenteditable="true"]') || event.target.closest('[data-tb-element-id]')) return;
                        event.preventDefault();
                        event.stopPropagation();
                        applySelection({blockId,element:null});
                        parent.postMessage({type:'tb-select-block',id:blockId}, '*');
                    });
                    block.querySelectorAll('[data-tb-element-id]').forEach((elementRoot) => {
                        const group = elementRoot.getAttribute('data-tb-element-group');
                        const itemId = elementRoot.getAttribute('data-tb-element-id');
                        elementRoot.addEventListener('click', (event) => {
                            if (event.target.closest('[contenteditable="true"]')) return;
                            event.preventDefault();
                            event.stopPropagation();
                            selectElement(blockId, group, itemId);
                        });
                        const definition = editorMap[blockId]?.[group];
                        (definition?.fields || []).forEach((field) => {
                            const target = fieldTarget(elementRoot, field.selector);
                            if (!target) return;
                            if (field.type === 'image') {
                                target.dataset.tbImageEditable = '1';
                                target.title = 'Double-click to choose another image';
                                target.addEventListener('dblclick', (event) => {
                                    event.preventDefault();
                                    event.stopPropagation();
                                    selectElement(blockId, group, itemId);
                                    parent.postMessage({type:'tb-open-image-browser',id:blockId,group,itemId,key:field.key}, '*');
                                });
                            } else {
                                target.dataset.tbInlineEditable = '1';
                                target.title = 'Double-click to edit text';
                                target.addEventListener('dblclick', (event) => startTextEdit(target, blockId, group, itemId, field, event));
                            }
                        });
                    });
                });
                document.addEventListener('click', (event) => {
                    if (event.target.closest('[data-tb-canvas-block]')) return;
                    applySelection({blockId:null,element:null});
                    parent.postMessage({type:'tb-clear-selection'}, '*');
                });
                window.addEventListener('message', (event) => {
                    if (event.data?.type !== 'tb-active-selection') return;
                    applySelection(event.data.selection);
                });
                if ('ResizeObserver' in window && documentNode) new ResizeObserver(sendHeight).observe(documentNode);
                if ('MutationObserver' in window && documentNode) new MutationObserver(sendHeight).observe(documentNode, {subtree:true,childList:true,attributes:true,characterData:true});
                document.querySelectorAll('img').forEach((image) => image.addEventListener('load', sendHeight));
                window.addEventListener('load', sendHeight);
                applySelection(activeSelection);
                setTimeout(sendHeight, 80);
                setTimeout(sendHeight, 350);
            })();`;
        const contentScripts = [...scripts.values()].join('\n').replaceAll('</script', '<\\/script');

        return `<!doctype html>
<html class="tb-canvas-root">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
html,body{overflow:hidden}.tb-canvas-root{background:var(--tb-color-background);color:var(--tb-color-text);font-family:Arial,sans-serif}.tb-canvas-body{margin:0;padding:24px;box-sizing:border-box;background:var(--tb-color-background)}.tb-canvas-document{min-height:500px}.tb-canvas-empty{min-height:500px;border:2px dashed var(--tb-color-border);border-radius:18px;display:flex;align-items:center;justify-content:center;flex-direction:column;color:var(--tb-color-muted);text-align:center}.tb-canvas-empty__title{margin:0 0 7px;font-size:22px}.tb-canvas-empty__text{max-width:330px;margin:0;font-size:14px;line-height:1.6}.tb-canvas-block{position:relative;border-radius:10px;outline:2px solid transparent;outline-offset:5px;cursor:pointer}.tb-canvas-block+.tb-canvas-block{margin-top:24px}.tb-canvas-block:hover{outline-color:color-mix(in srgb,var(--tb-color-primary) 35%,transparent)}.tb-canvas-block--selected{outline-color:color-mix(in srgb,var(--tb-color-primary) 70%,transparent)}.tb-canvas-block__label{position:absolute;z-index:30;left:4px;top:-17px;padding:3px 7px;border-radius:5px;background:var(--tb-color-primary);color:#fff;font-size:9px;font-weight:800;line-height:1;opacity:0;pointer-events:none}.tb-canvas-block:hover .tb-canvas-block__label,.tb-canvas-block--selected .tb-canvas-block__label{opacity:1}[data-tb-element-id]{position:relative}[data-tb-element-color]{color:var(--tb-element-color)!important}[data-tb-element-color] *{color:inherit!important}[data-tb-element-background]{background-color:var(--tb-element-background)!important;background-image:none!important}[data-tb-element-font-size],[data-tb-element-font-size] *{font-size:var(--tb-element-font-size)!important}.tb-canvas-element--selected{outline:3px solid var(--tb-color-primary)!important;outline-offset:3px!important}[data-tb-inline-editable]{cursor:text}[data-tb-inline-editable]:hover{outline:1px dashed color-mix(in srgb,var(--tb-color-primary) 65%,transparent);outline-offset:3px}[data-tb-image-editable]{cursor:zoom-in}[data-tb-image-editable]:hover{outline:3px solid color-mix(in srgb,var(--tb-color-primary) 70%,transparent);outline-offset:-3px}.tb-canvas-inline-editing{cursor:text!important;outline:2px solid var(--tb-color-primary)!important;outline-offset:4px!important;background:color-mix(in srgb,var(--tb-color-primary) 6%,var(--tb-color-surface))}
${[...styles.values()].join('\n')}
</style>
</head>
<body class="tb-canvas-body" style="${escapeHtml(paletteStyle())}">
<div class="tb-canvas-document">${html || '<div class="tb-canvas-empty"><h2 class="tb-canvas-empty__title">Start with a block</h2><p class="tb-canvas-empty__text">Use the Browse blocks button to open the full-screen library, preview a block, and add it here.</p></div>'}</div>
<script>${contentScripts}\n${runtime.replaceAll('</script', '<\\/script')}<\/script>
</body>
</html>`;
    };

    const renderCanvas = async () => {
        const sequence = ++renderSequence;
        syncHidden();
        try {
            await ensureManifests();
            blocks.forEach((block) => {
                const manifest = manifestCache.get(block.slug);
                if (manifest) ensureBlockStructure(manifest, block);
            });
            synchronizeCloneGroups();
            syncHidden();
            if (sequence !== renderSequence) return;
            elements.canvas.style.height = '560px';
            elements.canvas.srcdoc = canvasDocument();
            renderInspector();
        } catch (error) {
            elements.canvas.srcdoc = `<!doctype html><html><body><p>${escapeHtml(error.message)}</p></body></html>`;
        }
    };

    const scheduleCanvas = () => {
        syncHidden();
        window.clearTimeout(renderTimer);
        renderTimer = window.setTimeout(renderCanvas, 80);
    };

    const createField = (field, values, onUpdate, imageTarget = null) => {
        const wrapper = document.createElement(field.type === 'image' ? 'div' : 'label');
        wrapper.className = 'tb-builder__field';
        const label = document.createElement('span');
        label.className = 'tb-builder__label';
        label.textContent = field.label || field.key;
        wrapper.append(label);

        const current = values?.[field.key] ?? field.default ?? '';
        let input;
        if (field.type === 'textarea' || field.type === 'richtext' || field.type === 'html' || field.type === 'shortcode') {
            input = document.createElement('textarea');
            input.className = 'tb-builder__textarea';
            input.rows = field.rows || 5;
            input.value = current;
        } else if (field.type === 'menu') {
            input = document.createElement('select');
            input.className = 'tb-builder__select';
            input.append(new Option('Select a saved menu', '0'));
            [...menuMap.values()].forEach((menu) => input.append(new Option(menu.name || `Menu ${menu.id}`, String(menu.id))));
            input.value = menuMap.has(String(current)) ? String(current) : '0';
        } else if (field.type === 'select') {
            input = document.createElement('select');
            input.className = 'tb-builder__select';
            Object.entries(field.options || {}).forEach(([value, text]) => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = text;
                option.selected = String(current) === String(value);
                input.append(option);
            });
        } else if (field.type === 'range') {
            const row = document.createElement('div');
            row.className = 'tb-builder__range-row';
            input = document.createElement('input');
            input.className = 'tb-builder__range';
            input.type = 'range';
            input.min = field.min ?? 0;
            input.max = field.max ?? 100;
            input.step = field.step ?? 1;
            input.value = current;
            const valueInput = document.createElement('input');
            valueInput.className = 'tb-builder__range-value';
            valueInput.type = 'number';
            valueInput.min = input.min;
            valueInput.max = input.max;
            valueInput.step = input.step;
            valueInput.value = current;
            input.addEventListener('input', () => { valueInput.value = input.value; });
            valueInput.addEventListener('input', () => {
                input.value = valueInput.value;
                input.dispatchEvent(new Event('input', {bubbles: true}));
            });
            row.append(input, valueInput);
            wrapper.append(row);
        } else {
            input = document.createElement('input');
            input.className = 'tb-builder__input';
            input.type = field.type === 'image' ? 'url' : (field.type || 'text');
            if (field.type === 'color') input.type = 'color';
            if (field.type === 'number') {
                input.min = field.min ?? '';
                input.max = field.max ?? '';
                input.step = field.step ?? '1';
            }
            input.value = current;
            if (field.placeholder) input.placeholder = field.placeholder;
        }

        const update = () => {
            if (imageTarget?.type === 'element') updateElementValue(imageTarget, input.value);
            else values[field.key] = input.value;
            onUpdate?.(input.value);
            scheduleCanvas();
        };
        input.addEventListener('input', update);
        input.addEventListener('change', update);

        if (field.type === 'image') {
            const row = document.createElement('div');
            row.className = 'tb-builder__input-action-row';
            const browse = document.createElement('button');
            browse.type = 'button';
            browse.className = 'tb-builder__browse-image-button';
            browse.innerHTML = '<i class="fa-regular fa-images"></i><span>Browse</span>';
            browse.addEventListener('click', () => openImageBrowser(imageTarget));
            row.append(input, browse);
            wrapper.append(row);
        } else if (field.type !== 'range') {
            wrapper.append(input);
        }
        if (field.help) {
            const hint = document.createElement('span');
            hint.className = 'tb-builder__hint';
            hint.textContent = field.help;
            wrapper.append(hint);
        }
        return wrapper;
    };

    const moveBlock = (direction) => {
        const index = blocks.findIndex((block) => block.id === selectedId);
        const target = index + direction;
        if (index < 0 || target < 0 || target >= blocks.length) return;
        [blocks[index], blocks[target]] = [blocks[target], blocks[index]];
        renderCanvas();
    };

    const duplicateBlock = () => {
        const index = blocks.findIndex((block) => block.id === selectedId);
        if (index < 0) return;
        const copy = deepClone(blocks[index]);
        copy.id = uid();
        Object.values(copy.elements || {}).forEach((items) => items.forEach((item) => { item.id = uid(); item.clone_group = ''; }));
        blocks.splice(index + 1, 0, copy);
        selectedId = copy.id;
        selectedElement = null;
        renderCanvas();
    };

    const deleteBlock = () => {
        const index = blocks.findIndex((block) => block.id === selectedId);
        if (index < 0) return;
        blocks.splice(index, 1);
        selectedId = blocks[index]?.id || blocks[index - 1]?.id || null;
        selectedElement = null;
        renderCanvas();
    };

    const moveElement = (direction) => {
        const context = selectedElementContext();
        if (!context || !context.definition.controls?.move) return;
        const target = context.index + direction;
        if (target < 0 || target >= context.items.length) return;
        [context.items[context.index], context.items[target]] = [context.items[target], context.items[context.index]];
        renderCanvas();
    };

    const duplicateElement = () => {
        const context = selectedElementContext();
        if (!context || !context.definition.controls?.duplicate) return;
        const maximum = Number.isFinite(Number(context.definition.max)) ? Number(context.definition.max) : Infinity;
        if (context.items.length >= maximum) return;
        const copy = deepClone(context.item);
        copy.id = uid();
        copy.clone_group = '';
        context.items.splice(context.index + 1, 0, copy);
        selectedElement = {group: context.definition.key, itemId: copy.id};
        renderCanvas();
    };

    const cloneElement = () => {
        const context = selectedElementContext();
        if (!context || !context.definition.controls?.clone) return;
        const maximum = Number.isFinite(Number(context.definition.max)) ? Number(context.definition.max) : Infinity;
        if (context.items.length >= maximum) return;
        const cloneGroup = context.item.clone_group || `clone-${uid()}`;
        context.item.clone_group = cloneGroup;
        const copy = deepClone(context.item);
        copy.id = uid();
        copy.clone_group = cloneGroup;
        context.items.splice(context.index + 1, 0, copy);
        selectedElement = {group: context.definition.key, itemId: copy.id};
        renderCanvas();
    };

    const detachElementClone = () => {
        const context = selectedElementContext();
        if (!context?.item?.clone_group) return;
        context.item.clone_group = '';
        context.item.values = deepClone(context.item.values || {});
        context.item.appearance = deepClone(context.item.appearance || {});
        renderCanvas();
    };

    const deleteElement = () => {
        const context = selectedElementContext();
        if (!context || !context.definition.controls?.delete) return;
        const minimum = Number.isFinite(Number(context.definition.min)) ? Number(context.definition.min) : 0;
        if (context.items.length <= minimum) return;
        context.items.splice(context.index, 1);
        const next = context.items[context.index] || context.items[context.index - 1] || null;
        selectedElement = next ? {group: context.definition.key, itemId: next.id} : null;
        renderCanvas();
    };

    const actionButton = (icon, title, handler, options = {}) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = `tb-builder__action-button${options.danger ? ' tb-builder__action-button--danger' : ''}`;
        button.title = title;
        button.innerHTML = `<i class="${icon}"></i>`;
        button.disabled = Boolean(options.disabled);
        button.addEventListener('click', handler);
        return button;
    };

    const inspectorSection = (titleText, description = '') => {
        const section = document.createElement('section');
        section.className = 'tb-builder__inspector-section';
        const heading = document.createElement('div');
        heading.className = 'tb-builder__inspector-section-head';
        const title = document.createElement('h4');
        title.className = 'tb-builder__inspector-section-title';
        title.textContent = titleText;
        heading.append(title);
        if (description) {
            const text = document.createElement('p');
            text.className = 'tb-builder__inspector-section-text';
            text.textContent = description;
            heading.append(text);
        }
        const fields = document.createElement('div');
        fields.className = 'tb-builder__fields';
        section.append(heading, fields);
        return {section, heading, fields};
    };

    const createPaletteTokenField = (labelText, appearanceKey, context) => {
        const wrapper = document.createElement('label');
        wrapper.className = 'tb-builder__field';
        const label = document.createElement('span');
        label.className = 'tb-builder__label';
        label.textContent = labelText;
        const select = document.createElement('select');
        select.className = 'tb-builder__select';
        select.append(new Option(appearanceKey === 'color' ? 'Inherit block colour' : 'No background colour', ''));
        paletteTokenNames.forEach((token) => {
            const name = token.replaceAll('-', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
            select.append(new Option(name, token));
        });
        select.value = normalizePaletteToken(context.item.appearance?.[appearanceKey]);
        select.addEventListener('change', () => {
            updateElementAppearance({type:'element',blockId:context.block.id,group:context.definition.key,itemId:context.item.id}, appearanceKey, select.value);
            scheduleCanvas();
        });
        wrapper.append(label, select);
        return wrapper;
    };

    const computedElementFontSize = (context) => {
        try {
            const documentValue = elements.canvas?.contentDocument;
            if (!documentValue) return '';
            const node = [...documentValue.querySelectorAll('[data-tb-element-id]')]
                .find((candidate) => candidate.dataset.tbElementId === String(context?.item?.id || ''));
            if (!node) return '';
            const raw = documentValue.defaultView?.getComputedStyle(node)?.fontSize || '';
            const number = Number.parseFloat(raw);
            return Number.isFinite(number) ? String(Math.round(number * 10) / 10) : '';
        } catch (error) {
            return '';
        }
    };

    const createFontSizeField = (context) => {
        const wrapper = document.createElement('label');
        wrapper.className = 'tb-builder__field';
        const label = document.createElement('span');
        label.className = 'tb-builder__label';
        label.textContent = 'Font size (px)';
        const input = document.createElement('input');
        input.className = 'tb-builder__input';
        input.type = 'number';
        input.min = '6';
        input.max = '240';
        input.step = '1';
        const stored = normalizeFontSize(context.item.appearance?.font_size);
        const directComputed = computedElementFontSize(context);
        const computed = directComputed || selectedElementComputedFontSize;
        input.value = stored ? stored.replace(/px$/, '') : computed;
        input.defaultValue = input.value;
        input.placeholder = computed || '16';
        const hint = document.createElement('span');
        hint.className = 'tb-builder__hint';
        hint.textContent = stored
            ? 'Element override. Clear the value to restore the block’s CSS font size.'
            : `Computed from the canvas${computed ? `: ${computed}px` : ''}. Editing creates an element override.`;
        const update = () => {
            updateElementAppearance(
                {type:'element',blockId:context.block.id,group:context.definition.key,itemId:context.item.id},
                'font_size',
                input.value
            );
            scheduleCanvas();
        };
        input.addEventListener('input', update);
        input.addEventListener('change', update);
        wrapper.append(label, input, hint);
        return wrapper;
    };

    const renderInspector = () => {
        const block = selectedBlock();
        if (!block) {
            elements.inspectorEmpty.style.display = '';
            elements.inspectorContent.classList.remove('tb-builder__inspector-content--active');
            elements.inspectorContent.replaceChildren();
            return;
        }
        const manifest = manifestCache.get(block.slug);
        if (!manifest) return;
        ensureBlockStructure(manifest, block);
        elements.inspectorEmpty.style.display = 'none';
        elements.inspectorContent.classList.add('tb-builder__inspector-content--active');
        elements.inspectorContent.replaceChildren();

        const blockIndex = blocks.findIndex((candidate) => candidate.id === block.id);
        const head = document.createElement('div');
        head.className = 'tb-builder__inspector-head';
        const titleWrap = document.createElement('div');
        const title = document.createElement('h3');
        title.className = 'tb-builder__inspector-title';
        title.textContent = manifest.name || block.slug;
        const type = document.createElement('div');
        type.className = 'tb-builder__inspector-type';
        type.textContent = manifest.category || 'Block';
        titleWrap.append(title, type);
        const actions = document.createElement('div');
        actions.className = 'tb-builder__block-actions';
        actions.append(
            actionButton('fa-solid fa-arrow-up', 'Move block up', () => moveBlock(-1), {disabled:blockIndex <= 0}),
            actionButton('fa-solid fa-arrow-down', 'Move block down', () => moveBlock(1), {disabled:blockIndex < 0 || blockIndex >= blocks.length - 1}),
            actionButton('fa-regular fa-copy', 'Duplicate block', duplicateBlock),
            actionButton('fa-solid fa-trash', 'Delete block', deleteBlock, {danger:true}),
        );
        head.append(titleWrap, actions);
        elements.inspectorContent.append(head);

        const settings = manifestSettings(manifest);
        if (Array.isArray(settings.schema) && settings.schema.length) {
            const blockSection = inspectorSection('Block settings', 'These controls affect the whole block.');
            settings.schema.forEach((field) => blockSection.fields.append(createField(field, block.settings, null, null)));
            elements.inspectorContent.append(blockSection.section);
        }

        const context = selectedElementContext();
        if (context) {
            const position = context.definition.repeatable ? ` ${context.index + 1} of ${context.items.length}` : '';
            const linkedCount = cloneCount(context.item);
            const elementDescription = context.item.clone_group
                ? `Linked clone: edits affect all ${linkedCount} connected elements.`
                : 'These controls affect only the selected element.';
            const elementSection = inspectorSection(`${context.definition.label || context.definition.key}${position}`, elementDescription);
            const controls = context.definition.controls || {};
            if (controls.move || controls.duplicate || controls.clone || controls.delete || context.item.clone_group) {
                const toolbar = document.createElement('div');
                toolbar.className = 'tb-builder__element-actions';
                const minimum = Number.isFinite(Number(context.definition.min)) ? Number(context.definition.min) : 0;
                const maximum = Number.isFinite(Number(context.definition.max)) ? Number(context.definition.max) : Infinity;
                if (controls.move) toolbar.append(
                    actionButton('fa-solid fa-arrow-up', 'Move element up', () => moveElement(-1), {disabled:context.index <= 0}),
                    actionButton('fa-solid fa-arrow-down', 'Move element down', () => moveElement(1), {disabled:context.index >= context.items.length - 1}),
                );
                if (controls.duplicate) toolbar.append(actionButton('fa-regular fa-copy', 'Duplicate independently', duplicateElement, {disabled:context.items.length >= maximum}));
                if (controls.clone) toolbar.append(actionButton('fa-solid fa-link', 'Clone as linked element', cloneElement, {disabled:context.items.length >= maximum}));
                if (context.item.clone_group) toolbar.append(actionButton('fa-solid fa-link-slash', 'Detach this clone', detachElementClone));
                if (controls.delete) toolbar.append(actionButton('fa-solid fa-trash', 'Delete element', deleteElement, {danger:true,disabled:context.items.length <= minimum}));
                elementSection.heading.append(toolbar);
            }
            (context.definition.schema || []).forEach((field) => {
                const target = {type:'element', blockId:block.id, group:context.definition.key, itemId:context.item.id, key:field.key};
                elementSection.fields.append(createField(field, context.item.values, null, target));
            });
            const appearance = appearanceDefinition(context.definition);
            const appearanceTitle = document.createElement('div');
            appearanceTitle.className = 'tb-builder__field-divider';
            appearanceTitle.textContent = 'Appearance';
            elementSection.fields.append(appearanceTitle);
            if (appearance.color) elementSection.fields.append(createPaletteTokenField('Text colour', 'color', context));
            if (appearance.background_color) elementSection.fields.append(createPaletteTokenField('Background colour', 'background_color', context));
            elementSection.fields.append(createFontSizeField(context));
            elements.inspectorContent.append(elementSection.section);
        } else {
            const hint = document.createElement('div');
            hint.className = 'tb-builder__element-hint';
            hint.innerHTML = '<i class="fa-solid fa-arrow-pointer"></i><strong>Select an element</strong><span>Click text, an image, a button, or another registered element in the canvas to edit only that element.</span>';
            elements.inspectorContent.append(hint);
        }
    };

    const previewDocument = (manifest, renderToken) => {
        const block = createBlockFromManifest(manifest, 'preview');
        const rendered = renderManifest(manifest, block);
        const script = rendered.js.replaceAll('</script', '<\/script');
        const heightRuntime = `(() => {
            const sendHeight = () => {
                const body = document.body;
                const root = document.documentElement;
                const height = Math.max(
                    root?.scrollHeight || 0,
                    body?.scrollHeight || 0,
                    body?.getBoundingClientRect().height || 0
                );
                parent.postMessage({type:'tb-browser-preview-height',height:Math.ceil(height) + 4,token:${renderToken}}, '*');
            };
            if ('ResizeObserver' in window && document.body) new ResizeObserver(sendHeight).observe(document.body);
            if ('MutationObserver' in window && document.body) new MutationObserver(sendHeight).observe(document.body,{subtree:true,childList:true,attributes:true,characterData:true});
            document.querySelectorAll('img').forEach((image) => image.addEventListener('load', sendHeight));
            window.addEventListener('load', sendHeight);
            setTimeout(sendHeight, 60);
            setTimeout(sendHeight, 300);
        })();`;
        return `<!doctype html><html class="tb-preview-root"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><style>html,body{overflow:hidden}[data-tb-element-color]{color:var(--tb-element-color)!important}[data-tb-element-color] *{color:inherit!important}[data-tb-element-background]{background-color:var(--tb-element-background)!important;background-image:none!important}[data-tb-element-font-size],[data-tb-element-font-size] *{font-size:var(--tb-element-font-size)!important}.tb-preview-root{background:var(--tb-color-background);color:var(--tb-color-text);font-family:Arial,sans-serif}.tb-preview-body{margin:0;padding:10px;box-sizing:border-box}${rendered.css}</style></head><body class="tb-preview-body" style="${escapeHtml(paletteStyle())}">${rendered.html}<script>${script}\n${heightRuntime.replaceAll('</script', '<\/script')}<\/script></body></html>`;
    };

    const selectedLibraryManifest = () => {
        if (!libraryState.selectedSlug) return null;
        return manifestCache.get(libraryState.selectedSlug) || null;
    };

    const emptyPreviewDocument = (message = 'Select a block to preview it.') => `<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><style>html,body{height:100%;margin:0}body{display:flex;align-items:center;justify-content:center;padding:30px;box-sizing:border-box;background:${sanitizeColor(palette.background || '#ffffff','#ffffff')};color:${sanitizeColor(palette.muted || '#64748b','#64748b')};font:14px/1.6 Arial,sans-serif;text-align:center}</style></head><body>${escapeHtml(message)}</body></html>`;

    const setBrowserPreviewBreakpoint = (value) => {
        builder.querySelectorAll('[data-tb-browser-breakpoint]').forEach((button) => {
            button.classList.toggle('tb-block-browser__breakpoint--active', button.dataset.tbBrowserBreakpoint === value);
        });
        if (elements.browserPreviewShell) {
            elements.browserPreviewShell.style.width = value === 'full' ? '100%' : `${value}px`;
        }
    };

    const renderBrowserPreview = (manifest = selectedLibraryManifest()) => {
        if (!elements.browserPreview) return;
        const renderToken = ++browserPreviewRenderToken;
        if (!manifest) {
            elements.browserPreview.style.height = '220px';
            if (elements.browserPreviewShell) elements.browserPreviewShell.style.height = '220px';
            elements.browserPreview.srcdoc = emptyPreviewDocument();
            elements.browserPreviewTitle.textContent = 'Block preview';
            elements.browserPreviewCategory.textContent = 'Select a block';
            elements.browserPreviewDescription.textContent = 'Choose a block from the results to inspect its real HTML output.';
            elements.addSelected.disabled = true;
            return;
        }

        elements.browserPreview.style.height = '220px';
        if (elements.browserPreviewShell) elements.browserPreviewShell.style.height = '220px';
        elements.browserPreview.srcdoc = previewDocument(manifest, renderToken);
        elements.browserPreviewTitle.textContent = manifest.name || manifest.slug;
        elements.browserPreviewCategory.textContent = manifest.category || 'Block';
        elements.browserPreviewDescription.textContent = manifest.description || 'Preview this block before adding it to the post.';
        elements.addSelected.disabled = false;
    };

    const selectLibraryBlock = async (item) => {
        const slug = item?.slug || item?.manifest?.slug;
        if (!slug) return;
        const sequence = ++librarySelectionSequence;
        browserPreviewRenderToken += 1; // invalidate late height reports from the previous selection immediately
        libraryState.selectedSlug = slug;
        elements.blockList.querySelectorAll('[data-tb-library-slug]').forEach((card) => {
            card.classList.toggle('tb-block-browser__result-card--selected', card.dataset.tbLibrarySlug === slug);
        });

        elements.browserPreviewTitle.textContent = item.name || item.manifest?.name || slug;
        elements.browserPreviewCategory.textContent = item.category || item.manifest?.category || 'Block';
        elements.browserPreviewDescription.textContent = item.description || item.manifest?.description || 'Loading the live block preview…';
        elements.browserPreview.style.height = '220px';
        if (elements.browserPreviewShell) elements.browserPreviewShell.style.height = '220px';
        elements.browserPreview.srcdoc = emptyPreviewDocument('Loading live HTML preview…');
        elements.addSelected.disabled = true;

        try {
            const manifest = item.manifest || await fetchManifest(slug);
            if (sequence !== librarySelectionSequence || libraryState.selectedSlug !== slug) return;
            manifestCache.set(slug, manifest);
            renderBrowserPreview(manifest);
        } catch (error) {
            if (sequence !== librarySelectionSequence) return;
            elements.browserPreview.srcdoc = emptyPreviewDocument(error.message);
            elements.browserPreviewDescription.textContent = error.message;
            elements.addSelected.disabled = true;
        }
    };

    const renderCategories = () => {
        elements.categoryList.replaceChildren();
        const categories = ['', ...libraryState.categories];
        categories.forEach((category) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `tb-block-browser__category-button${category === libraryState.category ? ' tb-block-browser__category-button--active' : ''}`;
            const count = category ? Number(libraryState.categoryCounts?.[category] || 0) : Number(libraryState.availableTotal || 0);
            button.textContent = `${category || 'All blocks'} (${count})`;
            button.addEventListener('click', () => {
                if (libraryState.category === category) return;
                libraryState.category = category;
                elements.category.value = category;
                loadLibrary(1);
            });
            elements.categoryList.append(button);
        });
    };

    const paginationButton = (label, page, options = {}) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = `tb-block-browser__pagination-button${options.active ? ' tb-block-browser__pagination-button--active' : ''}${options.wide ? ' tb-block-browser__pagination-button--wide' : ''}`;
        button.innerHTML = label;
        button.disabled = Boolean(options.disabled);
        button.setAttribute('aria-label', options.ariaLabel || `Go to page ${page}`);
        if (options.active) button.setAttribute('aria-current', 'page');
        button.addEventListener('click', () => loadLibrary(page));
        return button;
    };

    const renderPagination = () => {
        elements.pagination.replaceChildren();
        if (libraryState.pages <= 1) return;

        elements.pagination.append(paginationButton('<i class="fa-solid fa-chevron-left"></i><span>Previous</span>', Math.max(1, libraryState.page - 1), {
            disabled: libraryState.page <= 1,
            wide: true,
            ariaLabel: 'Previous block page',
        }));

        const candidates = new Set([1, libraryState.pages]);
        for (let page = Math.max(1, libraryState.page - 2); page <= Math.min(libraryState.pages, libraryState.page + 2); page += 1) candidates.add(page);
        let previous = 0;
        [...candidates].sort((a, b) => a - b).forEach((page) => {
            if (previous && page - previous > 1) {
                const gap = document.createElement('span');
                gap.className = 'tb-block-browser__pagination-gap';
                gap.textContent = '…';
                elements.pagination.append(gap);
            }
            elements.pagination.append(paginationButton(String(page), page, {active: page === libraryState.page}));
            previous = page;
        });

        elements.pagination.append(paginationButton('<span>Next</span><i class="fa-solid fa-chevron-right"></i>', Math.min(libraryState.pages, libraryState.page + 1), {
            disabled: libraryState.page >= libraryState.pages,
            wide: true,
            ariaLabel: 'Next block page',
        }));
    };

    const renderLibrary = () => {
        elements.blockList.replaceChildren();
        renderCategories();

        if (!libraryState.items.length) {
            const empty = document.createElement('div');
            empty.className = 'tb-block-browser__empty';
            empty.innerHTML = '<i class="fa-regular fa-folder-open"></i><strong>No matching blocks</strong><span>Try another category or search phrase.</span>';
            elements.blockList.append(empty);
            renderPagination();
            renderBrowserPreview(null);
            return;
        }

        libraryState.items.forEach((item) => {
            if (!item?.slug) return;
            const card = document.createElement('article');
            card.className = `tb-block-browser__result-card${item.slug === libraryState.selectedSlug ? ' tb-block-browser__result-card--selected' : ''}`;
            card.dataset.tbLibrarySlug = item.slug;

            const select = document.createElement('button');
            select.type = 'button';
            select.className = 'tb-block-browser__result-select';
            select.addEventListener('click', () => selectLibraryBlock(item));
            select.addEventListener('dblclick', () => addBlock(item.slug));

            const icon = document.createElement('span');
            icon.className = 'tb-block-browser__result-icon';
            icon.innerHTML = '<i class="fa-regular fa-window-maximize"></i>';
            const text = document.createElement('span');
            text.className = 'tb-block-browser__result-text';
            const name = document.createElement('strong');
            name.className = 'tb-block-browser__result-name';
            name.textContent = item.name || item.slug;
            const category = document.createElement('span');
            category.className = 'tb-block-browser__result-category';
            category.textContent = item.category || 'Block';
            const description = document.createElement('span');
            description.className = 'tb-block-browser__result-description';
            description.textContent = item.description || '';
            text.append(name, category, description);
            select.append(icon, text);

            const add = document.createElement('button');
            add.type = 'button';
            add.className = 'tb-block-browser__result-add';
            add.title = `Add ${name.textContent}`;
            add.innerHTML = '<i class="fa-solid fa-plus"></i>';
            add.addEventListener('click', () => addBlock(item.slug));
            card.append(select, add);
            elements.blockList.append(card);
        });

        const selectedOnPage = libraryState.items.find((item) => item.slug === libraryState.selectedSlug);
        const fallback = libraryState.items[0] || null;
        selectLibraryBlock(selectedOnPage || fallback);
        renderPagination();
    };

    const loadLibrary = async (page = 1) => {
        elements.blockList.innerHTML = '<div class="tb-block-browser__loading"><i class="fa-solid fa-circle-notch fa-spin"></i><span>Loading blocks…</span></div>';
        const url = new URL(builder.dataset.apiList, window.location.href);
        url.searchParams.set('p', String(page));
        url.searchParams.set('context', builderContext);
        if (libraryState.category) url.searchParams.set('category', libraryState.category);
        if (libraryState.search) url.searchParams.set('q', libraryState.search);

        try {
            const response = await fetch(url, {headers: {'Accept': 'application/json'}});
            const payload = await response.json();
            if (!response.ok || !payload.ok) throw new Error(payload.message || 'Unable to load blocks.');
            const data = payload.data;
            libraryState.page = Number(data.page) || 1;
            libraryState.pages = Math.max(1, Number(data.pages) || 1);
            libraryState.items = data.items || [];
            libraryState.categories = data.categories || [];
            libraryState.categoryCounts = data.category_counts || {};
            libraryState.availableTotal = Number(data.available_total) || 0;
            libraryState.loaded = true;
            elements.resultCount.textContent = `${data.total} block${Number(data.total) === 1 ? '' : 's'}`;

            elements.category.replaceChildren(new Option(`All categories (${libraryState.availableTotal})`, ''));
            libraryState.categories.forEach((category) => elements.category.add(new Option(`${category} (${Number(libraryState.categoryCounts?.[category] || 0)})`, category)));
            elements.category.value = libraryState.category;
            renderLibrary();
        } catch (error) {
            elements.blockList.innerHTML = `<div class="tb-block-browser__empty"><i class="fa-solid fa-triangle-exclamation"></i><strong>Unable to load blocks</strong><span>${escapeHtml(error.message)}</span></div>`;
            elements.pagination.replaceChildren();
        }
    };

    const openBlockBrowser = () => {
        elements.browser.hidden = false;
        document.documentElement.classList.add('tb-block-browser-open');
        document.body.classList.add('tb-block-browser-open');
        setBrowserPreviewBreakpoint('768');
        if (!libraryState.loaded) loadLibrary(1);
        else {
            renderLibrary();
            renderBrowserPreview();
        }
        window.setTimeout(() => elements.search.focus(), 30);
    };

    const closeBlockBrowser = () => {
        elements.browser.hidden = true;
        document.documentElement.classList.remove('tb-block-browser-open');
        document.body.classList.remove('tb-block-browser-open');
        elements.openLibrary?.focus();
    };

    const selectedImage = () => imageState.items.find((item) => item.url === imageState.selectedUrl) || null;

    const renderImagePreview = (item = selectedImage()) => {
        if (!item) {
            elements.imagePreview.hidden = true;
            elements.imagePreview.removeAttribute('src');
            elements.imagePreviewEmpty.hidden = false;
            elements.imagePreviewName.textContent = 'No image selected';
            elements.imagePreviewCategory.textContent = 'Image library';
            elements.imagePreviewSize.textContent = '';
            elements.useImage.disabled = true;
            return;
        }
        elements.imagePreview.src = item.url;
        elements.imagePreview.alt = item.name || item.filename || 'Selected library image';
        elements.imagePreview.hidden = false;
        elements.imagePreviewEmpty.hidden = true;
        elements.imagePreviewName.textContent = item.name || item.filename;
        elements.imagePreviewCategory.textContent = item.category || 'Image library';
        elements.imagePreviewSize.textContent = item.width && item.height ? `${item.width} × ${item.height}px` : item.filename || '';
        elements.useImage.disabled = false;
    };

    const selectImage = (item) => {
        if (!item?.url) return;
        imageState.selectedUrl = item.url;
        elements.imageList.querySelectorAll('[data-tb-image-url]').forEach((card) => {
            card.classList.toggle('tb-image-browser__card--selected', card.dataset.tbImageUrl === item.url);
        });
        renderImagePreview(item);
    };

    const renderImageCategories = () => {
        elements.imageCategoryList.replaceChildren();
        const categories = [{slug: '', name: 'All images'}, ...imageState.categories];
        categories.forEach((category) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `tb-image-browser__category-button${category.slug === imageState.category ? ' tb-image-browser__category-button--active' : ''}`;
            button.textContent = category.name;
            button.addEventListener('click', () => {
                if (imageState.category === category.slug) return;
                imageState.category = category.slug;
                elements.imageCategory.value = category.slug;
                loadImages(1);
            });
            elements.imageCategoryList.append(button);
        });
    };

    const imagePaginationButton = (label, page, options = {}) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = `tb-image-browser__pagination-button${options.active ? ' tb-image-browser__pagination-button--active' : ''}${options.wide ? ' tb-image-browser__pagination-button--wide' : ''}`;
        button.innerHTML = label;
        button.disabled = Boolean(options.disabled);
        if (options.active) button.setAttribute('aria-current', 'page');
        button.addEventListener('click', () => loadImages(page));
        return button;
    };

    const renderImagePagination = () => {
        elements.imagePagination.replaceChildren();
        if (imageState.pages <= 1) return;
        elements.imagePagination.append(imagePaginationButton('<i class="fa-solid fa-chevron-left"></i><span>Previous</span>', Math.max(1, imageState.page - 1), {disabled: imageState.page <= 1, wide: true}));
        const candidates = new Set([1, imageState.pages]);
        for (let page = Math.max(1, imageState.page - 2); page <= Math.min(imageState.pages, imageState.page + 2); page += 1) candidates.add(page);
        let previous = 0;
        [...candidates].sort((a, b) => a - b).forEach((page) => {
            if (previous && page - previous > 1) {
                const gap = document.createElement('span');
                gap.className = 'tb-image-browser__pagination-gap';
                gap.textContent = '…';
                elements.imagePagination.append(gap);
            }
            elements.imagePagination.append(imagePaginationButton(String(page), page, {active: page === imageState.page}));
            previous = page;
        });
        elements.imagePagination.append(imagePaginationButton('<span>Next</span><i class="fa-solid fa-chevron-right"></i>', Math.min(imageState.pages, imageState.page + 1), {disabled: imageState.page >= imageState.pages, wide: true}));
    };

    const applySelectedImage = () => {
        const item = selectedImage();
        if (!item || !imageState.target) return;
        if (imageState.target.type === 'featured') {
            elements.featuredImage.value = item.url;
            elements.featuredImage.dispatchEvent(new Event('change', {bubbles: true}));
        } else if (imageState.target.type === 'element') {
            const block = blocks.find((candidate) => candidate.id === imageState.target.blockId);
            const group = block?.elements?.[imageState.target.group];
            const elementItem = Array.isArray(group) ? group.find((candidate) => candidate.id === imageState.target.itemId) : null;
            if (!block || !elementItem) return;
            updateElementValue(imageState.target, item.url);
            selectedId = block.id;
            selectedElement = {group:imageState.target.group,itemId:elementItem.id};
            renderCanvas();
        }
        closeImageBrowser();
    };

    const renderImages = () => {
        elements.imageList.replaceChildren();
        renderImageCategories();
        if (!imageState.items.length) {
            const empty = document.createElement('div');
            empty.className = 'tb-image-browser__empty';
            empty.innerHTML = '<i class="fa-regular fa-folder-open"></i><strong>No images found</strong><span>Add images to category folders under assets/images/library, or try another search.</span>';
            elements.imageList.append(empty);
            imageState.selectedUrl = '';
            renderImagePreview(null);
            renderImagePagination();
            return;
        }
        imageState.items.forEach((item) => {
            const card = document.createElement('button');
            card.type = 'button';
            card.className = `tb-image-browser__card${item.url === imageState.selectedUrl ? ' tb-image-browser__card--selected' : ''}`;
            card.dataset.tbImageUrl = item.url;
            const image = document.createElement('img');
            image.className = 'tb-image-browser__thumbnail';
            image.src = item.url;
            image.alt = item.name || item.filename;
            image.loading = 'lazy';
            const meta = document.createElement('span');
            meta.className = 'tb-image-browser__card-meta';
            const name = document.createElement('strong');
            name.className = 'tb-image-browser__card-name';
            name.textContent = item.name || item.filename;
            const category = document.createElement('span');
            category.className = 'tb-image-browser__card-category';
            category.textContent = item.category || 'Image';
            meta.append(name, category);
            card.append(image, meta);
            card.addEventListener('click', () => selectImage(item));
            card.addEventListener('dblclick', () => { selectImage(item); applySelectedImage(); });
            elements.imageList.append(card);
        });
        const selectedOnPage = imageState.items.find((item) => item.url === imageState.selectedUrl);
        selectImage(selectedOnPage || imageState.items[0]);
        renderImagePagination();
    };

    const loadImages = async (page = 1) => {
        elements.imageList.innerHTML = '<div class="tb-image-browser__loading"><i class="fa-solid fa-circle-notch fa-spin"></i><span>Loading images…</span></div>';
        const url = new URL(builder.dataset.apiImages, window.location.href);
        url.searchParams.set('p', String(page));
        url.searchParams.set('context', builderContext);
        if (imageState.category) url.searchParams.set('category', imageState.category);
        if (imageState.search) url.searchParams.set('q', imageState.search);
        try {
            const response = await fetch(url, {headers: {'Accept': 'application/json'}});
            const payload = await response.json();
            if (!response.ok || !payload.ok) throw new Error(payload.message || 'Unable to load images.');
            const data = payload.data;
            imageState.page = Number(data.page) || 1;
            imageState.pages = Math.max(1, Number(data.pages) || 1);
            imageState.items = data.items || [];
            imageState.categories = data.categories || [];
            imageState.loaded = true;
            elements.imageResultCount.textContent = `${data.total} image${Number(data.total) === 1 ? '' : 's'}`;
            elements.imageCategory.replaceChildren(new Option('All categories', ''));
            imageState.categories.forEach((category) => elements.imageCategory.add(new Option(category.name, category.slug)));
            elements.imageCategory.value = imageState.category;
            renderImages();
        } catch (error) {
            elements.imageList.innerHTML = `<div class="tb-image-browser__empty"><i class="fa-solid fa-triangle-exclamation"></i><strong>Unable to load images</strong><span>${escapeHtml(error.message)}</span></div>`;
            elements.imagePagination.replaceChildren();
        }
    };

    const openImageBrowser = (target) => {
        imageState.target = target;
        elements.imageBrowser.hidden = false;
        document.documentElement.classList.add('tb-image-browser-open');
        document.body.classList.add('tb-image-browser-open');
        if (!imageState.loaded) loadImages(1);
        else renderImages();
        window.setTimeout(() => elements.imageSearch.focus(), 30);
    };

    const closeImageBrowser = () => {
        elements.imageBrowser.hidden = true;
        document.documentElement.classList.remove('tb-image-browser-open');
        document.body.classList.remove('tb-image-browser-open');
    };

    const addBlock = async (slug) => {
        try {
            const manifest = await fetchManifest(slug);
            const block = createBlockFromManifest(manifest);
            blocks.push(block);
            selectedId = block.id;
            selectedElement = null;
            await renderCanvas();
            closeBlockBrowser();
            elements.frameShell.scrollIntoView({behavior: 'smooth', block: 'center'});
        } catch (error) {
            window.alert(error.message);
        }
    };

    const setBreakpoint = (value) => {
        builder.querySelectorAll('[data-tb-breakpoint]').forEach((button) => button.classList.toggle('tb-builder__tool-button--active', button.dataset.tbBreakpoint === value));
        elements.frameShell.style.width = value === 'full' ? '100%' : `${value}px`;
    };

    const paletteMatch = () => {
        const current = JSON.stringify(palette);
        return Object.entries(palettePresets).find(([, preset]) => JSON.stringify(preset.colors || {}) === current)?.[0] || 'custom';
    };

    const updatePaletteInputs = () => {
        builder.querySelectorAll('[data-tb-palette-color]').forEach((input) => {
            const key = input.dataset.tbPaletteColor;
            if (palette[key] && /^#[0-9a-f]{6}$/i.test(palette[key])) input.value = palette[key];
        });
    };

    const setPaletteFileStatus = (message, isError = false) => {
        if (!elements.paletteFileStatus) return;
        elements.paletteFileStatus.textContent = message;
        elements.paletteFileStatus.classList.toggle('tb-builder__hint--error', isError);
    };

    const exportPalette = () => {
        const payload = {
            format: 'thunder-blog-palette',
            version: 1,
            name: `${elements.title?.value.trim() || contentLabel} palette`,
            colors: {...palette},
        };
        const blob = new Blob([JSON.stringify(payload, null, 2) + '\n'], {type: 'application/json'});
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `${slugify(elements.title?.value || contentLabel) || builderContext}-palette.json`;
        document.body.append(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(url);
        setPaletteFileStatus('Palette exported successfully.');
    };

    const importPalette = async (file) => {
        if (!file) return;
        try {
            const parsed = JSON.parse(await file.text());
            const source = parsed?.colors && typeof parsed.colors === 'object' ? parsed.colors : parsed;
            if (!source || typeof source !== 'object' || Array.isArray(source)) throw new Error('The file does not contain a palette object.');
            const next = {...palette};
            let imported = 0;
            builder.querySelectorAll('[data-tb-palette-color]').forEach((input) => {
                const key = input.dataset.tbPaletteColor;
                let value = String(source[key] || '').trim();
                if (/^#[0-9a-f]{3}$/i.test(value)) value = `#${value.slice(1).split('').map((part) => part + part).join('')}`;
                if (!/^#[0-9a-f]{6}$/i.test(value)) return;
                next[key] = value;
                imported += 1;
            });
            if (!imported) throw new Error('No recognized Thunder Blog palette colors were found.');
            palette = next;
            elements.paletteSelect.value = 'custom';
            updatePaletteInputs();
            syncHidden();
            renderBrowserPreview();
            renderCanvas();
            setPaletteFileStatus(`Imported ${imported} palette colors from ${file.name}.`);
        } catch (error) {
            setPaletteFileStatus(error.message || 'The palette file could not be imported.', true);
        } finally {
            elements.paletteImport.value = '';
        }
    };

    builder.querySelectorAll('[data-tb-breakpoint]').forEach((button) => button.addEventListener('click', () => setBreakpoint(button.dataset.tbBreakpoint)));

    builder.querySelector('[data-tb-wide]')?.addEventListener('click', (event) => {
        builder.classList.toggle('tb-builder--wide');
        event.currentTarget.classList.toggle('tb-builder__tool-button--active');
    });

    elements.openLibrary?.addEventListener('click', openBlockBrowser);
    elements.closeLibrary?.addEventListener('click', closeBlockBrowser);
    elements.addSelected?.addEventListener('click', () => {
        const manifest = selectedLibraryManifest();
        if (manifest) addBlock(manifest.slug);
    });
    builder.querySelectorAll('[data-tb-browser-breakpoint]').forEach((button) => {
        button.addEventListener('click', () => setBrowserPreviewBreakpoint(button.dataset.tbBrowserBreakpoint));
    });
    elements.featuredImageBrowse?.addEventListener('click', () => openImageBrowser({type: 'featured'}));
    elements.closeImageBrowser?.addEventListener('click', closeImageBrowser);
    elements.useImage?.addEventListener('click', applySelectedImage);
    elements.imageCategory?.addEventListener('change', () => {
        imageState.category = elements.imageCategory.value;
        loadImages(1);
    });
    elements.imageSearch?.addEventListener('input', () => {
        window.clearTimeout(imageSearchTimer);
        imageSearchTimer = window.setTimeout(() => {
            imageState.search = elements.imageSearch.value.trim();
            loadImages(1);
        }, 280);
    });
    elements.paletteExport?.addEventListener('click', exportPalette);
    elements.paletteImportButton?.addEventListener('click', () => elements.paletteImport?.click());
    elements.paletteImport?.addEventListener('change', () => importPalette(elements.paletteImport.files?.[0]));
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        if (!elements.imageBrowser.hidden) closeImageBrowser();
        else if (!elements.browser.hidden) closeBlockBrowser();
    });

    builder.querySelector('[data-tb-fullscreen]')?.addEventListener('click', async () => {
        if (document.fullscreenElement === builder) {
            await document.exitFullscreen();
        } else if (builder.requestFullscreen) {
            await builder.requestFullscreen();
        }
    });

    document.addEventListener('fullscreenchange', () => builder.classList.toggle('tb-builder--fullscreen', document.fullscreenElement === builder));

    builder.querySelectorAll('[data-tb-tab]').forEach((tab) => {
        tab.addEventListener('click', () => {
            const name = tab.dataset.tbTab;
            builder.querySelectorAll('[data-tb-tab]').forEach((item) => item.classList.toggle('tb-builder__tab--active', item === tab));
            builder.querySelectorAll('[data-tb-panel]').forEach((panel) => panel.classList.toggle('tb-builder__tab-panel--active', panel.dataset.tbPanel === name));
        });
    });

    elements.category.addEventListener('change', () => {
        libraryState.category = elements.category.value;
        loadLibrary(1);
    });

    elements.search.addEventListener('input', () => {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(() => {
            libraryState.search = elements.search.value.trim();
            loadLibrary(1);
        }, 280);
    });

    elements.paletteSelect.addEventListener('change', () => {
        const key = elements.paletteSelect.value;
        if (key !== 'custom' && palettePresets[key]?.colors) {
            palette = {...palettePresets[key].colors};
            updatePaletteInputs();
        }
        syncHidden();
        renderBrowserPreview();
        renderCanvas();
    });

    builder.querySelectorAll('[data-tb-palette-color]').forEach((input) => {
        input.addEventListener('input', () => {
            palette[input.dataset.tbPaletteColor] = input.value;
            elements.paletteSelect.value = 'custom';
            syncHidden();
            renderBrowserPreview();
            scheduleCanvas();
        });
    });

    const pushSelectionToCanvas = () => elements.canvas.contentWindow?.postMessage({
        type: 'tb-active-selection',
        selection: {blockId:selectedId,element:selectedElement},
    }, '*');

    elements.canvas.addEventListener('load', () => {
        pushSelectionToCanvas();
        if (selectedElement) renderInspector();
    });

    window.addEventListener('message', (event) => {
        const data = event.data || {};
        if (data.type === 'tb-browser-preview-height' && event.source === elements.browserPreview?.contentWindow && Number.isFinite(Number(data.height))) {
            if (Number(data.token) !== browserPreviewRenderToken) return;
            const height = Math.max(120, Math.min(50000, Number(data.height)));
            elements.browserPreview.style.height = `${height}px`;
            if (elements.browserPreviewShell) elements.browserPreviewShell.style.height = `${height}px`;
            return;
        }
        if (event.source !== elements.canvas.contentWindow) return;
        if (data.type === 'tb-select-block' && blocks.some((block) => block.id === data.id)) {
            selectedId = data.id;
            selectedElement = null;
            selectedElementComputedFontSize = '';
            renderInspector();
            pushSelectionToCanvas();
        }
        if (data.type === 'tb-select-element' && blocks.some((block) => block.id === data.id)) {
            selectedId = data.id;
            selectedElement = {group:String(data.group || ''),itemId:String(data.itemId || '')};
            const reportedFontSize = Number.parseFloat(data.fontSize);
            selectedElementComputedFontSize = Number.isFinite(reportedFontSize)
                ? String(Math.round(reportedFontSize * 10) / 10)
                : '';
            renderInspector();
            pushSelectionToCanvas();
        }
        if (data.type === 'tb-clear-selection') {
            selectedId = null;
            selectedElement = null;
            selectedElementComputedFontSize = '';
            renderInspector();
            pushSelectionToCanvas();
        }
        if (data.type === 'tb-inline-edit') {
            const block = blocks.find((candidate) => candidate.id === data.id);
            const manifest = block ? manifestCache.get(block.slug) : null;
            const definition = manifestElements(manifest).find((candidate) => candidate.key === data.group);
            const elementItem = block?.elements?.[data.group]?.find((candidate) => candidate.id === data.itemId);
            const field = definition?.schema?.find((candidate) => candidate.key === data.key);
            if (block && elementItem && field) {
                const value = field.type === 'richtext' ? sanitizeRichText(data.value) : String(data.value ?? '').trim();
                updateElementValue({type:'element',blockId:block.id,group:data.group,itemId:elementItem.id,key:data.key}, value);
                selectedId = block.id;
                selectedElement = {group:data.group,itemId:elementItem.id};
                renderCanvas();
            }
        }
        if (data.type === 'tb-open-image-browser' && blocks.some((block) => block.id === data.id)) {
            selectedId = data.id;
            selectedElement = {group:String(data.group || ''),itemId:String(data.itemId || '')};
            openImageBrowser({type:'element',blockId:data.id,group:data.group,itemId:data.itemId,key:data.key});
        }
        if (data.type === 'tb-canvas-height' && Number.isFinite(Number(data.height))) {
            const height = Math.max(560, Math.min(100000, Number(data.height)));
            elements.canvas.style.height = `${height}px`;
            elements.frameShell.style.height = `${height}px`;
        }
    });

    elements.form?.addEventListener('submit', syncHidden);

    const slugify = (value) => String(value || '')
        .normalize('NFKD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    elements.slug?.addEventListener('input', () => { slugWasEdited = Boolean(elements.slug.value.trim()); });
    elements.title?.addEventListener('input', () => {
        if (!slugWasEdited && elements.slug) elements.slug.value = slugify(elements.title.value);
    });

    blocks = blocks.map((block) => ({
        id: block.id || uid(),
        slug: block.slug,
        settings: block.settings && typeof block.settings === 'object' ? block.settings : {},
        elements: block.elements && typeof block.elements === 'object' ? block.elements : {},
    }));
    selectedId = null;
    selectedElement = null;
    selectedElementComputedFontSize = '';
    elements.paletteSelect.value = paletteMatch();
    updatePaletteInputs();
    setBreakpoint('full');
    setBrowserPreviewBreakpoint('768');
    renderBrowserPreview(null);
    syncHidden();
    renderCanvas();
})();
