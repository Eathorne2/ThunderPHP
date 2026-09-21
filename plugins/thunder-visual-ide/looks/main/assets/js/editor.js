(() => {
    'use strict';

    const cfg = window.THUNDER_VISUAL_IDE || {};
    const definitions = cfg.nodeDefinitions || {};
    const paginationTemplates = Array.isArray(cfg.paginationTemplates) ? cfg.paginationTemplates : [];
    const htmlSnippets = Array.isArray(cfg.htmlSnippets) ? cfg.htmlSnippets : [];
    const validationRules = Array.isArray(cfg.validationRules) ? cfg.validationRules : [];
    const bundledPresets = Array.isArray(cfg.bundledPresets) ? cfg.bundledPresets : [];
    const formThemes = Array.isArray(cfg.formThemes) ? cfg.formThemes : [];
    const htmlSnippetPalettePresets = [
        {
            id: 'coastal-blue',
            name: 'Coastal Blue',
            colors: {
                primary: '#0369a1',
                secondary: '#0ea5e9',
                surface: '#ffffff',
                surface_alt: '#f0f9ff',
                text: '#0c4a6e',
                muted: '#526b7a',
                border: '#bae6fd',
                danger: '#dc2626',
                on_primary: '#ffffff',
                success: '#16a34a',
                warning: '#d97706',
                info: '#0284c7'
            }
        },
        {
            id: 'emerald-grove',
            name: 'Emerald Grove',
            colors: {
                primary: '#047857',
                secondary: '#10b981',
                surface: '#ffffff',
                surface_alt: '#ecfdf5',
                text: '#064e3b',
                muted: '#527269',
                border: '#a7f3d0',
                danger: '#dc2626',
                on_primary: '#ffffff',
                success: '#15803d',
                warning: '#d97706',
                info: '#0891b2'
            }
        },
        {
            id: 'amber-studio',
            name: 'Amber Studio',
            colors: {
                primary: '#b45309',
                secondary: '#f59e0b',
                surface: '#fffdf7',
                surface_alt: '#fffbeb',
                text: '#78350f',
                muted: '#8a6547',
                border: '#fde68a',
                danger: '#b91c1c',
                on_primary: '#ffffff',
                success: '#15803d',
                warning: '#b45309',
                info: '#0369a1'
            }
        },
        {
            id: 'ruby-editorial',
            name: 'Ruby Editorial',
            colors: {
                primary: '#be123c',
                secondary: '#e11d48',
                surface: '#ffffff',
                surface_alt: '#fff1f2',
                text: '#881337',
                muted: '#8f5968',
                border: '#fecdd3',
                danger: '#991b1b',
                on_primary: '#ffffff',
                success: '#15803d',
                warning: '#d97706',
                info: '#0369a1'
            }
        },
        {
            id: 'violet-signal',
            name: 'Violet Signal',
            colors: {
                primary: '#6d28d9',
                secondary: '#8b5cf6',
                surface: '#ffffff',
                surface_alt: '#f5f3ff',
                text: '#3b0764',
                muted: '#725b83',
                border: '#ddd6fe',
                danger: '#dc2626',
                on_primary: '#ffffff',
                success: '#16a34a',
                warning: '#d97706',
                info: '#0891b2'
            }
        },
        {
            id: 'graphite-dark',
            name: 'Graphite Dark',
            colors: {
                primary: '#38bdf8',
                secondary: '#818cf8',
                surface: '#111827',
                surface_alt: '#0b1220',
                text: '#f8fafc',
                muted: '#94a3b8',
                border: '#334155',
                danger: '#fb7185',
                on_primary: '#082f49',
                success: '#4ade80',
                warning: '#fbbf24',
                info: '#38bdf8'
            }
        },
        {
            id: 'warm-sand',
            name: 'Warm Sand',
            colors: {
                primary: '#92400e',
                secondary: '#c2410c',
                surface: '#fffdf8',
                surface_alt: '#f7f1e7',
                text: '#422006',
                muted: '#766554',
                border: '#e7d7c3',
                danger: '#b91c1c',
                on_primary: '#ffffff',
                success: '#15803d',
                warning: '#b45309',
                info: '#0369a1'
            }
        },
        {
            id: 'electric-lime',
            name: 'Electric Lime',
            colors: {
                primary: '#b7f34a',
                secondary: '#22d3ee',
                surface: '#0b0d0a',
                surface_alt: '#151914',
                text: '#f7fee7',
                muted: '#a3b39a',
                border: '#33412e',
                danger: '#fb7185',
                on_primary: '#111807',
                success: '#86efac',
                warning: '#facc15',
                info: '#67e8f9'
            }
        },
        {
            id: 'ink-paper',
            name: 'Ink & Paper',
            colors: {
                primary: '#111111',
                secondary: '#3157ff',
                surface: '#fffdf8',
                surface_alt: '#f0ece3',
                text: '#111111',
                muted: '#68645e',
                border: '#cfc9bf',
                danger: '#b91c1c',
                on_primary: '#ffffff',
                success: '#18794e',
                warning: '#a16207',
                info: '#3157ff'
            }
        },
        {
            id: 'digital-sunset',
            name: 'Digital Sunset',
            colors: {
                primary: '#f43f5e',
                secondary: '#7c3aed',
                surface: '#fff7ed',
                surface_alt: '#ffe4e6',
                text: '#3f1723',
                muted: '#7b5962',
                border: '#fecdd3',
                danger: '#be123c',
                on_primary: '#ffffff',
                success: '#16a34a',
                warning: '#ea580c',
                info: '#6d28d9'
            }
        }
    ];
    const app = document.getElementById('tvi-app');
    if (!app) return;

    const mainLayout = document.getElementById('tvi-main');
    const nodeLibraryPanel = document.getElementById('tvi-node-library-panel');
    const inspectorPanel = document.getElementById('tvi-inspector-panel');
    const nodeLibraryToggle = document.getElementById('tvi-toggle-node-library');
    const inspectorToggle = document.getElementById('tvi-toggle-inspector');
    const viewport = document.getElementById('tvi-viewport');
    const world = document.getElementById('tvi-world');
    const nodesLayer = document.getElementById('tvi-nodes');
    const svg = document.getElementById('tvi-connections');
    const palette = document.getElementById('tvi-palette-list');
    const inspector = document.getElementById('tvi-inspector-content');
    const search = document.getElementById('tvi-node-search');
    const emptyState = document.getElementById('tvi-empty-state');
    const importInput = document.getElementById('tvi-import-input');
    const packageImportInput = document.getElementById('tvi-package-import-input');
    const ideDataImportInput = document.getElementById('tvi-data-import-input');
    const samplesModal = document.getElementById('tvi-samples-modal');
    const samplesList = document.getElementById('tvi-samples-list');
    const previewModal = document.getElementById('tvi-preview-modal');
    const previewFiles = document.getElementById('tvi-preview-files');
    const previewCode = document.getElementById('tvi-preview-code');
    const previewPath = document.getElementById('tvi-preview-path');
    const previewSummary = document.getElementById('tvi-preview-summary');
    const previewBreadcrumbs = document.getElementById('tvi-preview-breadcrumbs');
    const previewUp = document.getElementById('tvi-preview-up');
    const codeModal = document.getElementById('tvi-code-modal');
    const codeModalTitle = document.getElementById('tvi-code-modal-title');
    const codeModalTextarea = document.getElementById('tvi-code-modal-textarea');
    const codeModalSave = codeModal?.querySelector('[data-action="save-code-editor"]');
    const methodsModal = document.getElementById('tvi-methods-modal');
    const methodsList = document.getElementById('tvi-methods-list');
    const methodName = document.getElementById('tvi-method-name');
    const methodsTitle = document.getElementById('tvi-methods-title');
    const methodParamsList = document.getElementById('tvi-method-params-list');
    const methodReturnType = document.getElementById('tvi-method-return-type');
    const methodImplementation = document.getElementById('tvi-method-implementation');
    const methodVisibility = document.getElementById('tvi-method-visibility');
    const methodStatic = document.getElementById('tvi-method-static');
    const methodChainable = document.getElementById('tvi-method-chainable');
    const methodVisibilityField = document.getElementById('tvi-method-visibility-field');
    const methodStaticField = document.getElementById('tvi-method-static-field');
    const methodBodyField = document.getElementById('tvi-method-body-field');
    const methodFlowField = document.getElementById('tvi-method-flow-field');
    const methodBody = document.getElementById('tvi-method-body');
    const methodHelp = document.getElementById('tvi-method-help');
    const methodsSummary = document.getElementById('tvi-methods-summary');
    const methodBrowserModal = document.getElementById('tvi-method-browser-modal');
    const methodBrowserTitle = document.getElementById('tvi-method-browser-title');
    const methodBrowserSummary = document.getElementById('tvi-method-browser-summary');
    const methodBrowserSearch = document.getElementById('tvi-method-browser-search');
    const methodBrowserList = document.getElementById('tvi-method-browser-list');
    const methodBrowserDetail = document.getElementById('tvi-method-browser-detail');
    const methodBrowserChainList = document.getElementById('tvi-method-chain-list');
    const methodBrowserChainPreview = document.getElementById('tvi-method-chain-preview');
    const methodBrowserChainStatus = document.getElementById('tvi-method-chain-status');
    const methodBrowserChainSource = document.getElementById('tvi-method-chain-source');
    const methodBrowserUseButton = document.getElementById('tvi-method-browser-use');
    const methodBrowserCreateButton = document.getElementById('tvi-method-chain-create');
    const libraryModal = document.getElementById('tvi-library-modal');
    const librarySearch = document.getElementById('tvi-library-search');
    const libraryList = document.getElementById('tvi-library-list');
    const paginationTemplatesModal = document.getElementById('tvi-pagination-templates-modal');
    const paginationTemplatesList = document.getElementById('tvi-pagination-templates-list');
    const htmlDesignerModal = document.getElementById('tvi-html-designer-modal');
    const htmlDesignerDialog = htmlDesignerModal?.querySelector('.tvi-modal__dialog--html-designer');
    const htmlDesignerTitle = document.getElementById('tvi-html-designer-title');
    const htmlDesignerName = document.getElementById('tvi-html-designer-name');
    const htmlDesignerComponent = document.getElementById('tvi-html-designer-component');
    const htmlDesignerHtml = document.getElementById('tvi-html-designer-html');
    const htmlDesignerCss = document.getElementById('tvi-html-designer-css');
    const htmlDesignerJs = document.getElementById('tvi-html-designer-js');
    const htmlDesignerPreview = document.getElementById('tvi-html-designer-preview');
    const htmlPreviewStage = document.getElementById('tvi-html-preview-stage');
    const htmlPreviewWidthButtons = [...document.querySelectorAll('[data-action="set-html-preview-width"]')];
    const htmlPreviewStatus = document.getElementById('tvi-html-preview-status');
    const htmlPreviewExpand = document.getElementById('tvi-html-preview-expand');
    const htmlJsToggle = document.getElementById('tvi-html-js-toggle');
    const htmlSnippetsModal = document.getElementById('tvi-html-snippets-modal');
    const htmlSnippetTabs = document.getElementById('tvi-html-snippet-tabs');
    const htmlSnippetSearch = document.getElementById('tvi-html-snippet-search');
    const htmlSnippetList = document.getElementById('tvi-html-snippet-list');
    const htmlSnippetPrev = document.getElementById('tvi-html-snippet-prev');
    const htmlSnippetNext = document.getElementById('tvi-html-snippet-next');
    const htmlSnippetPageInfo = document.getElementById('tvi-html-snippet-page-info');
    const htmlSnippetPreview = document.getElementById('tvi-html-snippet-preview');
    const htmlSnippetPreviewName = document.getElementById('tvi-html-snippet-preview-name');
    const htmlSnippetPreviewDescription = document.getElementById('tvi-html-snippet-preview-description');
    const htmlSnippetPreviewCategory = document.getElementById('tvi-html-snippet-preview-category');
    const htmlSnippetPreviewPanel = htmlSnippetPreview?.closest('.tvi-html-snippet-preview-panel');
    const htmlSnippetPreviewFullscreen = document.getElementById('tvi-html-snippet-preview-fullscreen');
    const htmlSnippetBrowserLayout = htmlSnippetsModal?.querySelector('.tvi-html-snippet-browser-layout');
    const htmlSnippetPalettePanel = document.getElementById('tvi-html-snippet-palette-panel');
    const htmlSnippetPaletteToggle = document.getElementById('tvi-html-snippet-palette-toggle');
    const htmlSnippetThemeName = document.getElementById('tvi-html-snippet-theme-name');
    const htmlSnippetPalette = document.getElementById('tvi-html-snippet-palette');
    const htmlSnippetColors = document.getElementById('tvi-html-snippet-colors');
    const htmlSnippetColorImport = document.getElementById('tvi-html-snippet-color-import');
    const htmlComponentImportInput = document.getElementById('tvi-html-component-import-input');
    const presetsModal = document.getElementById('tvi-presets-modal');
    const presetsList = document.getElementById('tvi-presets-list');
    const bundledPresetsList = document.getElementById('tvi-bundled-presets-list');
    const presetImportInput = document.getElementById('tvi-preset-import-input');
    const nodeBundleImportInput = document.getElementById('tvi-node-bundle-import-input');
    const nodeExportModal = document.getElementById('tvi-node-export-modal');
    const nodeExportName = document.getElementById('tvi-node-export-name');
    const nodeExportSummary = document.getElementById('tvi-node-export-summary');
    const projectsModal = document.getElementById('tvi-projects-modal');
    const projectsList = document.getElementById('tvi-projects-list');
    const projectsCount = document.getElementById('tvi-projects-count');
    const projectNameInput = document.getElementById('tvi-project-name');
    const overwriteModal = document.getElementById('tvi-overwrite-modal');
    const overwriteMessage = document.getElementById('tvi-overwrite-message');
    const packageExportModal = document.getElementById('tvi-package-export-modal');
    const packageIncludeAssets = document.getElementById('tvi-package-include-assets');
    const packageIncludeLibraries = document.getElementById('tvi-package-include-libraries');
    const graphContextBanner = document.getElementById('tvi-graph-context-banner');
    const graphContextType = document.getElementById('tvi-graph-context-type');
    const graphContextName = document.getElementById('tvi-graph-context-name');
    const settingsModal = document.getElementById('tvi-settings-modal');
    const aboutModal = document.getElementById('tvi-about-modal');
    const codeThemeSelect = document.getElementById('tvi-code-theme');
    const codeWordWrapSelect = document.getElementById('tvi-code-word-wrap');
    const contextMenu = document.getElementById('tvi-context-menu');
    const migrationOutputModal = document.getElementById('tvi-migration-output-modal');
    const migrationOutputTitle = document.getElementById('tvi-migration-output-title');
    const migrationOutputSummary = document.getElementById('tvi-migration-output-summary');
    const migrationOutput = document.getElementById('tvi-migration-output');
    const addModelDefaultsButton = document.getElementById('tvi-add-model-defaults');
    const managerEditorModal = document.getElementById('tvi-manager-editor-modal');
    const managerEditorTitle = document.getElementById('tvi-manager-editor-title');
    const managerEditorSummary = document.getElementById('tvi-manager-editor-summary');
    const managerEditorBody = document.getElementById('tvi-manager-editor-body');
    const panJoystick = document.getElementById('tvi-pan-joystick');
    const formThemeModal = document.getElementById('tvi-form-theme-modal');
    const formThemeList = document.getElementById('tvi-form-theme-list');
    const formThemePreview = document.getElementById('tvi-form-theme-preview');
    const formThemePreviewName = document.getElementById('tvi-form-theme-preview-name');
    const formThemePreviewDescription = document.getElementById('tvi-form-theme-preview-description');
    const formThemePalette = document.getElementById('tvi-form-theme-palette');
    const formThemeColors = document.getElementById('tvi-form-theme-colors');
    const formThemeColorImport = document.getElementById('tvi-form-theme-color-import');
    const marketplaceModal = document.getElementById('tvi-marketplace-modal');
    const marketplaceLayout = document.getElementById('tvi-marketplace-layout');
    const marketplacePackageImport = document.getElementById('tvi-marketplace-package-import');
    const marketplaceSearch = document.getElementById('tvi-marketplace-search');
    const marketplaceList = document.getElementById('tvi-marketplace-list');
    const marketplaceReadonlyNote = document.getElementById('tvi-marketplace-readonly-note');
    const marketplaceThemeEditor = document.getElementById('tvi-marketplace-theme-editor');
    const marketplaceSnippetEditor = document.getElementById('tvi-marketplace-snippet-editor');
    const marketplaceId = document.getElementById('tvi-marketplace-id');
    const marketplaceName = document.getElementById('tvi-marketplace-name');
    const marketplaceVersion = document.getElementById('tvi-marketplace-version');
    const marketplaceAuthor = document.getElementById('tvi-marketplace-author');
    const marketplaceWebsite = document.getElementById('tvi-marketplace-website');
    const marketplaceLicense = document.getElementById('tvi-marketplace-license');
    const marketplaceCategory = document.getElementById('tvi-marketplace-category');
    const marketplaceTags = document.getElementById('tvi-marketplace-tags');
    const marketplaceOrder = document.getElementById('tvi-marketplace-order');
    const marketplaceDescription = document.getElementById('tvi-marketplace-description');
    const marketplaceDefaultPalette = document.getElementById('tvi-marketplace-default-palette');
    const marketplacePalettes = document.getElementById('tvi-marketplace-palettes');
    const marketplacePaletteManager = document.getElementById('tvi-marketplace-palette-manager');
    const marketplacePaletteList = document.getElementById('tvi-marketplace-palette-list');
    const marketplacePaletteName = document.getElementById('tvi-marketplace-palette-name');
    const marketplacePaletteId = document.getElementById('tvi-marketplace-palette-id');
    const marketplacePaletteDefault = document.getElementById('tvi-marketplace-palette-default');
    const marketplacePaletteColors = document.getElementById('tvi-marketplace-palette-colors');
    const marketplacePreviewHtml = document.getElementById('tvi-marketplace-preview-html');
    const marketplaceThemeCss = document.getElementById('tvi-marketplace-theme-css');
    const marketplaceThemeJs = document.getElementById('tvi-marketplace-theme-js');
    const marketplaceComponentSelect = document.getElementById('tvi-marketplace-component-select');
    const marketplaceComponentCode = document.getElementById('tvi-marketplace-component-code');
    const marketplaceComponentTokens = document.getElementById('tvi-marketplace-component-tokens');
    const marketplaceSnippetHtml = document.getElementById('tvi-marketplace-snippet-html');
    const marketplaceSnippetCss = document.getElementById('tvi-marketplace-snippet-css');
    const marketplaceSnippetJs = document.getElementById('tvi-marketplace-snippet-js');
    const marketplacePreview = document.getElementById('tvi-marketplace-preview');
    const marketplaceValidation = document.getElementById('tvi-marketplace-validation');
    const marketplaceSourceBadge = document.getElementById('tvi-marketplace-source-badge');
    const marketplaceCopyButton = document.getElementById('tvi-marketplace-copy');
    const marketplaceDeleteButton = document.getElementById('tvi-marketplace-delete');
    const marketplaceExportButton = document.getElementById('tvi-marketplace-export');
    const marketplaceSaveButton = document.getElementById('tvi-marketplace-save');

    const state = {
        project: blankProject(),
        activeGraphId: 'main',
        selectedNodeIds: [],
        selectedEdgeId: null,
        connecting: null,
        interaction: null,
        spacePressed: false,
        history: [],
        historyIndex: -1,
        dirty: false,
        preview: [],
        previewDirectory: '',
        previewFileIndex: -1,
        previewContext: null,
        changeBase: null,
        historyTimer: null,
        codeEditor: null,
        methodEditor: null,
        methodBrowser: null,
        paletteInsertCount: 0,
        paginationTemplateSourceNodeId: null,
        openMenu: null,
        projectDialogMode: 'load',
        previewEditor: null,
        fullCodeEditor: null,
        codeTheme: localStorage.getItem('tvi_code_theme') || 'thunder-dark',
        codeWordWrap: localStorage.getItem('tvi_code_word_wrap') === '1',
        contextTarget: null,
        contextPoint: null,
        libraryInsertPoint: null,
        nodeClipboard: loadNodeClipboard(),
        clipboardPasteCount: 0,
        pendingOverwriteAction: null,
        pendingOverwriteProjectKey: null,
        overwriteApprovedProjects: new Set(),
        htmlDesigner: null,
        htmlEditors: null,
        htmlPreviewTimer: null,
        htmlPreviewWidth: localStorage.getItem('tvi_html_preview_width') || 'auto',
        htmlSnippetCategory: 'All',
        htmlSnippetPage: 1,
        htmlSnippetPerPage: 10,
        htmlSnippetSelectedId: null,
        htmlSnippetPaletteEditor: null,
        htmlSnippetPaletteMinimized: localStorage.getItem('tvi_html_snippet_palette_minimized') === '1',
        graphSelections: {},
        htmlPanelPreviousJsCollapsed: true,
        nodeLibraryHidden: localStorage.getItem('tvi_node_library_hidden') === '1',
        inspectorHidden: localStorage.getItem('tvi_inspector_hidden') === '1',
        pendingNodeBundle: null,
        nodeBundleInsertPoint: null,
        managerEditor: null,
        panJoystick: null,
        formThemeEditor: null,
        formThemeImportMode: 'full',
        marketplaceStudio: null,
        marketplacePreviewTimer: null,
        methodPointerDrag: null,
        suppressMethodBrowserClickUntil: 0
    };

    function blankProject() {
        return {
            schema_version: 4,
            generated_by: 'Thunder Visual IDE 0.12.97',
            active_graph_id: 'main',
            graphs: {
                main: { id: 'main', name: 'Plugin Architecture', kind: 'architecture', nodes: [], edges: [], viewport: { x: 90, y: 70, zoom: 1 } }
            }
        };
    }

    function uid(prefix = 'id') {
        return window.crypto?.randomUUID ? `${prefix}-${crypto.randomUUID()}` : `${prefix}-${Date.now().toString(36)}-${Math.random().toString(36).slice(2, 8)}`;
    }

    function clone(value) { return JSON.parse(JSON.stringify(value)); }
    function activeGraph() { return state.project.graphs[state.activeGraphId]; }
    function mainGraph() { return state.project.graphs.main; }
    function selectedNodes() { const ids = new Set(state.selectedNodeIds); return activeGraph().nodes.filter(n => ids.has(n.id)); }
    function nodeById(id, graphId = state.activeGraphId) { return state.project.graphs[graphId]?.nodes.find(n => n.id === id) || null; }
    function defFor(nodeOrType) { return definitions[typeof nodeOrType === 'string' ? nodeOrType : nodeOrType?.type] || null; }

    function init() {
        enhanceStaticIconButtons();
        bindEvents();
        setupCodeEditors();
        const saved = loadSaved();
        state.project = saved || blankProject();
        normalizeProject();
        applyWorkspacePanelVisibility();
        state.activeGraphId = state.project.active_graph_id && state.project.graphs[state.project.active_graph_id] ? state.project.active_graph_id : 'main';
        resetHistory();
        renderAll();
        setStatus(saved ? 'Restored saved schema v4 project' : 'New blank project');
    }

    function normalizeProject() {
        if (!state.project || state.project.schema_version !== 4 || typeof state.project.graphs !== 'object') state.project = blankProject();
        if (!state.project.graphs.main) state.project.graphs.main = blankProject().graphs.main;
        for (const [graphId, graph] of Object.entries(state.project.graphs)) {
            graph.id = graph.id || graphId;
            graph.name = graph.name || (graphId === 'main' ? 'Plugin Architecture' : 'Execution Flow');
            graph.kind = graph.kind || (graphId === 'main' ? 'architecture' : 'flow');
            graph.nodes = Array.isArray(graph.nodes) ? graph.nodes : [];
            graph.edges = Array.isArray(graph.edges) ? graph.edges : [];
            graph.viewport = graph.viewport || { x: 80, y: 60, zoom: 1 };
            graph.hidden_node_ids = Array.isArray(graph.hidden_node_ids) ? graph.hidden_node_ids : [];
            graph.viewport.zoom = clamp(Number(graph.viewport.zoom) || 1, .25, 2.2);
            graph.viewport.x = Number(graph.viewport.x) || 0;
            graph.viewport.y = Number(graph.viewport.y) || 0;
            for (const n of graph.nodes) {
                const def = defFor(n);
                n.data = { ...(def?.defaults || {}), ...(n.data || {}) };
                n.x = Number(n.x) || 0; n.y = Number(n.y) || 0;
                if (n.type === 'visual.folder') n.data.member_ids = Array.isArray(n.data.member_ids) ? n.data.member_ids : [];
                if (def?.nested_graph && !n.data.graph_id) createNestedGraph(n, false);
                if (def?.nested_graph && n.data.graph_id && state.project.graphs[n.data.graph_id]) {
                    state.project.graphs[n.data.graph_id].kind = def.nested_graph_kind || 'flow';
                    state.project.graphs[n.data.graph_id].name = state.project.graphs[n.data.graph_id].name || `${nodeTitle(n)} ${def.nested_graph_label || 'Flow'}`;
                }
                if (['lifecycle.view','lifecycle.reusable_view'].includes(n.type) && n.data.javascript_graph_id && state.project.graphs[n.data.javascript_graph_id]) {
                    state.project.graphs[n.data.javascript_graph_id].kind = 'javascript';
                    state.project.graphs[n.data.javascript_graph_id].owner_node_id = n.id;
                    state.project.graphs[n.data.javascript_graph_id].name = state.project.graphs[n.data.javascript_graph_id].name || `${nodeTitle(n)} JavaScript Flow`;
                }
            }
            const ids = new Set(graph.nodes.map(n => n.id));
            graph.edges = graph.edges.filter(e => ids.has(e.from) && ids.has(e.to));
        }
        synchronizeControllerFlowStructures(true);
        synchronizeAllLifecyclePriorities(false);
    }

    function enhanceStaticIconButtons() {
        document.querySelectorAll('button').forEach(button => {
            if (button.textContent.trim() !== '×') return;
            const action = String(button.dataset.action || 'close').replace(/[-_]+/g, ' ').trim();
            const label = action ? action.charAt(0).toUpperCase() + action.slice(1) : 'Close';
            button.classList.add('tvi-icon-button');
            setIconButton(button, 'close', label);
        });
    }

    function bindEvents() {
        document.addEventListener('click', actionClick);
        search.addEventListener('input', renderPalette);
        palette.addEventListener('dragstart', e => {
            const item = e.target.closest('[data-node-type]'); if (!item) return;
            e.dataTransfer.setData('application/x-thunder-node', item.dataset.nodeType); e.dataTransfer.effectAllowed = 'copy';
        });
        palette.addEventListener('dblclick', e => { const item = e.target.closest('[data-node-type]'); if (item) addNodeAtCenter(item.dataset.nodeType); });
        librarySearch.addEventListener('input', renderLibraryModal);
        libraryList.addEventListener('dblclick', e => { if (e.target.closest('button')) return; const item = e.target.closest('[data-node-type]'); if (item) addNodeFromLibrary(item.dataset.nodeType); });
        libraryList.addEventListener('click', e => { const button = e.target.closest('[data-add-node-type]'); if (button) addNodeFromLibrary(button.dataset.addNodeType); });
        methodsModal.addEventListener('click', methodModalClick);
        methodImplementation.addEventListener('change', updateMethodImplementationUi);
        presetsModal.addEventListener('click', presetModalClick);
        paginationTemplatesModal?.addEventListener('click', paginationTemplateModalClick);
        htmlSnippetSearch?.addEventListener('input', () => {
            state.htmlSnippetPage = 1;
            renderHtmlSnippets();
        });
        htmlDesignerName?.addEventListener('input', () => {
            refreshHtmlDesignerComponentOptions();
            scheduleHtmlPreview();
        });
        htmlSnippetsModal?.addEventListener('click', htmlSnippetsModalClick);
        htmlSnippetTabs?.addEventListener('change', () => {
            state.htmlSnippetCategory = htmlSnippetTabs.value || 'All';
            state.htmlSnippetPage = 1;
            renderHtmlSnippets();
        });
        htmlSnippetPalette?.addEventListener('change', htmlSnippetPaletteChange);
        htmlSnippetColors?.addEventListener('input', htmlSnippetColorInput);
        htmlSnippetColorImport?.addEventListener('change', importHtmlSnippetColors);
        document.addEventListener('fullscreenchange', syncHtmlSnippetPreviewFullscreenState);
        document.addEventListener('webkitfullscreenchange', syncHtmlSnippetPreviewFullscreenState);
        htmlComponentImportInput?.addEventListener('change', importHtmlComponent);
        formThemeList?.addEventListener('click', formThemeListClick);
        formThemePalette?.addEventListener('change', formThemePaletteChange);
        formThemeColors?.addEventListener('input', formThemeColorInput);
        formThemeColorImport?.addEventListener('change', importFormThemeColors);
        marketplaceModal?.addEventListener('click', marketplaceStudioClick);
        marketplaceModal?.addEventListener('input', marketplaceStudioInput);
        marketplaceModal?.addEventListener('change', marketplaceStudioInput);
        marketplaceSearch?.addEventListener('input', renderMarketplaceAssetList);
        marketplacePackageImport?.addEventListener('change', importMarketplacePackage);
        presetImportInput.addEventListener('change', importPresets);
        nodeBundleImportInput?.addEventListener('change', importNodeBundleFile);
        projectsModal.addEventListener('click', projectModalClick);
        samplesModal.addEventListener('click', sampleModalClick);
        previewFiles?.addEventListener('click', previewBrowserClick);
        previewBreadcrumbs?.addEventListener('click', previewBrowserClick);
        previewUp?.addEventListener('click', () => navigatePreviewDirectory(previewParentDirectory(state.previewDirectory)));
        codeModalTextarea.addEventListener('keydown', codeEditorKeyDown);
        methodBody.addEventListener('keydown', codeEditorKeyDown);
        viewport.addEventListener('dragover', e => { if ([...e.dataTransfer.types].includes('application/x-thunder-node')) { e.preventDefault(); e.dataTransfer.dropEffect = 'copy'; } });
        viewport.addEventListener('drop', e => { const type = e.dataTransfer.getData('application/x-thunder-node'); if (!definitions[type]) return; e.preventDefault(); const p = screenToWorld(e.clientX, e.clientY); addNode(type, p.x - 110, p.y - 40); });
        viewport.addEventListener('wheel', handleWheel, { passive: false });
        viewport.addEventListener('pointerdown', viewportPointerDown);
        viewport.addEventListener('selectstart', preventCanvasSelection, true);
        viewport.addEventListener('dragstart', preventCanvasNativeDrag, true);
        nodesLayer.addEventListener('pointerdown', nodePointerDown);
        nodesLayer.addEventListener('click', nodeClick);
        nodesLayer.addEventListener('dblclick', nodeDoubleClick);
        nodesLayer.addEventListener('contextmenu', nodeContextMenu);
        svg.addEventListener('click', edgeClick);
        svg.addEventListener('dblclick', e => { const t = e.target.closest('[data-edge-id]'); if (t) { e.preventDefault(); removeEdge(t.dataset.edgeId); } });
        svg.addEventListener('contextmenu', edgeContextMenu);
        // Suppress the browser menu at the window capture phase so SVG, node,
        // empty-canvas, and dynamically rendered descendants are all covered.
        window.addEventListener('contextmenu', suppressNativeCanvasContextMenu, true);
        viewport.addEventListener('contextmenu', canvasContextMenu);
        contextMenu.addEventListener('click', contextMenuClick);
        codeThemeSelect.addEventListener('change', previewSelectedSettings);
        codeWordWrapSelect?.addEventListener('change', previewSelectedSettings);
        inspector.addEventListener('input', inspectorInput);
        inspector.addEventListener('change', inspectorInput);
        inspector.addEventListener('click', inspectorClick);
        inspector.addEventListener('keydown', editableSelectKeyDown);
        document.addEventListener('pointerdown', event => {
            if (!event.target.closest?.('[data-editable-select]')) closeEditableSelectMenus();
        });
        managerEditorBody?.addEventListener('input', inspectorInput);
        managerEditorBody?.addEventListener('change', inspectorInput);
        managerEditorBody?.addEventListener('click', inspectorClick);
        methodBrowserSearch?.addEventListener('input', renderMethodBrowser);
        methodBrowserList?.addEventListener('click', methodBrowserListClick);
        methodBrowserList?.addEventListener('dblclick', methodBrowserListDoubleClick);
        methodBrowserList?.addEventListener('dragstart', methodBrowserListDragStart);
        methodBrowserList?.addEventListener('pointerdown', methodBrowserPointerDown);
        methodBrowserChainList?.addEventListener('click', methodChainListClick);
        methodBrowserChainList?.addEventListener('dragstart', methodChainDragStart);
        methodBrowserChainList?.addEventListener('dragover', methodChainDragOver);
        methodBrowserChainList?.addEventListener('dragleave', methodChainDragLeave);
        methodBrowserChainList?.addEventListener('drop', methodChainDrop);
        methodBrowserDetail?.addEventListener('click', methodBrowserDetailClick);
        methodBrowserDetail?.addEventListener('input', methodBrowserDetailInput);
        methodBrowserDetail?.addEventListener('change', methodBrowserDetailInput);
        panJoystick?.addEventListener('pointerdown', startPanJoystick);
        panJoystick?.addEventListener('keydown', panJoystickKeyDown);
        importInput.addEventListener('change', importProject);
        packageImportInput.addEventListener('change', importProjectPackage);
        ideDataImportInput.addEventListener('change', importAllIdeData);
        window.addEventListener('keydown', keyDown);
        window.addEventListener('keyup', keyUp);
        window.addEventListener('pointermove', methodBrowserPointerMove, { passive: false });
        window.addEventListener('pointerup', methodBrowserPointerUp, true);
        window.addEventListener('pointercancel', methodBrowserPointerCancel, true);
        window.addEventListener('blur', () => { state.spacePressed = false; viewport.classList.remove('is-pan-ready'); stopPanJoystick(); });
        window.addEventListener('resize', () => { hideContextMenu(); requestAnimationFrame(renderEdges); });
        window.addEventListener('scroll', hideContextMenu, true);
    }

    function actionClick(e) {
        if (!e.target.closest('#tvi-context-menu')) hideContextMenu();
        const menuToggle = e.target.closest('[data-menu-toggle]');
        if (menuToggle) {
            e.preventDefault();
            toggleMenu(menuToggle.dataset.menuToggle);
            return;
        }
        if (!e.target.closest('.tvi-menu')) closeMenus();
        const actionEl = e.target.closest('[data-action]'); if (!actionEl) return;
        const actions = {
            new: newProject, sample: openSamples, 'close-samples': closeSamples, save: saveNamedProject, 'save-as': () => openProjects('save'), 'load-project': () => openProjects('load'),
            'save-browser': saveBrowserRecovery, import: () => importInput.click(), export: exportProject,
            'import-project-package': () => packageImportInput.click(),
            'open-project-package-export': openProjectPackageExport,
            'close-project-package-export': closeProjectPackageExport,
            'export-project-package': exportProjectPackage,
            'export-all-ide-data': exportAllIdeData, 'import-all-ide-data': () => ideDataImportInput.click(), 'clear-all-ide-data': clearAllIdeData,
            'reload-definitions': () => location.reload(), undo, redo, 'back-graph': () => openGraph('main'),
            'group-selected': groupSelected, 'copy-selected': copySelectedNodes, 'paste-nodes': () => pasteCopiedNodes(),
            'zoom-in': () => zoomBy(1.15), 'zoom-out': () => zoomBy(.87), 'zoom-reset': resetZoom,
            'fit-all': fitAllNodes, 'show-all': showAllNodes,
            'toggle-node-library': toggleNodeLibrary, 'toggle-inspector': toggleInspector,
            validate: () => apiAction('validate'), preview: () => apiAction('preview'), build: () => apiAction('build'),
            'test-plugin': () => testPlugin(false, true), 'update-test-plugin': () => testPlugin(false, false),
            'migration-run': () => migrationAction('migration-run'), 'migration-rollback': () => migrationAction('migration-rollback'), 'migration-status': () => migrationAction('migration-status'),
            'close-preview': closePreview,
            'open-library': openLibrary, 'close-library': closeLibrary,
            'open-pagination-templates': openPaginationTemplates, 'close-pagination-templates': closePaginationTemplates,
            'open-html-designer': openHtmlDesigner, 'close-html-designer': closeHtmlDesigner,
            'open-marketplace-studio': openMarketplaceStudio, 'close-marketplace-studio': closeMarketplaceStudio,
            'new-marketplace-asset': newMarketplaceAsset,
            'copy-marketplace-asset': copyMarketplaceAsset,
            'save-marketplace-asset': saveMarketplaceAsset,
            'delete-marketplace-asset': deleteMarketplaceAsset,
            'export-marketplace-asset': exportMarketplaceAsset,
            'import-marketplace-package': () => marketplacePackageImport?.click(),
            'reset-marketplace-palette': resetMarketplacePalette,
            'open-html-snippets': openHtmlSnippets, 'close-html-snippets': closeHtmlSnippets,
            'toggle-html-snippet-preview-fullscreen': toggleHtmlSnippetPreviewFullscreen,
            'toggle-html-snippet-palette': toggleHtmlSnippetPalette,
            'reset-html-snippet-colors': resetHtmlSnippetColors,
            'export-html-snippet-colors': exportHtmlSnippetColors,
            'import-html-snippet-colors': () => htmlSnippetColorImport?.click(),
            'apply-html-snippet-colors': applyHtmlSnippetColors,
            'save-html-designer': saveHtmlDesigner, 'clear-html-designer': clearHtmlDesigner, 'toggle-html-preview': toggleHtmlPreview,
            'set-html-preview-width': () => setHtmlPreviewWidth(actionEl.dataset.previewWidth),
            'insert-html-component': insertHtmlComponentMarker,
            'toggle-html-panel': () => toggleHtmlPanel(actionEl.dataset.htmlPanel), 'toggle-html-js': toggleHtmlJsPanel,
            'export-html-component': exportHtmlComponent, 'import-html-component': () => htmlComponentImportInput?.click(),
            'save-html-component-preset': saveHtmlComponentPreset,
            'save-preset': savePreset, 'open-presets': openPresets, 'close-presets': closePresets,
            'export-selected-node-file': openNodeBundleExport, 'import-node-file': () => { state.nodeBundleInsertPoint=null; nodeBundleImportInput?.click(); },
            'export-presets': exportPresets, 'import-presets': () => presetImportInput.click(),
            'close-node-export': closeNodeBundleExport, 'confirm-node-export': confirmNodeBundleExport,
            'close-code-editor': closeCodeEditor, 'save-code-editor': saveCodeEditor,
            'close-methods': closeMethods, 'new-method': newMethod, 'save-method': saveMethod, 'add-model-defaults': addModelDefaults,
            'add-method-param': addMethodParam, 'open-method-flow': openMethodFlow,
            'close-method-browser': closeMethodBrowser, 'confirm-method-browser': confirmMethodBrowser,
            'clear-method-chain': clearMethodChain, 'create-method-chain': createMethodChain,
            'close-projects': closeProjects, 'refresh-projects': refreshProjects, 'confirm-save-project-as': saveProjectAs,
            'cancel-test-overwrite': cancelTestOverwrite, 'confirm-test-overwrite': confirmTestOverwrite,
            'open-settings': openSettings, 'close-settings': closeSettings, 'save-settings': saveSettings, 'open-about': openAbout, 'close-about': closeAbout, 'close-migration-output': closeMigrationOutput,
            'close-manager-editor': closeManagerEditor,
            'close-form-theme': closeFormTheme, 'apply-form-theme': applyFormTheme,
            'reset-form-theme-colors': resetFormThemeColors,
            'export-form-theme-colors': exportFormThemeColors,
            'import-form-theme-colors': () => { state.formThemeImportMode = 'full'; formThemeColorImport?.click(); },
            'import-form-theme-colors-only': () => { state.formThemeImportMode = 'colors-only'; formThemeColorImport?.click(); }
        };
        if (actions[actionEl.dataset.action]) { e.preventDefault(); closeMenus(); actions[actionEl.dataset.action](); }
    }

    function toggleMenu(name) {
        const next = state.openMenu === name ? null : name;
        state.openMenu = next;
        document.querySelectorAll('[data-menu-panel]').forEach(panel => { panel.hidden = panel.dataset.menuPanel !== next; });
        document.querySelectorAll('[data-menu-toggle]').forEach(button => button.classList.toggle('is-open', button.dataset.menuToggle === next));
    }
    function closeMenus() {
        if (!state.openMenu) return;
        state.openMenu = null;
        document.querySelectorAll('[data-menu-panel]').forEach(panel => { panel.hidden = true; });
        document.querySelectorAll('[data-menu-toggle]').forEach(button => button.classList.remove('is-open'));
    }

    function toggleNodeLibrary() {
        state.nodeLibraryHidden = !state.nodeLibraryHidden;
        localStorage.setItem('tvi_node_library_hidden', state.nodeLibraryHidden ? '1' : '0');
        applyWorkspacePanelVisibility();
    }

    function toggleInspector() {
        state.inspectorHidden = !state.inspectorHidden;
        localStorage.setItem('tvi_inspector_hidden', state.inspectorHidden ? '1' : '0');
        applyWorkspacePanelVisibility();
    }

    function applyWorkspacePanelVisibility() {
        if (!mainLayout) return;
        mainLayout.classList.toggle('is-node-library-hidden', state.nodeLibraryHidden);
        mainLayout.classList.toggle('is-inspector-hidden', state.inspectorHidden);
        if (nodeLibraryPanel) nodeLibraryPanel.hidden = state.nodeLibraryHidden;
        if (inspectorPanel) inspectorPanel.hidden = state.inspectorHidden;
        updatePanelToggle(nodeLibraryToggle, !state.nodeLibraryHidden, 'node library');
        updatePanelToggle(inspectorToggle, !state.inspectorHidden, 'inspector');
        requestAnimationFrame(() => {
            renderEdges();
            state.codeEditor?.refresh?.();
        });
    }

    function updatePanelToggle(button, visible, label) {
        if (!button) return;
        const action = visible ? 'Hide' : 'Show';
        button.title = `${action} ${label}`;
        button.setAttribute('aria-label', `${action} ${label}`);
        button.setAttribute('aria-pressed', visible ? 'true' : 'false');
        button.classList.toggle('is-panel-hidden', !visible);
    }

    function renderAll() {
        applyViewport(); renderPalette(); renderNodes(); renderEdges(); renderInspector(); renderMeta();
        if (state.managerEditor && !managerEditorModal?.hidden) renderManagerEditor();
    }

    function availableDefinitions(query = '') {
        const graph = activeGraph(); if (!graph) return {};
        const q = String(query).trim().toLowerCase();
        const groups = {};
        for (const [type, def] of Object.entries(definitions)) {
            if (def.internal) continue;
            if (!(def.allowed_graphs || []).includes(graph.kind)) continue;
            if (def.singleton && graph.nodes.some(n => n.type === type)) continue;
            const text = `${def.label} ${def.description} ${def.category}`.toLowerCase(); if (q && !text.includes(q)) continue;
            (groups[def.category || 'Other'] ||= []).push({ type, def });
        }
        return groups;
    }

    function renderPalette() {
        const groups = availableDefinitions(search.value);
        palette.innerHTML = Object.entries(groups).map(([group, items]) => `<section class="tvi-palette-group"><h3>${esc(group)}</h3>${items.map(({type,def}) => `<div class="tvi-palette-item" draggable="true" data-node-type="${attr(type)}"><span class="tvi-palette-icon" style="--node-color:${attr(def.color || '#64748b')}">${esc(def.icon || '•')}</span><div><strong>${esc(def.label)}</strong><small>${esc(def.description || '')}</small></div></div>`).join('')}</section>`).join('') || '<p class="tvi-muted">No nodes match this graph or search.</p>';
    }

    function renderLibraryModal() {
        const groups = availableDefinitions(librarySearch.value);
        libraryList.innerHTML = Object.entries(groups).map(([group, items]) => `<section class="tvi-library-group"><h3>${esc(group)}</h3><div>${items.map(({type,def}) => `<article class="tvi-library-item" data-node-type="${attr(type)}" style="--node-color:${attr(def.color || '#64748b')}"><span class="tvi-palette-icon">${esc(def.icon || '•')}</span><div><strong>${esc(def.label)}</strong><small>${esc(def.description || '')}</small></div><button type="button" class="tvi-icon-button" data-add-node-type="${attr(type)}" title="Add ${attr(def.label)}" aria-label="Add ${attr(def.label)}">${iconMarkup('plus')}</button></article>`).join('')}</div></section>`).join('') || '<p class="tvi-muted">No nodes match this graph or search.</p>';
    }

    function renderNodes() {
        const graph = activeGraph(); const hidden = hiddenNodeIds(graph);
        nodesLayer.innerHTML = graph.nodes.filter(n => !hidden.has(n.id)).map(n => nodeHtml(n)).join('');
        requestAnimationFrame(renderEdges);
    }

    function nodeHtml(node) {
        const def = defFor(node) || { label: node.type, color: '#64748b', icon: '?' };
        const selected = state.selectedNodeIds.includes(node.id);
        const ports = getPorts(node);
        const inputPorts = ports.inputs || [], outputPorts = ports.outputs || [];
        const isFolder = node.type === 'visual.folder';
        const viewOrderInfo = viewSiblingInfo(node);
        const lifecycleInfo = lifecycleOrderInfo(node);
        const orderBadge = viewOrderInfo
            ? `<span class="tvi-node-order" title="View output order follows vertical canvas position">${viewOrderInfo.index + 1}</span>`
            : lifecycleInfo
                ? `<span class="tvi-node-order tvi-node-order--hook" title="${attr(lifecycleInfo.title)}">${esc(lifecycleInfo.badge)}</span>`
                : '';
        const protectedNode = isProtectedNode(node);
        const mutedNode = nodeIsMuted(node);
        const chainableMethod = methodIsChainable(selectedMethodMeta(node));
        const chainBadge = chainableMethod ? '<span class="tvi-chainable-badge" title="This method can continue a method chain">CHAIN</span>' : '';
        const mutedBadge = mutedNode ? '<span class="tvi-muted-badge" title="The compiler ignores this node">MUTED</span>' : '';
        return `<article class="tvi-node ${selected ? 'is-selected' : ''} ${isFolder ? 'is-folder' : ''} ${protectedNode ? 'is-protected' : ''} ${chainableMethod ? 'is-chainable' : ''} ${mutedNode ? 'is-muted' : ''}" data-node-id="${attr(node.id)}" style="transform:translate(${node.x}px,${node.y}px);--node-color:${attr(def.color || '#64748b')}">
            <header class="tvi-node__head"><span class="tvi-node__icon">${esc(def.icon || '•')}</span><div><strong>${esc(nodeTitle(node))}</strong><small>${esc(def.label)}</small></div>${mutedBadge}${chainBadge}${orderBadge}${protectedNode ? `<span class="tvi-node-lock" title="Required by this execution graph" aria-label="Required node">◆</span>` : ''}${isFolder ? `<button type="button" class="tvi-folder-toggle" data-folder-toggle="${attr(node.id)}">${node.data.collapsed ? '+' : '−'}</button>` : ''}</header>
            <div class="tvi-node__body"><div class="tvi-node__ports tvi-node__ports--input">${inputPorts.map(p => portHtml(node,p)).join('')}</div><div class="tvi-node__summary">${esc(nodeSummary(node))}${namedVariableBadges(node)}${def.nested_graph ? `<em>Double-click to open ${esc(String(def.nested_graph_label || (def.nested_graph_kind === 'view' ? 'view builder' : 'flow')).toLowerCase())}</em>` : ''}</div><div class="tvi-node__ports tvi-node__ports--output">${outputPorts.map(p => portHtml(node,p)).join('')}</div></div>
        </article>`;
    }

    function phpVariableName(value) {
        let name = String(value || '').trim().replace(/^\$+/, '').replace(/[^A-Za-z0-9_]+/g, '_');
        if (/^[0-9]/.test(name)) name = '_' + name;
        return name;
    }

    function namedVariableBadges(node) {
        const data = node.data || {};
        const variables = [];
        for (const key of ['result_variable', 'validator_variable', 'errors_variable']) {
            const name = phpVariableName(data[key]);
            if (name && !variables.includes(name)) variables.push(name);
        }
        if (!variables.length) return '';
        return `<span class="tvi-node__variables">${variables.map(name => `<code>$${esc(name)}</code>`).join('')}</span>`;
    }

    function portHtml(node, p) {
        return `<button type="button" class="tvi-port tvi-port--${attr(p.direction)} tvi-port-type-${attr(p.type)}" data-node-id="${attr(node.id)}" data-port-id="${attr(p.id)}" data-port-direction="${attr(p.direction)}" title="${attr(p.label)} · ${attr(p.type)}"><span>${esc(p.label)}</span></button>`;
    }

    function renderEdges() {
        const graph = activeGraph(); const hidden = hiddenNodeIds(graph); const selectedSet = new Set(state.selectedNodeIds);
        const parts = [];
        for (const edge of graph.edges) {
            if (hidden.has(edge.from) || hidden.has(edge.to)) continue;
            const fromEl = portElement(edge.from, edge.from_port, 'out'); const toEl = portElement(edge.to, edge.to_port, 'in');
            if (!fromEl || !toEl) continue;
            const a = portWorldCenter(fromEl), b = portWorldCenter(toEl); const dx = Math.max(55, Math.abs(b.x-a.x)*.45);
            const d = `M ${a.x} ${a.y} C ${a.x+dx} ${a.y}, ${b.x-dx} ${b.y}, ${b.x} ${b.y}`;
            const selected = state.selectedEdgeId === edge.id;
            const muted = nodeIsMuted(nodeById(edge.from)) || nodeIsMuted(nodeById(edge.to));
            const type = edge.port_type || edgeType(edge);
            parts.push(`<g class="tvi-edge ${selected ? 'is-selected' : ''} ${muted ? 'is-muted' : ''} tvi-edge-type-${attr(type)}" data-edge-id="${attr(edge.id)}"><path class="tvi-edge-hit" d="${d}"/><path class="tvi-edge-line" d="${d}"/></g>`);
            if (selectedSet.has(edge.from) || selectedSet.has(edge.to)) {
                const mx=(a.x+b.x)/2,my=(a.y+b.y)/2;
                parts.push(`<g class="tvi-edge-delete" data-edge-id="${attr(edge.id)}" transform="translate(${mx},${my})"><circle r="10"></circle><text text-anchor="middle" dominant-baseline="central">×</text></g>`);
            }
        }
        svg.innerHTML = parts.join('');
        svg.querySelectorAll('.tvi-edge-delete').forEach(el => el.addEventListener('click', e => { e.stopPropagation(); removeEdge(el.dataset.edgeId); }));
    }

    function renderInspector() {
        const graph = activeGraph();
        if (state.selectedEdgeId) {
            const edge = graph.edges.find(e => e.id === state.selectedEdgeId);
            inspector.innerHTML = edge ? `<div class="tvi-inspector-card"><h3>Connection</h3><p><code>${esc(edge.from_port)}</code> → <code>${esc(edge.to_port)}</code></p><button type="button" class="tvi-danger" data-inspector-action="delete-edge">Delete connection</button></div>` : emptyInspector();
            return;
        }
        const nodes = selectedNodes(); if (!nodes.length) { inspector.innerHTML = emptyInspector(); return; }
        const node = nodes[0], def = defFor(node); if (!def) { inspector.innerHTML = emptyInspector(); return; }
        const fields = (def.properties || []).map(p => inspectorField(node,p)).join('');
        const multi = nodes.length > 1 ? `<div class="tvi-notice">${nodes.length} nodes selected. The inspector edits the first selected node.</div>` : '';
        const nested = def.nested_graph ? `<button type="button" data-inspector-action="open-flow">Open ${esc(def.nested_graph_label || (def.nested_graph_kind === 'view' ? 'View Builder' : 'Execution Graph'))}</button>` : '';
        const javascriptFlow = ['lifecycle.view','lifecycle.reusable_view'].includes(node.type) ? `<button type="button" data-inspector-action="open-javascript-flow">Open JavaScript Flow</button>` : '';
        const viewTools = node.type === 'lifecycle.view'
            ? viewComponentTools(node) + viewAssetTools(node)
            : (node.type === 'looks.look' ? lookFormThemeTools(node) : '');
        const folder = node.type === 'visual.folder' ? folderMembers(node) : '';
        const paginationPreview = node.type === 'view.pagination' ? paginationNodePreview(node) : '';
        const htmlDesignerTools = node.type === 'view.html_component' ? htmlComponentInspectorTools(node) : '';
        const viewPreset = graph.kind === 'view' && ['view.form','view.component','view.container','view.grid','view.pagination','view.html_component','view.html_boilerplate','view.chart'].includes(node.type) ? `<button type="button" data-inspector-action="save-view-tree-preset">Save This Visual Tree as Preset</button>` : '';
        const viewOrder = graph.kind === 'view' && String(node.type).startsWith('view.') ? viewOrderTools(node) : '';
        const lifecycleOrder = graph.kind === 'architecture' && ['lifecycle.controller', 'lifecycle.view'].includes(node.type)
            ? lifecycleOrderTools(node)
            : '';
        const methodInfo = dynamicMethodInfo(node, def);
        const methodBrowser = methodBrowserTools(node);
        const chartTools = node.type === 'charts.builder' ? chartBuilderTools(node) : '';
        inspector.innerHTML = `${multi}<div class="tvi-inspector-card"><div class="tvi-inspector-heading"><span style="--node-color:${attr(def.color || '#64748b')}">${esc(def.icon || '•')}</span><div><h3>${esc(def.label)}</h3><p>${esc(def.description || '')}</p></div></div>${viewOrder}${lifecycleOrder}${fields}${methodInfo}${methodBrowser}${chartTools}${nested}${javascriptFlow}${viewTools}${paginationPreview}${htmlDesignerTools}${viewPreset}${folder}<div class="tvi-inspector-actions"><button type="button" data-inspector-action="duplicate" ${isProtectedNode(node) ? 'disabled title="Required graph node"' : ''}>Duplicate</button><button type="button" class="tvi-danger" data-inspector-action="delete-node" ${isProtectedNode(node) ? 'disabled title="Required graph node"' : ''}>Delete</button></div><details><summary>Definition location</summary><code>nodes/${esc(def.package_path || '')}/node.json</code><p>Edit the actual node package files, then use Reload Nodes.</p></details></div>`;
    }

    function emptyInspector() { return '<div class="tvi-inspector-empty"><strong>No node selected</strong><p>Select a node, a connection, or open a nested flow or View Builder.</p></div>'; }

    function dynamicMethodInfo(node, def) {
        const methods = Array.isArray(def?.methods) ? def.methods : [];
        if (!methods.length || !def?.dynamic_ports || !String(def.dynamic_ports).endsWith('_method')) return '';
        const selected = methods.find(item => item?.name === node?.data?.method) || methods[0];
        if (!selected) return '';
        const group = String(selected.group || '').trim();
        const label = String(selected.label || selected.name || 'Selected method').trim();
        const description = String(selected.description || '').trim();
        const heading = group ? `${group} · ${label}` : label;
        return `<div class="tvi-notice tvi-method-info"><strong>${esc(heading)}</strong><code>${esc(methodSignature(selected))}</code>${description ? `<p>${esc(description)}</p>` : ''}</div>`;
    }

    function methodBrowserTools(node) {
        const target = methodBrowserSource(node);
        if (!target.supported) return '';
        const count = target.methods.length;
        const action = target.canUseSelected ? 'Browse Methods / Build Chain…' : 'Build Method Chain…';
        return `<div class="tvi-method-browser-tools"><strong>Method Browser & Chain Builder</strong><p>${count ? `${count} documented method${count === 1 ? '' : 's'} available from ${esc(target.name)}. Build a linear chain and create ordinary connected nodes in one step.` : 'Choose a model or class to see its available methods.'}</p><button type="button" data-inspector-action="open-method-browser" ${count ? '' : 'disabled'}>${action}</button></div>`;
    }

    function chartBuilderTools(node) {
        const count = clamp(parseInt(node?.data?.dataset_count) || 1, 1, 20);
        return `<div class="tvi-method-browser-tools tvi-chart-builder-tools"><strong>Datasets</strong><p>${count} dataset input${count === 1 ? '' : 's'} available. Add or remove ports without manually entering the count.</p><div class="tvi-inspector-actions"><button type="button" data-inspector-action="add-chart-dataset" ${count >= 20 ? 'disabled' : ''}>Add Dataset</button><button type="button" data-inspector-action="remove-chart-dataset" ${count <= 1 ? 'disabled' : ''}>Remove Dataset</button></div></div>`;
    }

    function pruneChartDatasetEdges(node, count) {
        if (!node || node.type !== 'charts.builder') return;
        activeGraph().edges = activeGraph().edges.filter(edge => {
            if (edge.to !== node.id || !String(edge.to_port || '').startsWith('dataset_')) return true;
            const index = Number(String(edge.to_port).slice(8));
            return !Number.isFinite(index) || index <= count;
        });
    }

    function adjustChartDatasetCount(delta) {
        const node = selectedNodes()[0];
        if (!node || node.type !== 'charts.builder') return;
        const current = clamp(parseInt(node.data.dataset_count) || 1, 1, 20);
        const next = clamp(current + delta, 1, 20);
        if (next === current) return;
        snapshot();
        node.data.dataset_count = next;
        if (next < current) pruneChartDatasetEdges(node, next);
        changed();
        renderAll();
    }

    function pruneIndexedInputEdges(node, prefix, count) {
        if (!node || !prefix) return;
        activeGraph().edges = activeGraph().edges.filter(edge => {
            if (edge.to !== node.id || !String(edge.to_port || '').startsWith(prefix)) return true;
            const index = Number(String(edge.to_port).slice(prefix.length));
            return !Number.isFinite(index) || index <= count;
        });
    }

    function pruneDynamicJavaScriptEdges(node, property, previous) {
        const before = Number(previous || 0);
        let count = before;
        let prefix = '';
        if (node.type === 'javascript.array' && property === 'item_count') {
            count = clamp(parseInt(node.data.item_count) || 0, 0, 30); prefix = 'item_';
        } else if (node.type === 'javascript.object' && property === 'property_count') {
            count = clamp(parseInt(node.data.property_count) || 0, 0, 30); prefix = 'property_';
        } else if (node.type === 'javascript.concatenate' && property === 'input_count') {
            count = clamp(parseInt(node.data.input_count) || 2, 2, 30); prefix = 'value_';
        } else if (node.type === 'javascript.code' && property === 'input_count') {
            count = clamp(parseInt(node.data.input_count) || 0, 0, 20); prefix = 'value_';
        }
        if (prefix && count < before) pruneIndexedInputEdges(node, prefix, count);
    }

    function frameworkMethodDescriptor(node) {
        const serviceMap = {
            'database.query_builder': { methodType: 'database.query_builder_method', methodProperty: 'method', objectInput: 'builder', objectOutput: 'builder', variable: '$db' },
            'session.session': { methodType: 'session.method', methodProperty: 'method', objectInput: 'session', objectOutput: 'session', variable: '$session' },
            'request.request': { methodType: 'request.method', methodProperty: 'method', objectInput: 'request', objectOutput: 'request', variable: '$request' },
            'images.image': { methodType: 'images.method', methodProperty: 'method', objectInput: 'image', objectOutput: 'image', variable: '$image' }
        };
        const methodMap = {
            'database.query_builder_method': serviceMap['database.query_builder'],
            'session.method': serviceMap['session.session'],
            'request.method': serviceMap['request.request'],
            'images.method': serviceMap['images.image']
        };
        if (serviceMap[node?.type]) return { ...serviceMap[node.type], isService: true };
        if (methodMap[node?.type]) return { ...methodMap[node.type], isService: false };
        return null;
    }

    function methodBrowserSource(node) {
        const framework = frameworkMethodDescriptor(node);
        if (framework) {
            const methodDef = definitions[framework.methodType] || {};
            const methods = Array.isArray(methodDef.methods) ? methodDef.methods : [];
            return {
                supported: true,
                source: null,
                property: framework.methodProperty,
                name: methodDef.label || nodeTitle(node),
                methods,
                callType: framework.methodType,
                methodProperty: framework.methodProperty,
                refData: {},
                objectInput: framework.objectInput,
                objectOutput: framework.objectOutput,
                anchorObjectOutput: framework.objectOutput,
                variable: framework.variable,
                isService: framework.isService,
                canUseSelected: !framework.isService
            };
        }
        if (node?.type === 'models.method_call') {
            const source = nodeById(node.data.model_node_id, 'main');
            return { supported: true, source, property: 'method_name', name: source ? nodeTitle(source) : 'the selected model', methods: normalizeMethods(parseJson(source?.data?.methods_json, [])), callType: 'models.method_call', methodProperty: 'method_name', refData: { model_node_id: source?.id || node.data.model_node_id || '' }, objectInput: 'model', objectOutput: 'model', anchorObjectOutput: 'model', variable: '$model', isService: false, canUseSelected: true };
        }
        if (node?.type === 'custom.class_instance') {
            const source = nodeById(node.data.class_node_id, 'main');
            return { supported: true, source, property: '', name: source ? nodeTitle(source) : 'the selected class', methods: normalizeMethods(parseJson(source?.data?.methods_json, [])).filter(method => method.name !== '__construct'), callType: 'custom.class_method_call', methodProperty: 'method_name', refData: { class_node_id: source?.id || node.data.class_node_id || '' }, objectInput: 'object', objectOutput: 'result', anchorObjectOutput: 'object', variable: '$object', isService: true, canUseSelected: false };
        }
        if (node?.type === 'custom.class_method_call') {
            const source = nodeById(node.data.class_node_id, 'main');
            return { supported: true, source, property: 'method_name', name: source ? nodeTitle(source) : 'the selected class', methods: normalizeMethods(parseJson(source?.data?.methods_json, [])).filter(method => method.name !== '__construct'), callType: 'custom.class_method_call', methodProperty: 'method_name', refData: { class_node_id: source?.id || node.data.class_node_id || '' }, objectInput: 'object', objectOutput: 'result', anchorObjectOutput: 'result', variable: '$object', isService: false, canUseSelected: true };
        }
        return { supported: false, source: null, property: 'method_name', name: 'the selected source', methods: [], callType: '', methodProperty: 'method_name', refData: {}, objectInput: '', objectOutput: '', variable: '$object', isService: false, canUseSelected: false };
    }

    function methodIsChainable(method) {
        if (!method) return false;
        if (Object.prototype.hasOwnProperty.call(method, 'chainable')) return Boolean(method.chainable);
        const type = String(method.return_type || '').replace(/^\?/, '').trim().toLowerCase();
        return type === 'self' || type === 'static';
    }

    function selectedMethodMeta(node) {
        const source = methodBrowserSource(node);
        if (!source.supported || !source.canUseSelected) return null;
        const name = String(node?.data?.[source.property] || '');
        return source.methods.find(method => method.name === name) || null;
    }

    function methodSummary(method) {
        const raw = String(method?.description || method?.help || '').replace(/```[\s\S]*?```/g, ' ').replace(/[#*`]/g, '').replace(/\s+/g, ' ').trim();
        if (!raw) return 'No summary has been written yet.';
        return raw.length > 180 ? raw.slice(0, 177) + '…' : raw;
    }

    function markdownHelp(value) {
        let text = String(value || '').replace(/\r\n?/g, '\n');
        if (!text.trim()) return '<p class="tvi-muted">No help information has been written for this method yet.</p>';
        const fences = [];
        text = text.replace(/```(?:[A-Za-z0-9_+-]+)?\n([\s\S]*?)```/g, (_, code) => {
            const token = `@@TVI_CODE_${fences.length}@@`;
            fences.push(`<pre><code>${esc(code.replace(/\n$/, ''))}</code></pre>`);
            return token;
        });
        const inline = value => esc(value)
            .replace(/`([^`]+)`/g, '<code>$1</code>')
            .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
            .replace(/\*([^*]+)\*/g, '<em>$1</em>');
        const lines = text.split('\n');
        const html = [];
        let list = [];
        const flushList = () => { if (list.length) { html.push(`<ul>${list.join('')}</ul>`); list = []; } };
        for (const line of lines) {
            const trimmed = line.trim();
            if (/^@@TVI_CODE_\d+@@$/.test(trimmed)) { flushList(); html.push(trimmed); continue; }
            const heading = /^(#{1,3})\s+(.+)$/.exec(trimmed);
            if (heading) { flushList(); const level = heading[1].length; html.push(`<h${level}>${inline(heading[2])}</h${level}>`); continue; }
            const item = /^[-*]\s+(.+)$/.exec(trimmed);
            if (item) { list.push(`<li>${inline(item[1])}</li>`); continue; }
            flushList();
            if (trimmed) html.push(`<p>${inline(trimmed)}</p>`);
        }
        flushList();
        return html.join('').replace(/@@TVI_CODE_(\d+)@@/g, (_, index) => fences[Number(index)] || '');
    }

    function chainCanAnchor(node, source) {
        if (!node || !source?.supported) return false;
        if (source.isService) return true;
        const method = selectedMethodMeta(node);
        return Boolean(method && methodIsChainable(method) && !method.static);
    }

    function openMethodBrowser(nodeId = null) {
        const node = nodeId ? nodeById(nodeId) : selectedNodes()[0];
        if (!node) return;
        const source = methodBrowserSource(node);
        if (!source.supported) return;
        if (!source.methods.length) { toast('Choose a model or class with methods first.', 'warn'); return; }
        const selectedName = source.property ? String(node.data[source.property] || '') : '';
        const initial = Math.max(0, source.methods.findIndex(method => method.name === selectedName));
        const anchored = chainCanAnchor(node, source);
        state.methodBrowser = {
            nodeId: node.id,
            methods: source.methods,
            sourceName: source.name,
            property: source.property,
            selectedIndex: initial,
            chainSelectedIndex: -1,
            chain: [],
            source,
            anchorNodeId: anchored ? node.id : null
        };
        methodBrowserSearch.value = '';
        methodBrowserModal.hidden = false;
        document.body.classList.add('tvi-modal-open');
        renderMethodBrowser();
        methodBrowserSearch.focus();
    }

    function closeMethodBrowser() {
        if (methodBrowserModal) methodBrowserModal.hidden = true;
        state.methodBrowser = null;
        if (![...document.querySelectorAll('.tvi-modal')].some(modal => !modal.hidden)) document.body.classList.remove('tvi-modal-open');
    }

    function chainParamInitial(parameter) {
        const hasDefault = String(parameter?.default ?? '').trim() !== '' || Boolean(parameter?.optional);
        return { source: hasDefault ? 'default' : 'external', value: chainParamSuggestedValue(parameter) };
    }

    function chainParamSuggestedValue(parameter) {
        const type = phpTypeToPort(parameter?.type);
        if (type === 'number') return '0';
        if (type === 'boolean') return 'true';
        if (type === 'array') return '[]';
        return '';
    }

    function createMethodChainStep(ctx, methodIndex) {
        const method = ctx.methods[methodIndex];
        const params = {};
        for (const parameter of normalizeMethodParams(method?.params || [])) params[parameter.name] = chainParamInitial(parameter);
        return { id: uid('chain-step'), methodIndex, params };
    }

    function appendMethodToChain(methodIndex) {
        const ctx = state.methodBrowser;
        if (!ctx || !ctx.methods[methodIndex]) return;
        const last = ctx.chain.length ? ctx.methods[ctx.chain[ctx.chain.length - 1].methodIndex] : null;
        if (last && !methodIsChainable(last)) { toast(`${last.name} ends the chain. Remove it or place the new method before it.`, 'warn'); return; }
        const method = ctx.methods[methodIndex];
        if (method.static && ctx.chain.length) { toast('A static method can only be the first step in this linear chain.', 'warn'); return; }
        ctx.chain.push(createMethodChainStep(ctx, methodIndex));
        ctx.chainSelectedIndex = ctx.chain.length - 1;
        ctx.selectedIndex = methodIndex;
        renderMethodBrowser();
    }

    function clearMethodChain() {
        const ctx = state.methodBrowser;
        if (!ctx) return;
        ctx.chain = [];
        ctx.chainSelectedIndex = -1;
        renderMethodBrowser();
    }

    function methodChainValidation(ctx = state.methodBrowser) {
        const errors = [], warnings = [];
        if (!ctx?.chain?.length) errors.push('Add at least one method.');
        ctx?.chain?.forEach((step, index) => {
            const method = ctx.methods[step.methodIndex];
            if (!method) { errors.push(`Step ${index + 1} has no method.`); return; }
            if (method.static && index > 0) errors.push(`${method.name} is static and cannot follow an object method.`);
            if (index < ctx.chain.length - 1 && !methodIsChainable(method)) errors.push(`${method.name} returns ${method.return_type || 'a terminal result'} and must end the chain.`);
            if (['update','delete'].includes(method.name) && !ctx.chain.slice(0, index).some(item => ['where','orWhere','whereRaw','whereIn','whereNull','whereBetween'].includes(ctx.methods[item.methodIndex]?.name))) warnings.push(`${method.name} has no earlier WHERE method and may affect every row.`);
            if (method.name === 'having' && !ctx.chain.slice(0, index).some(item => ctx.methods[item.methodIndex]?.name === 'groupBy')) warnings.push('having() is normally used after groupBy().');
        });
        return { valid: errors.length === 0, errors, warnings };
    }

    function phpPreviewString(value) {
        return `'${String(value ?? '').replace(/\\/g, '\\\\').replace(/'/g, "\\'")}'`;
    }

    function chainParameterExpression(parameter, config) {
        const source = String(config?.source || 'external');
        const value = String(config?.value ?? '');
        if (source === 'default') return String(parameter?.default ?? '').trim() || '/* default */';
        if (source === 'external') return `$${sanitize(parameter?.name || 'value')}`;
        if (source === 'number') return isFinite(Number(value)) ? String(Number(value)) : '0';
        if (source === 'boolean') return value === 'false' ? 'false' : 'true';
        if (source === 'null') return 'null';
        if (source === 'variable') return `$${phpVariableName(value || parameter?.name || 'value')}`;
        if (source === 'array') {
            try { const parsed = JSON.parse(value || '[]'); return JSON.stringify(parsed).replace(/"([^"\\]*(?:\\.[^"\\]*)*)"(?=\s*:)/g, "'$1'").replace(/"([^"\\]*(?:\\.[^"\\]*)*)"/g, "'$1'"); }
            catch (_) { return `[${value.split(',').map(item => phpPreviewString(item.trim())).join(', ')}]`; }
        }
        return phpPreviewString(value);
    }

    function methodChainPreview(ctx) {
        if (!ctx.chain.length) return '// Add methods to preview the chain.';
        const lines = [ctx.source.variable || '$object'];
        for (const step of ctx.chain) {
            const method = ctx.methods[step.methodIndex];
            const params = normalizeMethodParams(method?.params || []);
            const args = params.map(parameter => chainParameterExpression(parameter, step.params?.[parameter.name])).join(', ');
            lines.push(`    ->${method.name}(${args})`);
        }
        return lines.join('\n') + ';';
    }

    function renderMethodChain(ctx) {
        if (!methodBrowserChainList) return;
        const sourceNode = ctx.anchorNodeId ? nodeById(ctx.anchorNodeId) : null;
        const sourceLabel = sourceNode ? nodeTitle(sourceNode) : `${ctx.sourceName} source`;
        methodBrowserChainSource.textContent = sourceNode ? `The generated methods will continue from “${sourceLabel}”.` : `A new unconnected ${ctx.sourceName} chain will be created. Connect its first execution/object inputs when needed.`;
        const sourceHtml = `<div class="tvi-method-chain-source-step"><span class="tvi-method-chain-number">S</span><div class="tvi-method-chain-step-main"><strong>${esc(sourceLabel)}</strong><small>${sourceNode ? 'Existing graph source' : 'Implicit or external source'}</small></div></div>`;
        if (!ctx.chain.length) {
            methodBrowserChainList.innerHTML = `${sourceHtml}<div class="tvi-method-chain-connector"></div><div class="tvi-method-chain-empty">Drag methods from the left, double-click a method, or use <strong>Add to chain</strong>.</div>`;
        } else {
            const items = ctx.chain.map((step, index) => {
                const method = ctx.methods[step.methodIndex];
                const chainable = methodIsChainable(method);
                return `<div class="tvi-method-chain-connector"></div><article class="tvi-method-chain-step ${ctx.chainSelectedIndex === index ? 'is-active' : ''}" draggable="true" data-chain-step-index="${index}"><span class="tvi-method-chain-number">${index + 1}</span><div class="tvi-method-chain-step-main"><code>${esc(methodSignature(method))}</code><div><span class="tvi-method-chain-step-badge ${chainable ? 'is-chainable' : 'is-terminal'}">${chainable ? 'Chainable' : 'End'}</span></div></div><div class="tvi-method-chain-step-actions"><button type="button" data-chain-move="up" data-chain-index="${index}" title="Move earlier">↑</button><button type="button" data-chain-move="down" data-chain-index="${index}" title="Move later">↓</button><button type="button" class="tvi-danger" data-chain-remove="${index}" title="Remove">×</button></div></article>`;
            }).join('');
            methodBrowserChainList.innerHTML = sourceHtml + items;
        }
        const validation = methodChainValidation(ctx);
        methodBrowserChainPreview.textContent = methodChainPreview(ctx);
        const notes = [...validation.errors, ...validation.warnings];
        methodBrowserChainStatus.textContent = validation.valid ? (validation.warnings.length ? `${validation.warnings.length} warning${validation.warnings.length === 1 ? '' : 's'}` : 'Valid linear chain') : `${validation.errors.length} problem${validation.errors.length === 1 ? '' : 's'}`;
        methodBrowserChainStatus.title = notes.join('\n');
        if (methodBrowserCreateButton) methodBrowserCreateButton.disabled = !validation.valid;
    }

    function chainParamSourceOptions(parameter, selected) {
        const options = [
            ['external','External connection'],
            ['default','Use method default'],
            ['string','String literal'],
            ['number','Number literal'],
            ['boolean','Boolean literal'],
            ['array','Array literal'],
            ['variable','Variable'],
            ['null','Null']
        ];
        return options.map(([value,label]) => `<option value="${value}" ${selected === value ? 'selected' : ''} ${value === 'default' && String(parameter.default ?? '').trim() === '' && !parameter.optional ? 'disabled' : ''}>${label}</option>`).join('');
    }

    function chainParamValueControl(parameter, config, stepIndex) {
        const source = String(config?.source || 'external');
        const value = String(config?.value ?? '');
        if (['external','default','null'].includes(source)) return `<input type="text" value="${source === 'external' ? 'Connect this port in the graph' : source === 'default' ? esc(String(parameter.default || 'Framework default')) : 'null'}" disabled>`;
        if (source === 'boolean') return `<select data-chain-param-value data-chain-step="${stepIndex}" data-chain-param="${attr(parameter.name)}"><option value="true" ${value !== 'false' ? 'selected' : ''}>true</option><option value="false" ${value === 'false' ? 'selected' : ''}>false</option></select>`;
        if (source === 'array') return `<input type="text" data-chain-param-value data-chain-step="${stepIndex}" data-chain-param="${attr(parameter.name)}" value="${attr(value)}" placeholder='["id", "name"] or id,name'>`;
        return `<input type="${source === 'number' ? 'number' : 'text'}" data-chain-param-value data-chain-step="${stepIndex}" data-chain-param="${attr(parameter.name)}" value="${attr(value)}" placeholder="${source === 'variable' ? 'variable_name' : 'Value'}">`;
    }

    function renderMethodBrowserDetail(ctx, method, chainIndex = -1) {
        const params = normalizeMethodParams(method?.params || []);
        const chainable = methodIsChainable(method);
        let parameterEditor = '';
        if (chainIndex >= 0 && ctx.chain[chainIndex]) {
            const step = ctx.chain[chainIndex];
            parameterEditor = `<section class="tvi-method-chain-params"><div><h3>Configure this chain step</h3><p class="tvi-muted">Literal values become normal Data nodes. External values remain visible as unconnected method inputs.</p></div>${params.length ? params.map(parameter => {
                const config = step.params[parameter.name] || chainParamInitial(parameter);
                step.params[parameter.name] = config;
                return `<div class="tvi-method-chain-param"><div><strong>$${esc(parameter.name)}</strong><small>${esc(parameter.type || 'mixed')}${String(parameter.default || '').trim() ? ` · default ${esc(parameter.default)}` : ''}</small></div><div class="tvi-method-chain-param-controls"><select data-chain-param-source data-chain-step="${chainIndex}" data-chain-param="${attr(parameter.name)}">${chainParamSourceOptions(parameter, config.source)}</select>${chainParamValueControl(parameter, config, chainIndex)}</div></div>`;
            }).join('') : '<p class="tvi-muted">This method has no parameters.</p>'}</section>`;
        }
        methodBrowserDetail.innerHTML = `<h2>${esc(method.label || method.name || 'Method')}</h2><code class="tvi-method-signature">${esc(methodSignature(method))}</code><div class="tvi-method-meta"><span>${esc(method.group || 'Method')}</span><span>Returns ${esc(method.return_type || 'mixed')}</span><span>${params.length} parameter${params.length === 1 ? '' : 's'}</span>${method.static ? '<span>static</span>' : ''}<span>${chainable ? 'Chainable' : 'Terminal'}</span></div><div class="tvi-method-help-render">${markdownHelp(method.help || method.description || '')}</div>${chainIndex < 0 ? `<button type="button" class="tvi-method-chain-add tvi-button-primary" data-chain-add-method="${ctx.selectedIndex}">Add to chain</button>` : ''}${parameterEditor}`;
    }

    function renderMethodBrowser() {
        const ctx = state.methodBrowser;
        if (!ctx || !methodBrowserList || !methodBrowserDetail) return;
        const query = String(methodBrowserSearch.value || '').trim().toLowerCase();
        const indexed = ctx.methods.map((method, index) => ({ method, index })).filter(({ method }) => {
            const haystack = `${method.name} ${methodSignature(method)} ${method.help || ''} ${method.description || ''}`.toLowerCase();
            return !query || haystack.includes(query);
        });
        const groups = {};
        for (const item of indexed) (groups[item.method.group || 'Methods'] ||= []).push(item);
        methodBrowserList.innerHTML = Object.entries(groups).map(([group, items]) => `<section class="tvi-method-browser-group"><strong>${esc(group)}</strong>${items.map(({ method, index }) => { const chainable = methodIsChainable(method); return `<button type="button" class="tvi-method-browser-item ${ctx.selectedIndex === index ? 'is-active' : ''}" data-method-browser-index="${index}" data-method-pointer-draggable="true"><span class="tvi-method-browser-drag-grip" aria-hidden="true">⋮⋮</span><code>${esc(methodSignature(method))}</code><small>${esc(methodSummary(method))}</small><span class="tvi-method-browser-item-badges"><span class="${chainable ? 'is-chainable' : 'is-terminal'}">${chainable ? 'Chainable' : 'End'}</span>${method.static ? '<span>Static</span>' : ''}</span></button>`; }).join('')}</section>`).join('') || '<p class="tvi-muted">No methods match this search.</p>';
        methodBrowserTitle.textContent = `${ctx.sourceName} · Methods & Chain Builder`;
        methodBrowserSummary.textContent = `${ctx.methods.length} available method${ctx.methods.length === 1 ? '' : 's'}. Drag or double-click methods to assemble a linear chain.`;
        if (methodBrowserUseButton) methodBrowserUseButton.hidden = !ctx.source.canUseSelected;
        renderMethodChain(ctx);
        const chainMethod = ctx.chainSelectedIndex >= 0 ? ctx.methods[ctx.chain[ctx.chainSelectedIndex]?.methodIndex] : null;
        const selected = chainMethod || ctx.methods[ctx.selectedIndex] || ctx.methods[0];
        if (selected) renderMethodBrowserDetail(ctx, selected, chainMethod ? ctx.chainSelectedIndex : -1);
    }

    function methodBrowserListClick(event) {
        if (Date.now() < state.suppressMethodBrowserClickUntil) { event.preventDefault(); return; }
        const button = event.target.closest('[data-method-browser-index]');
        const ctx = state.methodBrowser;
        if (!button || !ctx) return;
        ctx.selectedIndex = Number(button.dataset.methodBrowserIndex) || 0;
        ctx.chainSelectedIndex = -1;
        methodBrowserList.querySelectorAll('[data-method-browser-index]').forEach(item => {
            item.classList.toggle('is-active', Number(item.dataset.methodBrowserIndex) === ctx.selectedIndex);
        });
        const selected = ctx.methods[ctx.selectedIndex] || ctx.methods[0];
        if (selected) renderMethodBrowserDetail(ctx, selected, -1);
    }

    function methodBrowserListDoubleClick(event) {
        if (Date.now() < state.suppressMethodBrowserClickUntil) { event.preventDefault(); return; }
        const button = event.target.closest('[data-method-browser-index]');
        if (!button) return;
        event.preventDefault();
        appendMethodToChain(Number(button.dataset.methodBrowserIndex) || 0);
    }

    function methodBrowserPointerDown(event) {
        const button = event.target.closest('[data-method-pointer-draggable]');
        const ctx = state.methodBrowser;
        if (!button || !ctx || event.button !== 0 || event.pointerType === 'touch' && !event.isPrimary) return;
        state.methodPointerDrag = {
            pointerId: event.pointerId,
            methodIndex: Number(button.dataset.methodBrowserIndex) || 0,
            startX: event.clientX,
            startY: event.clientY,
            active: false,
            ghost: null
        };
    }

    function methodBrowserPointerMove(event) {
        const drag = state.methodPointerDrag;
        const ctx = state.methodBrowser;
        if (!drag || !ctx || event.pointerId !== drag.pointerId) return;
        if (!drag.active && Math.hypot(event.clientX - drag.startX, event.clientY - drag.startY) < 6) return;
        if (!drag.active) {
            drag.active = true;
            const method = ctx.methods[drag.methodIndex];
            const ghost = document.createElement('div');
            ghost.className = 'tvi-method-browser-drag-ghost';
            ghost.innerHTML = `<strong>Add method</strong><code>${esc(methodSignature(method || {}))}</code>`;
            document.body.appendChild(ghost);
            drag.ghost = ghost;
            document.body.classList.add('is-method-pointer-dragging');
        }
        event.preventDefault();
        if (drag.ghost) {
            drag.ghost.style.left = `${event.clientX + 14}px`;
            drag.ghost.style.top = `${event.clientY + 14}px`;
        }
        const rect = methodBrowserChainList?.getBoundingClientRect();
        const inside = rect && event.clientX >= rect.left && event.clientX <= rect.right && event.clientY >= rect.top && event.clientY <= rect.bottom;
        methodBrowserChainList?.classList.toggle('is-drag-over', Boolean(inside));
    }

    function methodChainInsertionIndex(clientY, ctx = state.methodBrowser) {
        if (!ctx || !methodBrowserChainList) return 0;
        const steps = Array.from(methodBrowserChainList.querySelectorAll('[data-chain-step-index]'));
        for (const step of steps) {
            const rect = step.getBoundingClientRect();
            if (clientY < rect.top + rect.height / 2) return Number(step.dataset.chainStepIndex) || 0;
        }
        return ctx.chain.length;
    }

    function finishMethodBrowserPointerDrag(event, cancelled = false) {
        const drag = state.methodPointerDrag;
        const ctx = state.methodBrowser;
        if (!drag || event.pointerId !== drag.pointerId) return;
        let created = false;
        if (drag.active && !cancelled && ctx && methodBrowserChainList) {
            const rect = methodBrowserChainList.getBoundingClientRect();
            const inside = event.clientX >= rect.left && event.clientX <= rect.right && event.clientY >= rect.top && event.clientY <= rect.bottom;
            if (inside) {
                const targetIndex = methodChainInsertionIndex(event.clientY, ctx);
                const step = createMethodChainStep(ctx, drag.methodIndex);
                ctx.chain.splice(targetIndex, 0, step);
                ctx.chainSelectedIndex = targetIndex;
                ctx.selectedIndex = drag.methodIndex;
                created = true;
            }
        }
        drag.ghost?.remove();
        methodBrowserChainList?.classList.remove('is-drag-over');
        document.body.classList.remove('is-method-pointer-dragging');
        if (drag.active) state.suppressMethodBrowserClickUntil = Date.now() + 350;
        state.methodPointerDrag = null;
        if (created) renderMethodBrowser();
    }

    function methodBrowserPointerUp(event) {
        finishMethodBrowserPointerDrag(event, false);
    }

    function methodBrowserPointerCancel(event) {
        finishMethodBrowserPointerDrag(event, true);
    }

    function methodBrowserListDragStart(event) {
        const button = event.target.closest('[data-method-browser-index]');
        if (!button || !event.dataTransfer) return;
        const payload = `method:${button.dataset.methodBrowserIndex}`;
        event.dataTransfer.setData('application/x-tvi-method', payload);
        event.dataTransfer.setData('text/plain', payload);
        event.dataTransfer.effectAllowed = 'copyMove';
        button.classList.add('is-dragging');
        requestAnimationFrame(() => button.classList.remove('is-dragging'));
    }

    function methodChainDragStart(event) {
        const step = event.target.closest('[data-chain-step-index]');
        if (!step || !event.dataTransfer) return;
        const payload = `step:${step.dataset.chainStepIndex}`;
        event.dataTransfer.setData('application/x-tvi-method', payload);
        event.dataTransfer.setData('text/plain', payload);
        event.dataTransfer.effectAllowed = 'move';
    }

    function methodChainDragOver(event) {
        const types = Array.from(event.dataTransfer?.types || []);
        if (!types.includes('application/x-tvi-method') && !types.includes('text/plain')) return;
        event.preventDefault();
        methodBrowserChainList.classList.add('is-drag-over');
        const raw = event.dataTransfer?.getData('application/x-tvi-method') || event.dataTransfer?.getData('text/plain') || '';
        event.dataTransfer.dropEffect = raw.startsWith('method:') ? 'copy' : 'move';
    }

    function methodChainDragLeave(event) {
        if (!methodBrowserChainList.contains(event.relatedTarget)) methodBrowserChainList.classList.remove('is-drag-over');
    }

    function methodChainDrop(event) {
        const ctx = state.methodBrowser;
        if (!ctx || !event.dataTransfer) return;
        const raw = event.dataTransfer.getData('application/x-tvi-method')
            || event.dataTransfer.getData('text/plain');
        if (!/^\s*(method|step):\d+\s*$/.test(raw)) return;
        event.preventDefault();
        methodBrowserChainList.classList.remove('is-drag-over');
        const target = event.target.closest('[data-chain-step-index]');
        let targetIndex = target ? Number(target.dataset.chainStepIndex) : ctx.chain.length;
        const rect = target?.getBoundingClientRect();
        if (target && rect && event.clientY > rect.top + rect.height / 2) targetIndex += 1;
        if (raw.startsWith('method:')) {
            const methodIndex = Number(raw.split(':')[1]);
            const step = createMethodChainStep(ctx, methodIndex);
            ctx.chain.splice(targetIndex, 0, step);
            ctx.chainSelectedIndex = targetIndex;
            ctx.selectedIndex = methodIndex;
        } else if (raw.startsWith('step:')) {
            const from = Number(raw.split(':')[1]);
            if (!Number.isInteger(from) || !ctx.chain[from]) return;
            const [step] = ctx.chain.splice(from, 1);
            if (from < targetIndex) targetIndex -= 1;
            ctx.chain.splice(Math.max(0, Math.min(targetIndex, ctx.chain.length)), 0, step);
            ctx.chainSelectedIndex = ctx.chain.indexOf(step);
        }
        renderMethodBrowser();
    }

    function methodChainListClick(event) {
        const ctx = state.methodBrowser;
        if (!ctx) return;
        const remove = event.target.closest('[data-chain-remove]');
        if (remove) {
            const index = Number(remove.dataset.chainRemove);
            ctx.chain.splice(index, 1);
            ctx.chainSelectedIndex = Math.min(index, ctx.chain.length - 1);
            renderMethodBrowser();
            return;
        }
        const move = event.target.closest('[data-chain-move]');
        if (move) {
            const index = Number(move.dataset.chainIndex);
            const to = move.dataset.chainMove === 'up' ? index - 1 : index + 1;
            if (to >= 0 && to < ctx.chain.length) {
                [ctx.chain[index], ctx.chain[to]] = [ctx.chain[to], ctx.chain[index]];
                ctx.chainSelectedIndex = to;
                renderMethodBrowser();
            }
            return;
        }
        const step = event.target.closest('[data-chain-step-index]');
        if (step) {
            ctx.chainSelectedIndex = Number(step.dataset.chainStepIndex);
            ctx.selectedIndex = ctx.chain[ctx.chainSelectedIndex]?.methodIndex ?? ctx.selectedIndex;
            renderMethodBrowser();
        }
    }

    function methodBrowserDetailClick(event) {
        const add = event.target.closest('[data-chain-add-method]');
        if (add) appendMethodToChain(Number(add.dataset.chainAddMethod) || 0);
    }

    function methodBrowserDetailInput(event) {
        const ctx = state.methodBrowser;
        const control = event.target.closest('[data-chain-param-source],[data-chain-param-value]');
        if (!ctx || !control) return;
        const stepIndex = Number(control.dataset.chainStep);
        const name = String(control.dataset.chainParam || '');
        const step = ctx.chain[stepIndex];
        if (!step || !name) return;
        step.params[name] ||= { source: 'external', value: '' };
        if (control.hasAttribute('data-chain-param-source')) {
            step.params[name].source = control.value;
            if (!step.params[name].value) {
                const method = ctx.methods[step.methodIndex];
                const parameter = normalizeMethodParams(method?.params || []).find(item => item.name === name);
                step.params[name].value = chainParamSuggestedValue(parameter);
            }
            renderMethodBrowser();
        } else {
            step.params[name].value = control.value;
            renderMethodChain(ctx);
        }
    }

    function confirmMethodBrowser() {
        const ctx = state.methodBrowser;
        if (!ctx || !ctx.source.canUseSelected) return;
        const node = nodeById(ctx.nodeId);
        const method = ctx.methods[ctx.selectedIndex];
        if (!node || !method) return closeMethodBrowser();
        snapshot();
        node.data[ctx.property || 'method_name'] = method.name;
        changed();
        closeMethodBrowser();
        renderAll();
    }

    function chainLiteralNode(graph, source, value, x, y, title) {
        let type = 'data.string', data = {};
        if (source === 'number') { type = 'data.number'; data.value = isFinite(Number(value)) ? Number(value) : 0; }
        else if (source === 'boolean') { type = 'data.boolean'; data.value = String(value) !== 'false'; }
        else if (source === 'null') type = 'data.null';
        else if (source === 'variable') { type = 'data.variable'; data.variable_name = phpVariableName(value || title || 'value'); }
        else { type = 'data.string'; data.value = String(value ?? ''); }
        const def = definitions[type];
        if (!def) return null;
        const node = { id: uid(type.replace(/\W/g, '-')), type, x, y, data: { ...(clone(def.defaults || {})), ...data, title: title || def.label } };
        graph.nodes.push(node);
        return node;
    }

    function parseChainArray(value) {
        try {
            const parsed = JSON.parse(String(value || '[]'));
            return Array.isArray(parsed) ? parsed : Object.values(parsed || {});
        } catch (_) {
            return String(value || '').split(',').map(item => item.trim()).filter(item => item !== '');
        }
    }

    function chainArrayNode(graph, value, x, y, title) {
        const items = parseChainArray(value);
        const def = definitions['data.array'];
        if (!def) return null;
        const arrayNode = { id: uid('data-array'), type: 'data.array', x, y, data: { ...(clone(def.defaults || {})), title: title || 'Array', mode: 'indexed', item_count: items.length, keys: '' } };
        graph.nodes.push(arrayNode);
        items.forEach((item, index) => {
            let source = 'string', literal = item;
            if (item === null) source = 'null';
            else if (typeof item === 'number') source = 'number';
            else if (typeof item === 'boolean') source = 'boolean';
            const child = chainLiteralNode(graph, source, literal, x - 20 + (index % 3) * 95, y + 145 + Math.floor(index / 3) * 105, `${title || 'Array'} ${index + 1}`);
            if (child) graph.edges.push({ id: uid('edge'), from: child.id, from_port: 'value', to: arrayNode.id, to_port: `item_${index + 1}`, port_type: getPorts(child).outputs.find(port => port.id === 'value')?.type || 'mixed' });
        });
        return arrayNode;
    }

    function createMethodChain() {
        const ctx = state.methodBrowser;
        const validation = methodChainValidation(ctx);
        if (!ctx || !validation.valid) { toast(validation.errors[0] || 'The chain is not valid.', 'fail'); return; }
        const graph = activeGraph();
        if (!graph || graph.kind !== 'flow') { toast('Method chains can only be created in an execution flow.', 'fail'); return; }
        const callDef = definitions[ctx.source.callType];
        if (!callDef) { toast('The method-call node definition is unavailable.', 'fail'); return; }
        const anchor = ctx.anchorNodeId ? nodeById(ctx.anchorNodeId) : null;
        const view = viewport.getBoundingClientRect();
        const center = screenToWorld(view.left + view.width / 2, view.top + view.height / 2);
        const startX = anchor ? Number(anchor.x) + 300 : center.x - Math.max(0, ctx.chain.length - 1) * 145;
        const startY = anchor ? Number(anchor.y) : center.y - 70;
        snapshot();
        const created = [];
        let previous = anchor;
        for (let index = 0; index < ctx.chain.length; index++) {
            const step = ctx.chain[index];
            const method = ctx.methods[step.methodIndex];
            const node = {
                id: uid(ctx.source.callType.replace(/\W/g, '-')),
                type: ctx.source.callType,
                x: startX + index * 310,
                y: startY,
                data: { ...(clone(callDef.defaults || {})), ...(clone(ctx.source.refData || {})), title: method.label || method.name || callDef.label }
            };
            node.data[ctx.source.methodProperty] = method.name;
            graph.nodes.push(node);
            created.push(node);
            if (previous) {
                const previousPorts = getPorts(previous);
                const currentPorts = getPorts(node);
                if (previousPorts.outputs.some(port => port.id === 'exec') && currentPorts.inputs.some(port => port.id === 'exec_in')) graph.edges.push({ id: uid('edge'), from: previous.id, from_port: 'exec', to: node.id, to_port: 'exec_in', port_type: 'exec' });
                if (!method.static && ctx.source.objectInput && currentPorts.inputs.some(port => port.id === ctx.source.objectInput)) {
                    const outputPort = previous === anchor ? (ctx.source.anchorObjectOutput || ctx.source.objectOutput) : ctx.source.objectOutput;
                    if (previousPorts.outputs.some(port => port.id === outputPort)) graph.edges.push({ id: uid('edge'), from: previous.id, from_port: outputPort, to: node.id, to_port: ctx.source.objectInput, port_type: 'object' });
                }
            }
            const params = normalizeMethodParams(method.params || []);
            let literalRow = 0;
            for (const parameter of params) {
                const config = step.params?.[parameter.name] || chainParamInitial(parameter);
                const source = String(config.source || 'external');
                if (source === 'external' || source === 'default') continue;
                const px = node.x + 20 + literalRow * 25;
                const py = node.y + 190 + literalRow * 115;
                const label = `${method.name} · ${parameter.name}`;
                const dataNode = source === 'array' ? chainArrayNode(graph, config.value, px, py, label) : chainLiteralNode(graph, source, config.value, px, py, label);
                if (dataNode) {
                    graph.edges.push({ id: uid('edge'), from: dataNode.id, from_port: 'value', to: node.id, to_port: `param_${sanitize(parameter.name)}`, port_type: getPorts(dataNode).outputs.find(port => port.id === 'value')?.type || 'mixed' });
                    literalRow += 1;
                }
            }
            previous = node;
        }
        changed();
        closeMethodBrowser();
        selectNodes(created.map(node => node.id));
        renderAll();
        toast(`${created.length} method node${created.length === 1 ? '' : 's'} created and connected.`, 'success');
    }

    function viewSiblingInfo(node) {
        const graph = activeGraph();
        if (!node || graph?.kind !== 'view' || !String(node.type).startsWith('view.')) return null;
        const incoming = graph.edges.find(edge => edge.to === node.id && edge.port_type === 'view-child');
        let siblings = [];
        if (incoming) {
            const ids = graph.edges
                .filter(edge => edge.port_type === 'view-child' && edge.from === incoming.from && edge.from_port === incoming.from_port)
                .map(edge => edge.to);
            siblings = ids.map(id => nodeById(id)).filter(Boolean);
        } else {
            const childIds = new Set(graph.edges.filter(edge => edge.port_type === 'view-child').map(edge => edge.to));
            siblings = graph.nodes.filter(item => String(item.type).startsWith('view.') && !childIds.has(item.id));
        }
        siblings.sort((a, b) => (Number(a.y) - Number(b.y)) || (Number(a.x) - Number(b.x)) || String(a.id).localeCompare(String(b.id)));
        const index = siblings.findIndex(item => item.id === node.id);
        return index < 0 ? null : { siblings, index, incoming };
    }

    function viewOrderTools(node) {
        const info = viewSiblingInfo(node);
        if (!info) return '';
        const scope = info.incoming ? `inside ${nodeTitle(nodeById(info.incoming.from))}` : 'among top-level View items';
        return `<div class="tvi-view-order-tools"><div><strong>Display order ${info.index + 1} of ${info.siblings.length}</strong><small>Order follows vertical canvas position ${esc(scope)}. Drag this node higher or lower, or use these controls.</small></div><div class="tvi-view-order-actions"><button type="button" data-inspector-action="move-view-earlier" ${info.index === 0 ? 'disabled' : ''}>↑ Render Earlier</button><button type="button" data-inspector-action="move-view-later" ${info.index >= info.siblings.length - 1 ? 'disabled' : ''}>↓ Render Later</button></div></div>`;
    }

    function moveViewNode(direction) {
        const node = selectedNodes()[0], info = viewSiblingInfo(node);
        if (!node || !info) return;
        const targetIndex = info.index + direction;
        if (targetIndex < 0 || targetIndex >= info.siblings.length) return;
        const other = info.siblings[targetIndex];
        snapshot();
        const position = { x: node.x, y: node.y };
        node.x = other.x; node.y = other.y;
        other.x = position.x; other.y = position.y;
        changed(); renderAll();
    }

    function connectedRouteNodes(node) {
        if (!node || activeGraph().kind !== 'architecture') return [];
        const graph = activeGraph();
        const routeIds = new Set();
        for (const edge of graph.edges) {
            if (edge.from === node.id) {
                const other = nodeById(edge.to);
                if (other?.type === 'routing.route') routeIds.add(other.id);
            } else if (edge.to === node.id) {
                const other = nodeById(edge.from);
                if (other?.type === 'routing.route') routeIds.add(other.id);
            }
        }
        return [...routeIds].map(id => nodeById(id)).filter(Boolean);
    }

    function lifecycleHookName(node) {
        return String(node?.data?.hook_name || (node?.type === 'lifecycle.view' ? 'view' : 'controller'));
    }

    function lifecycleNodesForRoute(routeId, nodeType, hookName = null) {
        const graph = activeGraph();
        const ids = new Set();
        for (const edge of graph.edges) {
            if (edge.from === routeId) ids.add(edge.to);
            if (edge.to === routeId) ids.add(edge.from);
        }
        return graph.nodes
            .filter(node => ids.has(node.id) && node.type === nodeType && (hookName === null || lifecycleHookName(node) === hookName))
            .sort((a, b) => (Number(a.y) - Number(b.y)) || (Number(a.x) - Number(b.x)) || String(a.id).localeCompare(String(b.id)));
    }

    function lifecycleOrderEntries(node) {
        if (!node || !['lifecycle.controller', 'lifecycle.view'].includes(node.type) || activeGraph().kind !== 'architecture') return [];
        return connectedRouteNodes(node).map(route => {
            const siblings = lifecycleNodesForRoute(route.id, node.type, lifecycleHookName(node));
            const index = siblings.findIndex(item => item.id === node.id);
            return { route, siblings, index };
        }).filter(entry => entry.index >= 0);
    }

    function lifecycleOrderInfo(node) {
        const entries = lifecycleOrderEntries(node);
        if (!entries.length) return null;
        const first = entries[0];
        const prefix = node.type === 'lifecycle.controller' ? 'C' : 'V';
        const routeName = first.route.data?.route_name || nodeTitle(first.route);
        return {
            badge: `${prefix}${first.index + 1}${entries.length > 1 ? '+' : ''}`,
            title: `${node.type === 'lifecycle.controller' ? 'Controller' : 'View'} ${lifecycleHookName(node)} order ${first.index + 1} of ${first.siblings.length} on ${routeName}${entries.length > 1 ? `; connected to ${entries.length} routes` : ''}`
        };
    }

    function lifecycleOrderTools(node) {
        const entries = lifecycleOrderEntries(node);
        if (!entries.length) {
            return '<div class="tvi-view-order-tools"><div><strong>Route hook order</strong><small>Connect this node to a Route to see and control when its hook runs.</small></div></div>';
        }
        const rows = entries.map(entry => {
            const routeName = entry.route.data?.route_name || nodeTitle(entry.route);
            const hookName = lifecycleHookName(node);
            return `<div class="tvi-hook-order-row"><div><strong>${esc(routeName)} · ${esc(hookName)}</strong><small>Runs ${entry.index + 1} of ${entry.siblings.length} for this hook. Lower generated priority runs first.</small></div><div class="tvi-view-order-actions"><button type="button" data-inspector-action="move-hook-earlier" data-route-id="${attr(entry.route.id)}" ${entry.index === 0 ? 'disabled' : ''}>↑ Run Earlier</button><button type="button" data-inspector-action="move-hook-later" data-route-id="${attr(entry.route.id)}" ${entry.index >= entry.siblings.length - 1 ? 'disabled' : ''}>↓ Run Later</button></div></div>`;
        }).join('');
        return `<div class="tvi-view-order-tools tvi-hook-order-tools"><div><strong>Route hook order</strong><small>Order follows vertical canvas position. Drag this node higher or lower, or use these controls.</small></div>${rows}</div>`;
    }

    function moveLifecycleNode(routeId, direction) {
        const node = selectedNodes()[0];
        if (!node || !routeId) return;
        const siblings = lifecycleNodesForRoute(routeId, node.type, lifecycleHookName(node));
        const index = siblings.findIndex(item => item.id === node.id);
        const targetIndex = index + direction;
        if (index < 0 || targetIndex < 0 || targetIndex >= siblings.length) return;
        const other = siblings[targetIndex];
        snapshot();
        const position = { x: node.x, y: node.y };
        node.x = other.x;
        node.y = other.y;
        other.x = position.x;
        other.y = position.y;
        synchronizeAllLifecyclePriorities(false);
        changed();
        renderAll();
    }

    function synchronizeAllLifecyclePriorities(markChanged = false) {
        const graph = state.project?.graphs?.main;
        if (!graph) return;
        for (const route of graph.nodes.filter(node => node.type === 'routing.route')) {
            for (const nodeType of ['lifecycle.controller', 'lifecycle.view']) {
                const ids = new Set();
                for (const edge of graph.edges) {
                    if (edge.from === route.id) ids.add(edge.to);
                    if (edge.to === route.id) ids.add(edge.from);
                }
                const connected = graph.nodes.filter(node => ids.has(node.id) && node.type === nodeType);
                const hookNames = [...new Set(connected.map(lifecycleHookName))];
                for (const hookName of hookNames) {
                    const siblings = connected
                        .filter(node => lifecycleHookName(node) === hookName)
                        .sort((a, b) => (Number(a.y) - Number(b.y)) || (Number(a.x) - Number(b.x)) || String(a.id).localeCompare(String(b.id)));
                    siblings.forEach((node, index) => {
                        node.data ||= {};
                        node.data.route_priorities = typeof node.data.route_priorities === 'object' && node.data.route_priorities !== null
                            ? node.data.route_priorities
                            : {};
                        node.data.route_priorities[route.id] = (index + 1) * 10;
                    });
                }
            }
        }
        if (markChanged) changed();
    }

    function projectActionHookSuggestions() {
        const names = [];
        for (const graph of Object.values(state.project?.graphs || {})) {
            for (const candidate of graph?.nodes || []) {
                if (!['view.do_action', 'view.action_hook'].includes(String(candidate?.type || ''))) continue;
                const hookName = String(candidate?.data?.hook_name || '').trim();
                if (hookName) names.push(hookName);
            }
        }
        return [...new Set(names)].sort((a, b) => a.localeCompare(b));
    }

    function inspectorPropertySuggestions(node, property) {
        let suggestions = Array.isArray(property?.suggestions)
            ? property.suggestions
            : [];
        const source = property?.suggestions_by;
        if (source && typeof source === 'object') {
            const sourceProperty = String(source.property || '');
            const selected = String(node?.data?.[sourceProperty] ?? '');
            const mapped = source.values && typeof source.values === 'object'
                ? source.values[selected]
                : null;
            if (Array.isArray(mapped)) suggestions = mapped;
        }

        const canIncludeProjectActions = Boolean(property?.include_project_action_hooks)
            && !(String(node?.type || '') === 'lifecycle.controller'
                && String(node?.data?.hook_type || 'action') === 'filter');
        if (canIncludeProjectActions) {
            suggestions = [...suggestions, ...projectActionHookSuggestions()];
        }

        return [...new Set(
            suggestions
                .map(value => String(value || '').trim())
                .filter(Boolean)
        )];
    }

    function editableSelectControl(node, property, value, suggestions) {
        const controlId = `tvi-editable-select-${String(node.id).replace(/[^a-z0-9_-]+/gi, '-')}-${String(property.name).replace(/[^a-z0-9_-]+/gi, '-')}`;
        const options = suggestions.map(option => {
            const selected = String(option) === String(value);
            return `<button type="button" role="option" aria-selected="${selected ? 'true' : 'false'}" class="${selected ? 'is-selected' : ''}" data-editable-select-option="${attr(option)}">${esc(option)}</button>`;
        }).join('');

        return `<div class="tvi-editable-select" data-editable-select>
            <input id="${attr(controlId)}" data-property="${attr(property.name)}" data-inspector-node-id="${attr(node.id)}" type="text" value="${attr(value)}" autocomplete="off" role="combobox" aria-autocomplete="none" aria-haspopup="listbox" aria-expanded="false">
            <button type="button" class="tvi-editable-select__toggle" data-editable-select-toggle aria-label="Show available hook names" aria-expanded="false" title="Show available hook names">▾</button>
            <div class="tvi-editable-select__menu" data-editable-select-menu role="listbox" aria-labelledby="${attr(controlId)}" hidden>${options}</div>
        </div>`;
    }

    function closeEditableSelectMenus(except = null) {
        inspector.querySelectorAll('[data-editable-select]').forEach(wrapper => {
            if (except && wrapper === except) return;
            const menu = wrapper.querySelector('[data-editable-select-menu]');
            const toggle = wrapper.querySelector('[data-editable-select-toggle]');
            const input = wrapper.querySelector('input[role="combobox"]');
            if (menu) menu.hidden = true;
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
            if (input) input.setAttribute('aria-expanded', 'false');
            wrapper.classList.remove('is-open');
        });
    }

    function setEditableSelectOpen(wrapper, open) {
        if (!wrapper) return;
        if (open) closeEditableSelectMenus(wrapper);
        const menu = wrapper.querySelector('[data-editable-select-menu]');
        const toggle = wrapper.querySelector('[data-editable-select-toggle]');
        const input = wrapper.querySelector('input[role="combobox"]');
        if (!menu || !toggle || !input) return;

        menu.hidden = !open;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        input.setAttribute('aria-expanded', open ? 'true' : 'false');
        wrapper.classList.toggle('is-open', open);

        const current = String(input.value || '');
        menu.querySelectorAll('[data-editable-select-option]').forEach(option => {
            const selected = String(option.dataset.editableSelectOption || '') === current;
            option.classList.toggle('is-selected', selected);
            option.setAttribute('aria-selected', selected ? 'true' : 'false');
        });
    }

    function editableSelectKeyDown(event) {
        const wrapper = event.target.closest?.('[data-editable-select]');
        if (!wrapper) return;
        const menu = wrapper.querySelector('[data-editable-select-menu]');
        const input = wrapper.querySelector('input[role="combobox"]');
        if (!menu || !input) return;

        const options = [...menu.querySelectorAll('[data-editable-select-option]')];
        const optionIndex = options.indexOf(event.target);

        if (event.key === 'Escape') {
            if (!menu.hidden) {
                event.preventDefault();
                setEditableSelectOpen(wrapper, false);
                input.focus();
            }
            return;
        }

        if (event.target === input && (event.key === 'ArrowDown' || event.key === 'ArrowUp')) {
            event.preventDefault();
            setEditableSelectOpen(wrapper, true);
            const selectedIndex = options.findIndex(option => option.classList.contains('is-selected'));
            const index = selectedIndex >= 0 ? selectedIndex : (event.key === 'ArrowDown' ? 0 : options.length - 1);
            options[index]?.focus();
            return;
        }

        if (optionIndex >= 0 && (event.key === 'ArrowDown' || event.key === 'ArrowUp')) {
            event.preventDefault();
            const direction = event.key === 'ArrowDown' ? 1 : -1;
            options[(optionIndex + direction + options.length) % options.length]?.focus();
            return;
        }

        if (optionIndex >= 0 && event.key === 'Tab') {
            setEditableSelectOpen(wrapper, false);
        }
    }

    function inspectorField(node, p) {
        const value = node.data[p.name] ?? p.default ?? '';
        const help = p.help ? `<small>${esc(p.help)}</small>` : '';
        let control = '';
        if (p.type === 'textarea') control = `<textarea data-property="${attr(p.name)}" data-inspector-node-id="${attr(node.id)}" rows="4">${esc(String(value))}</textarea>`;
        else if (p.type === 'code') control = `<div class="tvi-code-field"><textarea class="tvi-code-editor" data-property="${attr(p.name)}" data-inspector-node-id="${attr(node.id)}" rows="11">${esc(String(value))}</textarea><button type="button" data-inspector-action="open-code-editor" data-editor-property="${attr(p.name)}">Open Fullscreen</button></div>`;
        else if (p.type === 'model_methods' || p.type === 'class_methods') control = methodsControl(node, p);
        else if (p.type === 'permission_list') control = catalogControl(node, p, 'permission');
        else if (p.type === 'migration_columns') control = migrationColumnsControl(node, p);
        else if (p.type === 'role_list') control = catalogControl(node, p, 'role');
        else if (p.type === 'dependency_list') control = dependencyControl(node, p);
        else if (p.type === 'validation_rules') control = validationRulesControl(node, p);
        else if (p.type === 'breakpoints') control = breakpointControl(node, p);
        else if (p.type === 'checkbox') control = `<input data-property="${attr(p.name)}" data-inspector-node-id="${attr(node.id)}" type="checkbox" ${value ? 'checked' : ''}>`;
        else if (p.type === 'editable_select') {
            control = editableSelectControl(node, p, value, inspectorPropertySuggestions(node, p));
        }
        else if (p.type === 'text' && inspectorPropertySuggestions(node, p).length) {
            control = editableSelectControl(node, p, value, inspectorPropertySuggestions(node, p));
        }
        else if (p.type === 'select') control = `<select data-property="${attr(p.name)}" data-inspector-node-id="${attr(node.id)}">${(p.options || []).map(o => `<option value="${attr(o)}" ${String(value) === String(o) ? 'selected' : ''}>${esc(o)}</option>`).join('')}</select>`;
        else if (p.type === 'pagination_template') control = paginationTemplateSelect(node, p);
        else if (p.type === 'pagination_palette') control = paginationPaletteControl(node, p);
        else if (p.type === 'number') control = `<input data-property="${attr(p.name)}" data-inspector-node-id="${attr(node.id)}" type="number" value="${attr(value)}">`;
        else if (p.type === 'model_ref') control = refSelect(node,p,'models.model','class_name');
        else if (p.type === 'model_method') control = methodSelect(node,p);
        else if (p.type === 'function_ref') control = refSelect(node,p,'custom.function_definition','function_name');
        else if (p.type === 'class_ref') control = refSelect(node,p,'custom.class_definition','class_name');
        else if (p.type === 'class_method') control = classMethodSelect(node,p);
        else if (p.type === 'view_ref') control = refSelect(node,p,'lifecycle.view','title');
        else if (p.type === 'reusable_view_ref') control = refSelect(node,p,'lifecycle.reusable_view','title');
        else if (p.type === 'code_asset_ref') control = codeAssetSelect(node,p);
        else if (p.type === 'modal_ref') control = activeGraphRefSelect(node,p,'view.modal','modal_id');
        else if (p.type === 'view_component_ref') control = javascriptViewComponentSelect(node,p);
        else if (p.type === 'ajax_progress_ref') control = javascriptViewComponentSelect(node,p,'view.ajax_progress');
        else if (p.type === 'form_ref') control = formSelect(node,p);
        else if (p.type === 'permission_ref') control = catalogReferenceSelect(node, p, 'permission');
        else if (p.type === 'role_ref') control = catalogReferenceSelect(node, p, 'role');
        else if (p.type === 'file') control = `<input data-file-property="${attr(p.name)}" data-inspector-node-id="${attr(node.id)}" type="file" ${p.accept ? `accept="${attr(p.accept)}"` : ''}><small>${esc(node.data.original_name || 'No file selected')}</small>`;
        else control = `<input data-property="${attr(p.name)}" data-inspector-node-id="${attr(node.id)}" type="text" value="${attr(value)}">`;
        if (p.type === 'validation_rules') return `<div class="tvi-field tvi-field--manager"><span>${esc(p.label)}</span>${control}${help}</div>`;
        return `<label class="tvi-field"><span>${esc(p.label)}</span>${control}${help}</label>`;
    }

    function insertInspectorMarker(node, property, marker) {
        if (!node || !property || !marker) return;
        const field = [...inspector.querySelectorAll(`[data-property="${property}"]`)]
            .find(element => String(element.dataset.inspectorNodeId || '') === String(node.id));
        snapshot();

        if (field && Number.isInteger(field.selectionStart) && Number.isInteger(field.selectionEnd)) {
            const before = String(field.value || '').slice(0, field.selectionStart);
            const after = String(field.value || '').slice(field.selectionEnd);
            field.value = before + marker + after;
            const cursor = before.length + marker.length;
            field.setSelectionRange(cursor, cursor);
            node.data[property] = field.value;
            field.focus();
        } else {
            node.data[property] = String(node.data[property] || '').replace(/\s*$/, '')
                + `\n\n${marker}\n`;
        }

        changed();
        renderAll();
    }


    function validationRuleDefinition(ruleName) {
        return validationRules.find(item => String(item.id || '') === String(ruleName || '')) || null;
    }

    function validationRuleParameterType(ruleName) {
        return validationRuleDefinition(ruleName)?.parameter || (ruleName ? 'scalar' : 'none');
    }

    function validationRuleFromRaw(raw) {
        let rule = '';
        let param = '';
        let errorMessage = '';
        if (typeof raw === 'string') {
            const separator = raw.indexOf(':');
            rule = separator >= 0 ? raw.slice(0, separator) : raw;
            param = separator >= 0 ? raw.slice(separator + 1) : '';
        } else if (raw && typeof raw === 'object') {
            rule = String(raw.rule || '');
            param = raw.param ?? '';
            errorMessage = String(raw.error_message || '');
        }
        const parameterType = validationRuleParameterType(rule);
        const item = {
            rule: String(rule || '').trim(),
            param: '',
            param_second: '',
            error_message: errorMessage,
            unique: {
                table: '',
                column: '',
                primary_key: '',
                ignore_value: '',
                ignore_from_data: '',
                where: '',
                whereNot: ''
            }
        };
        if (parameterType === 'pair') {
            const values = Array.isArray(param) ? param : String(param || '').split(',');
            item.param = String(values[0] ?? '').trim();
            item.param_second = String(values[1] ?? '').trim();
        } else if (parameterType === 'csv') {
            item.param = Array.isArray(param) ? param.join(', ') : String(param || '');
        } else if (parameterType === 'unique') {
            if (param && typeof param === 'object' && !Array.isArray(param)) {
                item.unique.table = String(param.table || '');
                item.unique.column = String(param.column || '');
                item.unique.primary_key = String(param.primary_key || '');
                item.unique.ignore_value = String(param.ignore_value ?? '');
                item.unique.ignore_from_data = String(param.ignore_from_data || '');
                item.unique.where = validationKeyValueText(param.where);
                item.unique.whereNot = validationKeyValueText(param.whereNot);
            } else {
                item.unique.table = String(param || '');
            }
        } else {
            item.param = Array.isArray(param) ? param.join(', ') : String(param ?? '');
        }
        return item;
    }

    function validationKeyValueText(value) {
        if (!value || typeof value !== 'object' || Array.isArray(value)) return '';
        return Object.entries(value).map(([key, item]) => `${key}=${String(item)}`).join(', ');
    }

    function validationScalar(value) {
        const text = String(value ?? '').trim();
        if (text === '') return '';
        if (text === 'true') return true;
        if (text === 'false') return false;
        if (text === 'null') return null;
        if (/^-?\d+(?:\.\d+)?$/.test(text)) return Number(text);
        return text;
    }

    function validationKeyValueObject(value) {
        const result = {};
        for (const piece of String(value || '').split(',')) {
            const trimmed = piece.trim();
            if (!trimmed) continue;
            const separator = trimmed.indexOf('=');
            const key = (separator >= 0 ? trimmed.slice(0, separator) : trimmed).trim();
            if (!key) continue;
            const rawValue = separator >= 0 ? trimmed.slice(separator + 1).trim() : '1';
            result[key] = validationScalar(rawValue);
        }
        return result;
    }

    function validationFields(node, property = 'rules_json') {
        const raw = parseJson(node?.data?.[property], {});
        if (!raw || typeof raw !== 'object' || Array.isArray(raw)) return [];
        return Object.entries(raw).map(([fieldName, definition]) => {
            let displayName = '';
            let rawRules = definition;
            if (definition && typeof definition === 'object' && !Array.isArray(definition) && Array.isArray(definition.rules)) {
                displayName = String(definition.name || '');
                rawRules = definition.rules;
            }
            return {
                field_name: String(fieldName),
                display_name: displayName,
                rules: (Array.isArray(rawRules) ? rawRules : []).map(validationRuleFromRaw)
            };
        });
    }

    function validationRuleToRaw(item) {
        const rule = String(item.rule || '').trim();
        if (!rule) return null;
        const parameterType = validationRuleParameterType(rule);
        const errorMessage = String(item.error_message || '').trim();
        let param = '';
        let advanced = false;
        if (parameterType === 'pair') {
            const first = String(item.param || '').trim();
            const second = String(item.param_second || '').trim();
            param = [first, second].filter(value => value !== '');
        } else if (parameterType === 'csv') {
            param = String(item.param || '').split(',').map(value => value.trim()).filter(Boolean);
        } else if (parameterType === 'unique') {
            const unique = item.unique || {};
            const table = String(unique.table || '').trim();
            const column = String(unique.column || '').trim();
            const primaryKey = String(unique.primary_key || '').trim();
            const ignoreValue = String(unique.ignore_value ?? '').trim();
            const ignoreFromData = String(unique.ignore_from_data || '').trim();
            const where = validationKeyValueObject(unique.where);
            const whereNot = validationKeyValueObject(unique.whereNot);
            advanced = Boolean(column || primaryKey || ignoreValue !== '' || ignoreFromData || Object.keys(where).length || Object.keys(whereNot).length);
            if (advanced || errorMessage) {
                param = {};
                if (table) param.table = table;
                if (column) param.column = column;
                if (primaryKey) param.primary_key = primaryKey;
                if (ignoreValue !== '') param.ignore_value = validationScalar(ignoreValue);
                if (ignoreFromData) param.ignore_from_data = ignoreFromData;
                if (Object.keys(where).length) param.where = where;
                if (Object.keys(whereNot).length) param.whereNot = whereNot;
            } else {
                param = table;
            }
        } else if (parameterType !== 'none') {
            param = String(item.param || '').trim();
        }
        if (!errorMessage && !advanced) {
            if (Array.isArray(param)) return `${rule}${param.length ? ':' + param.join(',') : ''}`;
            return `${rule}${param !== '' ? ':' + String(param) : ''}`;
        }
        const result = { rule };
        if ((Array.isArray(param) && param.length) || (param && typeof param === 'object' && Object.keys(param).length) || (typeof param === 'string' && param !== '')) {
            result.param = param;
        }
        if (errorMessage) result.error_message = errorMessage;
        return result;
    }

    function writeValidationFields(node, property, fields) {
        const rules = {};
        for (const field of fields) {
            const fieldName = String(field.field_name || '').trim();
            if (!fieldName) continue;
            const items = (Array.isArray(field.rules) ? field.rules : []).map(validationRuleToRaw).filter(Boolean);
            const displayName = String(field.display_name || '').trim();
            rules[fieldName] = displayName ? { name: displayName, rules: items } : items;
        }
        node.data[property] = JSON.stringify(rules, null, 2);
    }

    function validationRuleOptions(selectedRule) {
        const groups = new Map();
        for (const definition of validationRules) {
            const category = String(definition.category || 'Other');
            if (!groups.has(category)) groups.set(category, []);
            groups.get(category).push(definition);
        }
        let options = '';
        for (const [category, items] of groups) {
            options += `<optgroup label="${attr(category)}">${items.map(item => `<option value="${attr(item.id)}" ${String(selectedRule) === String(item.id) ? 'selected' : ''}>${esc(item.name || item.id)}</option>`).join('')}</optgroup>`;
        }
        if (selectedRule && !validationRuleDefinition(selectedRule)) {
            options += `<optgroup label="Custom"><option value="${attr(selectedRule)}" selected>${esc(selectedRule)} (custom)</option></optgroup>`;
        }
        options += '<optgroup label="Custom"><option value="__custom__">Custom rule…</option></optgroup>';
        return options;
    }

    function validationRuleParameterControl(item, propertyName, fieldIndex, ruleIndex, nodeId) {
        const definition = validationRuleDefinition(item.rule);
        const type = validationRuleParameterType(item.rule);
        const base = `data-validation-property="${attr(propertyName)}" data-validation-field-index="${fieldIndex}" data-validation-rule-index="${ruleIndex}" data-inspector-node-id="${attr(nodeId)}"`;
        if (type === 'none') return '';
        if (type === 'pair') {
            const labels = definition?.parameter_labels || ['First parameter', 'Second parameter'];
            const placeholders = definition?.placeholders || ['', ''];
            return `<div class="tvi-validation-rule__params tvi-validation-rule__params--pair"><label><span>${esc(labels[0])}</span><input type="text" value="${attr(item.param || '')}" placeholder="${attr(placeholders[0] || '')}" ${base} data-validation-target="param"></label><label><span>${esc(labels[1])}</span><input type="text" value="${attr(item.param_second || '')}" placeholder="${attr(placeholders[1] || '')}" ${base} data-validation-target="param_second"></label></div>`;
        }
        if (type === 'unique') {
            const unique = item.unique || {};
            const field = (name, label, placeholder = '') => `<label><span>${esc(label)}</span><input type="text" value="${attr(unique[name] ?? '')}" placeholder="${attr(placeholder)}" ${base} data-validation-target="unique.${attr(name)}"></label>`;
            return `<details class="tvi-validation-unique" open><summary>Unique rule options</summary><div class="tvi-validation-rule__params tvi-validation-rule__params--unique">${field('table','Table','users')}${field('column','Column','email')}${field('primary_key','Primary key','id')}${field('ignore_value','Ignore value','42')}${field('ignore_from_data','Ignore value from data field','user_id')}${field('where','Where conditions','deleted=0, school_id=12')}${field('whereNot','Where-not conditions','status=deleted')}</div><small>Condition lists use <code>column=value</code> pairs separated by commas.</small></details>`;
        }
        const label = definition?.parameter_label || 'Rule parameter';
        const placeholder = definition?.placeholder || 'value';
        return `<label><span>${esc(label)}</span><input type="text" value="${attr(item.param || '')}" placeholder="${attr(placeholder)}" ${base} data-validation-target="param"></label>`;
    }

    function validationRulesControl(node, property) {
        const fields = validationFields(node, property.name);
        const rows = fields.map((field, fieldIndex) => {
            const ruleRows = field.rules.map((item, ruleIndex) => {
                const definition = validationRuleDefinition(item.rule);
                const base = `data-validation-property="${attr(property.name)}" data-validation-field-index="${fieldIndex}" data-validation-rule-index="${ruleIndex}" data-inspector-node-id="${attr(node.id)}"`;
                const customName = !definition ? `<label><span>Custom rule name</span><input type="text" value="${attr(item.rule || '')}" placeholder="phone_zm" ${base} data-validation-target="rule"></label>` : '';
                return `<article class="tvi-validation-rule-row"><div class="tvi-validation-rule-row__head"><label><span>Rule</span><select ${base} data-validation-target="rule_select">${validationRuleOptions(item.rule)}</select></label><button type="button" class="tvi-danger tvi-icon-button" data-inspector-action="remove-validation-rule" data-validation-property="${attr(property.name)}" data-validation-field-index="${fieldIndex}" data-validation-rule-index="${ruleIndex}" data-inspector-node-id="${attr(node.id)}" title="Remove rule" aria-label="Remove rule">×</button></div>${customName}${validationRuleParameterControl(item, property.name, fieldIndex, ruleIndex, node.id)}<label><span>Custom error message <small>optional</small></span><input type="text" value="${attr(item.error_message || '')}" placeholder="Use the framework default message" ${base} data-validation-target="error_message"></label><div class="tvi-validation-rule-help"><strong>${esc(definition?.description || 'A custom validation rule registered by your application.')}</strong>${definition?.syntax ? `<code>${esc(definition.syntax)}</code>` : ''}</div></article>`;
            }).join('');
            return `<details class="tvi-validation-field" open><summary><strong>${esc(field.field_name || 'Unnamed field')}</strong><span>${field.rules.length} rule${field.rules.length === 1 ? '' : 's'}</span></summary><div class="tvi-validation-field__body"><div class="tvi-validation-field__identity"><label><span>Data field</span><input type="text" value="${attr(field.field_name || '')}" placeholder="email" data-validation-property="${attr(property.name)}" data-validation-field-index="${fieldIndex}" data-validation-target="field_name" data-inspector-node-id="${attr(node.id)}"></label><label><span>Display name <small>optional</small></span><input type="text" value="${attr(field.display_name || '')}" placeholder="Email address" data-validation-property="${attr(property.name)}" data-validation-field-index="${fieldIndex}" data-validation-target="display_name" data-inspector-node-id="${attr(node.id)}"></label></div><div class="tvi-validation-rule-list">${ruleRows || '<p class="tvi-muted">No rules added to this field.</p>'}</div><div class="tvi-validation-field__actions"><button type="button" data-inspector-action="add-validation-rule" data-validation-property="${attr(property.name)}" data-validation-field-index="${fieldIndex}" data-inspector-node-id="${attr(node.id)}">Add Rule</button><button type="button" class="tvi-danger" data-inspector-action="remove-validation-field" data-validation-property="${attr(property.name)}" data-validation-field-index="${fieldIndex}" data-inspector-node-id="${attr(node.id)}">Remove Field</button></div></div></details>`;
        }).join('');
        const expand = state.managerEditor?.nodeId === node.id && state.managerEditor?.property === property.name ? '' : `<button type="button" class="tvi-manager-expand" data-inspector-action="open-manager-editor" data-manager-kind="validation" data-manager-property="${attr(property.name)}" data-inspector-node-id="${attr(node.id)}">Expand Rules Editor</button>`;
        return `<div class="tvi-validation-editor"><div class="tvi-manager-inline-actions">${expand}</div>${rows || '<p class="tvi-muted">No validation fields defined.</p>'}<div class="tvi-validation-editor__actions"><button type="button" data-inspector-action="add-validation-field" data-validation-property="${attr(property.name)}" data-inspector-node-id="${attr(node.id)}">Add Field</button><button type="button" data-inspector-action="open-code-editor" data-editor-property="${attr(property.name)}" data-inspector-node-id="${attr(node.id)}">Edit Raw JSON</button></div><small class="tvi-muted">Built-in rules come from <code>validation-rules/rules.json</code>. Custom rules remain available through “Custom rule”.</small></div>`;
    }

    function migrationColumns(node, property = 'columns_json') {
        return parseJson(node?.data?.[property], []).filter(item => item && typeof item === 'object');
    }

    function migrationColumnsControl(node, property) {
        const items = migrationColumns(node, property.name);
        const types = ['INT','BIGINT','TINYINT','SMALLINT','DECIMAL','FLOAT','DOUBLE','VARCHAR','CHAR','TEXT','LONGTEXT','ENUM','DATE','DATETIME','TIMESTAMP','TIME','BOOLEAN','JSON','BLOB'];
        const rows = items.map((item, index) => `<article class="tvi-column-row"><div class="tvi-column-row__grid"><label><span>Name</span><input type="text" value="${attr(item.name || '')}" data-column-property="${attr(property.name)}" data-column-index="${index}" data-column-field="name" data-inspector-node-id="${attr(node.id)}"></label><label><span>Type</span><select data-column-property="${attr(property.name)}" data-column-index="${index}" data-column-field="type" data-inspector-node-id="${attr(node.id)}">${types.map(type => `<option value="${type}" ${String(item.type || 'VARCHAR').toUpperCase() === type ? 'selected' : ''}>${type}</option>`).join('')}</select></label><label><span>Length</span><input type="text" value="${attr(item.length || '')}" placeholder="255 or 10,2" data-column-property="${attr(property.name)}" data-column-index="${index}" data-column-field="length" data-inspector-node-id="${attr(node.id)}"></label><label><span>Default</span><input type="text" value="${attr(item.default || '')}" placeholder="CURRENT_TIMESTAMP" data-column-property="${attr(property.name)}" data-column-index="${index}" data-column-field="default" data-inspector-node-id="${attr(node.id)}"></label><label class="tvi-enum-values-field" ${String(item.type || '').toUpperCase() === 'ENUM' ? '' : 'hidden'}><span>Enum values</span><input type="text" value="${attr(item.enum_values || '')}" placeholder="draft,published,archived" data-column-property="${attr(property.name)}" data-column-index="${index}" data-column-field="enum_values" data-inspector-node-id="${attr(node.id)}"><small>Comma-separated values; quotes are optional.</small></label></div><div class="tvi-column-row__checks"><label><input type="checkbox" ${item.unsigned ? 'checked' : ''} data-column-property="${attr(property.name)}" data-column-index="${index}" data-column-field="unsigned" data-inspector-node-id="${attr(node.id)}">Unsigned</label><label><input type="checkbox" ${item.nullable ? 'checked' : ''} data-column-property="${attr(property.name)}" data-column-index="${index}" data-column-field="nullable" data-inspector-node-id="${attr(node.id)}">Nullable</label><label><input type="checkbox" ${item.auto_increment ? 'checked' : ''} data-column-property="${attr(property.name)}" data-column-index="${index}" data-column-field="auto_increment" data-inspector-node-id="${attr(node.id)}">Auto increment</label></div><label><span>Extra SQL <small>optional</small></span><input type="text" value="${attr(item.extra || '')}" placeholder="ON UPDATE CURRENT_TIMESTAMP" data-column-property="${attr(property.name)}" data-column-index="${index}" data-column-field="extra" data-inspector-node-id="${attr(node.id)}"></label><div class="tvi-column-row__actions"><button type="button" data-inspector-action="copy-migration-column" data-column-property="${attr(property.name)}" data-column-index="${index}" data-inspector-node-id="${attr(node.id)}">Copy Column</button><button type="button" class="tvi-danger" data-inspector-action="remove-migration-column" data-column-property="${attr(property.name)}" data-column-index="${index}" data-inspector-node-id="${attr(node.id)}">Remove Column</button></div></article>`).join('');
        const expand = state.managerEditor?.nodeId === node.id && state.managerEditor?.property === property.name ? '' : `<button type="button" class="tvi-manager-expand" data-inspector-action="open-manager-editor" data-manager-kind="migration-columns" data-manager-property="${attr(property.name)}" data-inspector-node-id="${attr(node.id)}">Expand Columns Editor</button>`;
        return `<div class="tvi-columns-editor"><div class="tvi-manager-inline-actions">${expand}</div>${rows || '<p class="tvi-muted">No columns defined.</p>'}<button type="button" data-inspector-action="add-migration-column" data-column-property="${attr(property.name)}" data-inspector-node-id="${attr(node.id)}">Add Column</button></div>`;
    }

    function catalogItems(node, kind) {
        const property = kind === 'permission' ? 'permissions_json' : 'roles_json';
        return parseJson(node?.data?.[property], []).filter(item => item && typeof item === 'object');
    }

    function catalogControl(node, property, kind) {
        const items = catalogItems(node, kind);
        const fields = kind === 'permission'
            ? [['name', 'Name'], ['slug', 'Slug'], ['group', 'Group'], ['description', 'Description']]
            : [['name', 'Name'], ['slug', 'Slug'], ['description', 'Description']];
        const rows = items.map((item, index) => `<article class="tvi-catalog-row"><div>${fields.map(([field, label]) => `<label><span>${esc(label)}</span><input type="text" value="${attr(item[field] || '')}" data-catalog-kind="${attr(kind)}" data-catalog-property="${attr(property.name)}" data-catalog-index="${index}" data-catalog-field="${attr(field)}" data-inspector-node-id="${attr(node.id)}"></label>`).join('')}</div><div class="tvi-catalog-row__actions">${kind === 'permission' ? `<button type="button" data-inspector-action="copy-catalog-item" data-catalog-kind="${attr(kind)}" data-catalog-property="${attr(property.name)}" data-catalog-index="${index}" data-inspector-node-id="${attr(node.id)}">Copy Permission</button>` : ''}<button type="button" class="tvi-danger" data-inspector-action="remove-catalog-item" data-catalog-kind="${attr(kind)}" data-catalog-property="${attr(property.name)}" data-catalog-index="${index}" data-inspector-node-id="${attr(node.id)}">Remove</button></div></article>`).join('');
        return `<div class="tvi-catalog-editor">${rows || `<p class="tvi-muted">No ${esc(kind)} entries yet.</p>`}<button type="button" data-inspector-action="add-catalog-item" data-catalog-kind="${attr(kind)}" data-catalog-property="${attr(property.name)}">Add ${esc(kind)}</button></div>`;
    }

    function dependencyItems(node, property = 'dependencies') {
        const raw = parseJson(node?.data?.[property], {});
        if (Array.isArray(raw)) {
            return raw.filter(item => item && typeof item === 'object').map(item => ({
                id: String(item.id || ''),
                name: String(item.name || item.id || ''),
                version: String(item.version || '1.0.0'),
                required: Boolean(item.required)
            }));
        }
        if (!raw || typeof raw !== 'object') return [];
        return Object.entries(raw).map(([id, item]) => ({
            id,
            name: String(item?.name || id),
            version: String(item?.version || '1.0.0'),
            required: Boolean(item?.required)
        }));
    }

    function writeDependencyItems(node, property, items) {
        const dependencies = {};
        for (const item of items) {
            const id = String(item.id || '').trim();
            if (!id) continue;
            dependencies[id] = {
                name: String(item.name || id).trim() || id,
                version: String(item.version || '1.0.0').trim() || '1.0.0',
                required: Boolean(item.required)
            };
        }
        node.data[property] = JSON.stringify(dependencies, null, 2);
    }

    function dependencyControl(node, property) {
        const items = dependencyItems(node, property.name);
        const rows = items.map((item, index) => `<article class="tvi-catalog-row tvi-dependency-row"><div><label><span>Plugin ID</span><input type="text" value="${attr(item.id)}" data-dependency-property="${attr(property.name)}" data-dependency-index="${index}" data-dependency-field="id" data-inspector-node-id="${attr(node.id)}"></label><label><span>Name</span><input type="text" value="${attr(item.name)}" data-dependency-property="${attr(property.name)}" data-dependency-index="${index}" data-dependency-field="name" data-inspector-node-id="${attr(node.id)}"></label><label><span>Version</span><input type="text" value="${attr(item.version)}" placeholder="^1.0.0" data-dependency-property="${attr(property.name)}" data-dependency-index="${index}" data-dependency-field="version" data-inspector-node-id="${attr(node.id)}"></label><label class="tvi-field--checkbox"><span>Required</span><input type="checkbox" ${item.required ? 'checked' : ''} data-dependency-property="${attr(property.name)}" data-dependency-index="${index}" data-dependency-field="required" data-inspector-node-id="${attr(node.id)}"></label></div><button type="button" class="tvi-danger" data-inspector-action="remove-dependency" data-dependency-property="${attr(property.name)}" data-dependency-index="${index}">Remove</button></article>`).join('');
        return `<div class="tvi-catalog-editor tvi-dependency-editor">${rows || '<p class="tvi-muted">No plugin dependencies defined.</p>'}<button type="button" data-inspector-action="add-dependency" data-dependency-property="${attr(property.name)}">Add Dependency</button></div>`;
    }

    function catalogReferenceSelect(node, property, kind) {
        const type = kind === 'permission' ? 'authorization.all_permissions' : 'authorization.all_roles';
        const items = mainGraph().nodes.filter(item => item.type === type).flatMap(item => catalogItems(item, kind));
        const value = String(node.data[property.name] || '');
        const options = items.map(item => {
            const slug = String(item.slug || '').trim();
            if (!slug) return '';
            const label = String(item.name || slug);
            return `<option value="${attr(slug)}" ${value === slug ? 'selected' : ''}>${esc(label)} (${esc(slug)})</option>`;
        }).join('');
        return `<select data-property="${attr(property.name)}" data-inspector-node-id="${attr(node.id)}"><option value="">Select…</option>${options}${value && !items.some(item => String(item.slug || '') === value) ? `<option value="${attr(value)}" selected>${esc(value)} (manual)</option>` : ''}</select>`;
    }

    function breakpointControl(node, p) {
        const widths = (node.data[p.name] && typeof node.data[p.name] === 'object') ? node.data[p.name] : {};
        const labels = { mobile: 'Mobile', small: 'Small ≥576', medium: 'Medium ≥768', large: 'Large ≥1200' };
        const options = Array.from({length:12}, (_,i) => i + 1).map(value => `<option value="${value}">${value} / 12</option>`).join('');
        return `<div class="tvi-breakpoints">${Object.entries(labels).map(([key,label]) => `<label><span>${esc(label)}</span><select data-breakpoint-property="${attr(p.name)}" data-inspector-node-id="${attr(node.id)}" data-breakpoint="${key}">${options.replace(`value="${Number(widths[key] || 12)}"`,`value="${Number(widths[key] || 12)}" selected`)}</select></label>`).join('')}</div>`;
    }

    function paginationTemplateById(id) {
        return paginationTemplates.find(template => String(template.id) === String(id)) || paginationTemplates[0] || null;
    }

    function paginationTemplateSelect(node, property) {
        const value = String(node.data[property.name] || '');
        const options = paginationTemplates.map(template => `<option value="${attr(template.id)}" ${value === String(template.id) ? 'selected' : ''}>${esc(template.name)}</option>`).join('');
        return `<div class="tvi-pagination-template-select"><select data-property="${attr(property.name)}" data-inspector-node-id="${attr(node.id)}">${options}</select><button type="button" data-inspector-action="apply-pagination-template">Apply Design Defaults</button><button type="button" data-action="open-pagination-templates">Browse Designs…</button></div>`;
    }



    const paginationPaletteRoles = [
        ['button_background', 'Button background', 'surface'],
        ['button_text', 'Button text', 'text'],
        ['button_border', 'Button border', 'border'],
        ['hover_background', 'Hover background', 'surface_alt'],
        ['hover_text', 'Hover text', 'text'],
        ['active_background', 'Active background', 'primary'],
        ['active_text', 'Active text', 'on_primary'],
        ['disabled_background', 'Disabled background', 'surface_alt'],
        ['disabled_text', 'Disabled text', 'muted'],
        ['summary_text', 'Summary text', 'muted']
    ];

    const paginationThemeColors = [
        ['primary', 'Primary'], ['secondary', 'Secondary'], ['surface', 'Surface'],
        ['surface_alt', 'Alternate surface'], ['text', 'Text'], ['muted', 'Muted text'],
        ['border', 'Border'], ['on_primary', 'Text on primary'], ['danger', 'Danger'],
        ['success', 'Success'], ['warning', 'Warning'], ['info', 'Info']
    ];

    function paginationPaletteControl(node) {
        const mode = ['defaults', 'custom', 'inherit'].includes(String(node.data.color_mode))
            ? String(node.data.color_mode)
            : 'defaults';
        const modeOptions = [
            ['defaults', 'Use design defaults'],
            ['custom', 'Use custom colors'],
            ['inherit', 'Inherit from Look theme']
        ].map(([value, label]) => `<option value="${value}" ${mode === value ? 'selected' : ''}>${esc(label)}</option>`).join('');
        let body = '<p class="tvi-muted">The selected pagination design supplies its original palette.</p>';
        if (mode === 'custom') {
            body = `<div class="tvi-pagination-palette-grid">${paginationPaletteRoles.map(([role, label]) => {
                const field = `custom_${role}`;
                const value = String(node.data[field] || '');
                const pickerValue = /^#[0-9a-f]{6}$/i.test(value) ? value : '#000000';
                return `<div class="tvi-pagination-color-row"><label><span>${esc(label)}</span><input type="text" value="${attr(value)}" data-pagination-palette-field="${attr(field)}" data-inspector-node-id="${attr(node.id)}"></label><input type="color" value="${attr(pickerValue)}" data-pagination-palette-picker="${attr(field)}" data-inspector-node-id="${attr(node.id)}" aria-label="Choose ${attr(label)}"></div>`;
            }).join('')}</div>`;
        } else if (mode === 'inherit') {
            const options = selected => paginationThemeColors.map(([value, label]) => `<option value="${value}" ${selected === value ? 'selected' : ''}>${esc(label)}</option>`).join('');
            body = `<div class="tvi-pagination-theme-map">${paginationPaletteRoles.map(([role, label, fallback]) => {
                const field = `theme_${role}`;
                const selected = String(node.data[field] || fallback);
                return `<label><span>${esc(label)}</span><select data-pagination-palette-field="${attr(field)}" data-inspector-node-id="${attr(node.id)}">${options(selected)}</select></label>`;
            }).join('')}</div><small>Each pagination role can inherit a different named color from the connected Look.</small>`;
        }
        if (mode === 'custom') {
            body += `<div class="tvi-pagination-palette-actions"><button type="button" data-inspector-action="export-pagination-colors" data-inspector-node-id="${attr(node.id)}">Export Colors</button><button type="button" data-inspector-action="import-pagination-colors" data-inspector-node-id="${attr(node.id)}">Import Colors</button></div><small>Export this custom palette once and reuse it with any pagination design.</small>`;
        }
        return `<div class="tvi-pagination-palette-control"><label><span>Color source</span><select data-pagination-palette-field="color_mode" data-inspector-node-id="${attr(node.id)}">${modeOptions}</select></label>${body}</div>`;
    }

    function paginationPaletteSettings(data = {}) {
        const settings = { color_mode: String(data.color_mode || 'defaults') };
        for (const [role, , themeFallback] of paginationPaletteRoles) {
            settings[`custom_${role}`] = String(data[`custom_${role}`] || '');
            settings[`theme_${role}`] = String(data[`theme_${role}`] || themeFallback);
        }
        return settings;
    }

    function paginationCustomColors(data = {}) {
        const colors = {};
        for (const [role] of paginationPaletteRoles) {
            colors[role] = String(data[`custom_${role}`] || '').trim();
        }
        return colors;
    }

    function applyPaginationCustomColors(node, colors) {
        if (!node || node.type !== 'view.pagination' || !colors || typeof colors !== 'object') return false;
        let applied = false;
        for (const [role] of paginationPaletteRoles) {
            const value = String(colors[role] ?? colors[`custom_${role}`] ?? '').trim();
            if (!value) continue;
            node.data[`custom_${role}`] = value;
            applied = true;
        }
        if (applied) node.data.color_mode = 'custom';
        return applied;
    }

    function exportPaginationColors(node) {
        if (!node || node.type !== 'view.pagination') {
            toast('Select a Pagination Component first.', 'warn');
            return;
        }
        const payload = {
            format: 'thunder-visual-ide-pagination-palette',
            format_version: 1,
            colors: paginationCustomColors(node.data)
        };
        const base = String(node.data.component_name || node.data.title || 'pagination').replace(/[^a-z0-9_-]+/gi, '-').replace(/^-+|-+$/g, '') || 'pagination';
        downloadBlob(JSON.stringify(payload, null, 2), `${base}-colors.json`, 'application/json');
        toast('Pagination colors exported.', 'success');
    }

    function importPaginationColors(node) {
        if (!node || node.type !== 'view.pagination') {
            toast('Select a Pagination Component first.', 'warn');
            return;
        }
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'application/json,.json';
        input.addEventListener('change', () => {
            const file = input.files?.[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = () => {
                try {
                    const payload = JSON.parse(String(reader.result || '{}'));
                    const colors = payload?.colors && typeof payload.colors === 'object' ? payload.colors : payload;
                    if (!colors || typeof colors !== 'object' || Array.isArray(colors)) {
                        throw new Error('The file does not contain pagination colors.');
                    }
                    snapshot();
                    if (!applyPaginationCustomColors(node, colors)) {
                        throw new Error('No recognized pagination color roles were found.');
                    }
                    changed();
                    renderAll();
                    toast('Pagination colors imported.', 'success');
                } catch (error) {
                    toast(error.message || 'Unable to import pagination colors.', 'fail');
                }
            };
            reader.readAsText(file);
        }, { once: true });
        input.click();
    }

    function paginationColorTokens(node, template) {
        const mode = ['defaults', 'custom', 'inherit'].includes(String(node.data.color_mode))
            ? String(node.data.color_mode)
            : 'defaults';
        const defaults = template?.palette || {};
        const tokens = {};
        for (const [role, , themeFallback] of paginationPaletteRoles) {
            const fallback = String(defaults[role] || '#000000');
            let value = fallback;
            if (mode === 'custom') {
                value = String(node.data[`custom_${role}`] || fallback).trim() || fallback;
            } else if (mode === 'inherit') {
                const mapped = String(node.data[`theme_${role}`] || themeFallback).replace(/[^a-z0-9_]/gi, '') || themeFallback;
                value = `var(--thv-theme-${mapped.replaceAll('_', '-')}, ${fallback})`;
            }
            tokens[`{{${role}}}`] = value;
        }
        return tokens;
    }

    function paginationClassToken(classes, fallback) {
        const token = String(classes || '').trim().split(/\s+/).find(Boolean) || fallback;
        return token.replace(/[^A-Za-z0-9_-]/g, '') || fallback;
    }

    function paginationPreviewMarkup(node, template) {
        const data = node.data || {};
        const defaults = template?.defaults || {};
        let html = String(template?.preview_html || '<div>Pagination preview unavailable.</div>');
        const replacements = [
            ['wrapper_class', 'tvi-pager-wrapper'], ['buttons_class', 'tvi-pager-buttons'],
            ['button_class', 'tvi-pager-button'], ['active_class', 'is-active'],
            ['disabled_class', 'is-disabled'], ['ellipsis_class', 'is-ellipsis'],
            ['summary_class', 'tvi-pager-summary']
        ];
        for (const [key, fallback] of replacements) {
            const oldValue = paginationClassToken(defaults[key], fallback);
            const newValue = paginationClassToken(data[key], fallback);
            html = html.replaceAll(oldValue, newValue);
        }
        html = html.replaceAll('Previous', esc(String(data.prev_label || 'Previous')))
            .replaceAll('Next', esc(String(data.next_label || 'Next')));
        if (!data.show_summary) {
            const summaryClass = paginationClassToken(data.summary_class, 'tvi-pager-summary');
            html = html.replace(new RegExp(`<[^>]+class=["'][^"']*${summaryClass}[^"']*["'][^>]*>.*?<\/[^>]+>`, 'is'), '');
        }

        const holder = document.createElement('div');
        holder.innerHTML = html;
        const appendStyle = (elements, style) => {
            const value = String(style || '').trim();
            if (!value) return;
            [...elements].forEach(element => {
                element.style.cssText = `${element.style.cssText};${value}`;
            });
        };
        const byClass = (key, fallback) => holder.getElementsByClassName(
            paginationClassToken(data[key], fallback)
        );
        appendStyle(byClass('wrapper_class', 'tvi-pager-wrapper'), data.wrapper_style);
        appendStyle(byClass('buttons_class', 'tvi-pager-buttons'), data.buttons_style);
        appendStyle(byClass('button_class', 'tvi-pager-button'), data.button_style);
        appendStyle(byClass('active_class', 'is-active'), data.active_style);
        appendStyle(byClass('disabled_class', 'is-disabled'), data.disabled_style);
        appendStyle(byClass('ellipsis_class', 'is-ellipsis'), data.ellipsis_style);
        appendStyle(byClass('summary_class', 'tvi-pager-summary'), data.summary_style);
        return holder.innerHTML;
    }

    function paginationPreviewCss(node, template, scopeClass) {
        const data = node.data || {};
        const tokens = {
            '{{scope}}': scopeClass,
            '{{wrapper}}': paginationClassToken(data.wrapper_class, scopeClass),
            '{{buttons}}': paginationClassToken(data.buttons_class, 'tvi-pager-buttons'),
            '{{button}}': paginationClassToken(data.button_class, 'tvi-pager-button'),
            '{{active}}': paginationClassToken(data.active_class, 'is-active'),
            '{{disabled}}': paginationClassToken(data.disabled_class, 'is-disabled'),
            '{{ellipsis}}': paginationClassToken(data.ellipsis_class, 'is-ellipsis'),
            '{{summary}}': paginationClassToken(data.summary_class, 'tvi-pager-summary')
        };
        let css = data.base_styles ? String(template?.css || '') : '';
        css += `\n${String(data.custom_css || '')}`;
        for (const [token, replacement] of Object.entries(tokens)) css = css.replaceAll(token, replacement);
        for (const [token, replacement] of Object.entries(paginationColorTokens(node, template))) css = css.replaceAll(token, replacement);
        return css;
    }

    function paginationNodePreview(node) {
        const template = paginationTemplateById(node.data.template_id);
        if (!template) return '<div class="tvi-view-tools"><h4>Live preview</h4><p class="tvi-muted">No pagination templates are installed.</p></div>';
        const scopeClass = `tvi-pagination-preview-${String(node.id).replace(/[^A-Za-z0-9_-]/g, '')}`;
        const themeVariables = themeVariableDeclarations(viewThemeState(viewForGraph()).colors);
        return `<div class="tvi-pagination-live" style="${attr(themeVariables)}"><div class="tvi-subhead"><strong>Live pagination preview</strong><span>${esc(template.name)}</span></div><style data-pagination-preview-style>${paginationPreviewCss(node, template, scopeClass)}</style><div class="tvi-pagination-preview ${attr(scopeClass)}" data-pagination-preview>${paginationPreviewMarkup(node, template)}</div><small>Preview uses sample page data. The generated component uses the real <code>Core\\Pager</code> object.</small></div>`;
    }

    function updatePaginationPreview(nodeId) {
        const node = nodeById(nodeId);
        if (!node || node.type !== 'view.pagination') return;
        const container = inspector.querySelector('[data-pagination-preview]');
        const style = inspector.querySelector('[data-pagination-preview-style]');
        if (!container || !style) return;
        const template = paginationTemplateById(node.data.template_id);
        const scopeClass = [...container.classList].find(name => name.startsWith('tvi-pagination-preview-')) || `tvi-pagination-preview-${String(node.id).replace(/[^A-Za-z0-9_-]/g, '')}`;
        container.innerHTML = paginationPreviewMarkup(node, template);
        style.textContent = paginationPreviewCss(node, template, scopeClass);
    }

    function applyPaginationTemplate(node, templateId, resetComponentName = false) {
        const template = paginationTemplateById(templateId);
        if (!node || !template) return;
        let componentName = String(node.data.component_name || template.defaults?.component_name || 'pagination');
        if (resetComponentName) {
            const base = `${String(template.id || 'pagination')}-pagination`;
            const used = new Set(activeGraph().nodes
                .filter(item => item.id !== node.id)
                .map(item => String(item.data?.component_name || '').trim())
                .filter(Boolean));
            componentName = base;
            let suffix = 2;
            while (used.has(componentName)) componentName = `${base}-${suffix++}`;
        }
        node.data = {
            ...node.data,
            ...(clone(template.defaults || {})),
            title: node.data.title || template.name,
            component_name: componentName,
            template_id: template.id
        };
    }

    function openPaginationTemplates() {
        if (activeGraph().kind !== 'view') {
            toast('Open a View Builder before adding pagination designs.', 'warn');
            return;
        }
        const selected = selectedNodes()[0];
        state.paginationTemplateSourceNodeId = selected?.type === 'view.pagination' ? selected.id : null;
        renderPaginationTemplates();
        paginationTemplatesModal.hidden = false;
    }

    function closePaginationTemplates() {
        paginationTemplatesModal.hidden = true;
        state.paginationTemplateSourceNodeId = null;
    }

    function renderPaginationTemplates() {
        const sourceNode = state.paginationTemplateSourceNodeId ? nodeById(state.paginationTemplateSourceNodeId) : null;
        const sourcePalette = sourceNode?.type === 'view.pagination' ? paginationPaletteSettings(sourceNode.data) : null;
        const themeVariables = themeVariableDeclarations(viewThemeState(viewForGraph()).colors);
        paginationTemplatesList.innerHTML = paginationTemplates.map(template => {
            const defaults = template.defaults || {};
            const sampleNode = {
                id: `template-${template.id}`,
                data: { ...clone(defaults), ...(sourcePalette || {}), template_id: template.id }
            };
            const scopeClass = `tvi-pagination-card-${String(template.id).replace(/[^A-Za-z0-9_-]/g, '')}`;
            return `<article class="tvi-pagination-template-card" style="${attr(themeVariables)}"><div><strong>${esc(template.name)}</strong><p>${esc(template.description || '')}</p></div><style>${paginationPreviewCss(sampleNode, template, scopeClass)}</style><div class="tvi-pagination-template-preview ${attr(scopeClass)}">${paginationPreviewMarkup(sampleNode, template)}</div><div class="tvi-pagination-template-actions"><code>pagination-templates/${esc(template.package_path || template.id)}</code><button type="button" data-pagination-template-add="${attr(template.id)}">Add to View</button></div></article>`;
        }).join('') || '<p class="tvi-muted">No pagination template packages are installed.</p>';
    }

    function paginationTemplateModalClick(event) {
        const button = event.target.closest('[data-pagination-template-add]');
        if (!button) return;
        if (activeGraph().kind !== 'view') {
            toast('Pagination designs can only be added inside a View Builder.', 'fail');
            return;
        }
        const template = paginationTemplateById(button.dataset.paginationTemplateAdd);
        if (!template) return;
        const sourceNode = state.paginationTemplateSourceNodeId ? nodeById(state.paginationTemplateSourceNodeId) : null;
        const sourcePalette = sourceNode?.type === 'view.pagination' ? paginationPaletteSettings(sourceNode.data) : null;
        const rect = viewport.getBoundingClientRect();
        const point = screenToWorld(rect.left + rect.width / 2, rect.top + rect.height / 2);
        const offset = (state.paletteInsertCount++ % 8) * 28;
        addNode('view.pagination', point.x - 110 + offset, point.y - 50 + offset);
        const node = selectedNodes()[0];
        if (node?.type === 'view.pagination') {
            snapshot();
            applyPaginationTemplate(node, template.id, true);
            if (sourcePalette) Object.assign(node.data, sourcePalette);
            changed();
            renderAll();
        }
        toast(`${template.name} pagination added.`, 'success');
    }

    function htmlComponentInspectorTools(node) {
        return `<div class="tvi-html-component-tools"><strong>HTML Component Studio</strong><p>Edit HTML, automatically namespaced CSS and JavaScript with a live isolated preview.</p><button type="button" data-inspector-action="open-html-designer">Open Fullscreen Designer</button></div>`;
    }


    async function refreshMarketplaceRuntimeAssets() {
        try {
            const response = await fetch(`${cfg.apiBase}/marketplace-assets-list`, { method:'POST', body:new FormData() });
            const data = await response.json();
            if (!response.ok || !data.ok) return false;
            formThemes.splice(0, formThemes.length, ...(Array.isArray(data.themes) ? data.themes.map(item => clone(item)) : []));
            htmlSnippets.splice(0, htmlSnippets.length, ...(Array.isArray(data.snippets) ? data.snippets.map(item => clone(item)) : []));
            return true;
        } catch (_) { return false; }
    }

    async function openMarketplaceStudio() {
        state.marketplaceStudio = {
            tab: 'theme',
            themes: [],
            snippets: [],
            starter: {},
            selectedId: null,
            draft: null,
            componentKey: 'field',
            paletteId: 'default',
            focusedColumn: '',
            loading: true
        };
        marketplaceModal.hidden = false;
        document.body.classList.add('tvi-modal-open');
        renderMarketplaceAssetList();
        await refreshMarketplaceStudioData('theme');
    }

    function closeMarketplaceStudio() {
        if (marketplaceModal) marketplaceModal.hidden = true;
        clearTimeout(state.marketplacePreviewTimer);
        state.marketplaceStudio = null;
        if (![...document.querySelectorAll('.tvi-modal')].some(modal => !modal.hidden)) {
            document.body.classList.remove('tvi-modal-open');
        }
    }

    async function refreshMarketplaceStudioData(preferType = '', preferId = '') {
        const studio = state.marketplaceStudio;
        if (!studio) return;
        studio.loading = true;
        renderMarketplaceAssetList();
        try {
            const response = await fetch(`${cfg.apiBase}/marketplace-assets-list`, { method: 'POST', body: new FormData() });
            const data = await response.json();
            if (!response.ok || !data.ok) {
                showIssues(data);
                return;
            }
            applyMarketplaceStudioData(data);
            studio.loading = false;
            if (preferType) studio.tab = preferType;
            const assets = marketplaceAssets();
            const selected = assets.find(item => String(item.id) === String(preferId))
                || assets.find(item => item.custom)
                || assets[0]
                || null;
            if (selected) loadMarketplaceAsset(selected.id);
            else newMarketplaceAsset();
        } catch (error) {
            studio.loading = false;
            toast(error.message, 'fail');
            renderMarketplaceAssetList();
        }
    }

    function applyMarketplaceStudioData(data) {
        const studio = state.marketplaceStudio;
        if (!studio) return;
        studio.themes = Array.isArray(data.themes) ? clone(data.themes) : [];
        studio.snippets = Array.isArray(data.snippets) ? clone(data.snippets) : [];
        studio.starter = data.starter && typeof data.starter === 'object' ? clone(data.starter) : {};
        formThemes.splice(0, formThemes.length, ...studio.themes.map(item => clone(item)));
        htmlSnippets.splice(0, htmlSnippets.length, ...studio.snippets.map(item => clone(item)));
    }

    function marketplaceAssets() {
        const studio = state.marketplaceStudio;
        return studio?.tab === 'snippet' ? studio.snippets : studio?.themes || [];
    }

    function marketplaceAssetById(id) {
        return marketplaceAssets().find(item => String(item.id) === String(id)) || null;
    }

    function marketplaceSlug(value) {
        return String(value || '').trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }

    function marketplacePalettesObject(value) {
        if (!value) return {};
        if (!Array.isArray(value)) return clone(value);
        const result = {};
        value.forEach(palette => {
            const id = marketplaceSlug(palette?.id || palette?.name || 'default') || 'default';
            result[id] = { name: palette?.name || id, colors: formThemeColorMap(palette?.colors || {}) };
        });
        return result;
    }

    function marketplaceStarterTheme() {
        const colors = clone(state.marketplaceStudio?.starter?.theme_colors || {
            primary:'#2563eb',secondary:'#64748b',surface:'#ffffff',surface_alt:'#f8fafc',text:'#0f172a',muted:'#64748b',border:'#cbd5e1',danger:'#b91c1c',on_primary:'#ffffff',success:'#16a34a',warning:'#d97706',info:'#0891b2'
        });
        return {
            id: 'my-form-theme', original_id: '', name: 'My Form Theme', version: '1.0.0', author: '', website: '', license: 'Commercial', category: 'Forms', tags: [], order: 100,
            description: 'A custom form theme created in Thunder Visual IDE.', source: 'custom', custom: true, editable: true, isNew: true,
            default_palette: 'default', palettes: { default: { name: 'Default', colors } },
            preview_html: `<div class="tvi-theme-preview">
  <form class="thv-form thv-grid" style="--thv-gap:1rem">
    <div class="thv-field thv-col-mobile-12 thv-col-medium-6"><label for="preview-name">Full name</label><input id="preview-name" type="text" placeholder="Steve Choongo"></div>
    <div class="thv-field thv-col-mobile-12 thv-col-medium-6"><label for="preview-email">Email address</label><input id="preview-email" type="email" placeholder="steve@example.com"></div>
    <div class="thv-field thv-col-mobile-12"><label for="preview-role">Account type</label><select id="preview-role"><option>Administrator</option><option>Editor</option></select></div>
    <div class="thv-field thv-col-mobile-12"><label for="preview-message">About you</label><textarea id="preview-message" placeholder="Tell us something useful"></textarea></div>
    <div class="thv-field thv-checkbox thv-col-mobile-12"><input type="checkbox" checked><span class="thv-label">Send me product updates</span></div>
    <div class="thv-col-mobile-12"><button type="button" class="thv-button thv-button--primary">Create account</button></div>
  </form>
</div>`,
            css: `{{scope}}{font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:var(--thv-theme-text)}
{{scope}}.thv-form,{{scope}} .thv-form{padding:1.25rem;border:1px solid var(--thv-theme-border);border-radius:1rem;background:var(--thv-theme-surface)}
{{scope}}.thv-field>label,{{scope}} .thv-field>label,{{scope}} .thv-label{color:var(--thv-theme-text);font-size:.9rem}
{{scope}}.thv-field :where(input:not([type=checkbox]):not([type=radio]),textarea,select),{{scope}} .thv-field :where(input:not([type=checkbox]):not([type=radio]),textarea,select){background:var(--thv-theme-surface);color:var(--thv-theme-text);border-color:var(--thv-theme-border)}
{{scope}} .thv-button--primary,{{scope}}.thv-button--primary{background:var(--thv-theme-primary);color:var(--thv-theme-on-primary);border-color:var(--thv-theme-primary)}`,
            js: '// Optional theme behaviour. Keep selectors inside {{scope}}.',
            components: clone(state.marketplaceStudio?.starter?.theme_components || {
                field:'<div{{wrapper_attributes}}>{{label_html}}{{control_html}}{{error_html}}</div>',textarea:'<div{{wrapper_attributes}}>{{label_html}}{{control_html}}{{error_html}}</div>',select:'<div{{wrapper_attributes}}>{{label_html}}{{control_html}}{{error_html}}</div>',checkbox:'<div{{wrapper_attributes}}>{{control_html}}{{label_html}}{{error_html}}</div>',button:'<div{{wrapper_attributes}}>{{control_html}}</div>',form:'<form{{wrapper_attributes}}>\n{{children_html}}\n</form>'
            })
        };
    }

    function marketplaceStarterSnippet() {
        return {
            id: 'my-html-snippet', original_id: '', name: 'My HTML Snippet', version: '1.0.0', author: '', website: '', license: 'Commercial', category: 'General', tags: [], order: 100,
            description: 'A reusable HTML component starter.', source: 'custom', custom: true, editable: true, isNew: true,
            html: `<section class="market-card">
  <h3>Reusable component</h3>
  <p>Replace this content with your own HTML.</p>
  <button type="button" data-action="continue">Continue</button>
</section>`,
            css: `.market-card{padding:1.25rem;border:1px solid var(--thv-theme-border,#cbd5e1);border-radius:1rem;background:var(--thv-theme-surface,#fff);color:var(--thv-theme-text,#0f172a)}
.market-card h3{margin:0 0 .5rem}
.market-card button{padding:.65rem 1rem;border:0;border-radius:.55rem;background:var(--thv-theme-primary,#2563eb);color:var(--thv-theme-on-primary,#fff)}`,
            js: `const button = root.querySelector('[data-action="continue"]');
button?.addEventListener('click', () => root.classList.toggle('is-active'));`
        };
    }

    function marketplaceDraftFromAsset(asset, copyAsset = false) {
        const studio = state.marketplaceStudio;
        const type = studio?.tab || 'theme';
        const draft = clone(asset || (type === 'theme' ? marketplaceStarterTheme() : marketplaceStarterSnippet()));
        draft.original_id = !copyAsset && draft.custom ? draft.id : '';
        draft.editable = copyAsset || draft.custom || draft.isNew;
        draft.custom = true;
        draft.source = draft.editable ? 'custom' : 'built-in';
        draft.isNew = copyAsset || !asset;
        if (copyAsset) {
            draft.id = `${marketplaceSlug(draft.id)}-custom`;
            draft.name = `${draft.name || draft.id} Custom`;
        }
        draft.tags = Array.isArray(draft.tags) ? draft.tags : String(draft.tags || '').split(',').map(v => v.trim()).filter(Boolean);
        if (type === 'theme') {
            draft.palettes = marketplacePalettesObject(draft.palettes);
            draft.components = { ...marketplaceStarterTheme().components, ...(draft.components || {}) };
        }
        return draft;
    }

    function marketplaceEnsurePalettes(draft) {
        if (!draft) return {};
        const palettes = marketplacePalettesObject(draft.palettes);
        if (!Object.keys(palettes).length) {
            palettes.default = {
                name: 'Default',
                colors: clone(state.marketplaceStudio?.starter?.theme_colors || marketplaceStarterTheme().palettes.default.colors)
            };
        }
        Object.entries(palettes).forEach(([id, palette]) => {
            palette.name = String(palette?.name || id);
            palette.colors = formThemeColorMap(palette?.colors || {});
        });
        draft.palettes = palettes;
        if (!palettes[draft.default_palette]) draft.default_palette = Object.keys(palettes)[0];
        const studio = state.marketplaceStudio;
        if (studio && !palettes[studio.paletteId]) studio.paletteId = draft.default_palette;
        return palettes;
    }

    function marketplaceSelectedPalette() {
        const studio = state.marketplaceStudio;
        const draft = studio?.draft;
        if (!studio || !draft || studio.tab !== 'theme') return null;
        const palettes = marketplaceEnsurePalettes(draft);
        const id = palettes[studio.paletteId] ? studio.paletteId : draft.default_palette;
        studio.paletteId = id;
        return { id, palette: palettes[id], palettes };
    }

    function renderMarketplacePaletteManager() {
        const selected = marketplaceSelectedPalette();
        const studio = state.marketplaceStudio;
        const draft = studio?.draft;
        if (!selected || !marketplacePaletteList || !marketplacePaletteColors) return;
        const readonly = !draft.editable;
        marketplacePaletteList.innerHTML = Object.entries(selected.palettes).map(([id, palette]) => {
            const swatch = palette.colors?.primary || palette.colors?.surface || '#64748b';
            const isDefault = id === draft.default_palette;
            return `<button type="button" class="tvi-marketplace-palette-item ${id === selected.id ? 'is-active' : ''}" data-marketplace-palette-id="${attr(id)}"><i style="background:${attr(swatch)}"></i><span>${esc(palette.name || id)}</span>${isDefault ? '<b>DEFAULT</b>' : ''}</button>`;
        }).join('');
        marketplacePaletteName.value = selected.palette.name || selected.id;
        marketplacePaletteId.textContent = selected.id;
        marketplacePaletteDefault.textContent = selected.id === draft.default_palette ? 'Default palette' : 'Set as default';
        marketplacePaletteDefault.disabled = readonly || selected.id === draft.default_palette;
        marketplacePaletteColors.innerHTML = Object.entries(selected.palette.colors || {}).map(([name, value]) => {
            const isHex = /^#[0-9a-f]{6}$/i.test(String(value));
            return `<div class="tvi-marketplace-palette-color"><input type="text" value="${attr(name)}" data-marketplace-color-key="${attr(name)}" aria-label="Color token name"><input type="text" value="${attr(value)}" data-marketplace-color-value="${attr(name)}" aria-label="${attr(name)} value"><input type="color" value="${attr(isHex ? value : '#000000')}" data-marketplace-color-picker="${attr(name)}" aria-label="Choose ${attr(name)} color"><button type="button" data-marketplace-remove-color="${attr(name)}" title="Remove color token">×</button></div>`;
        }).join('');
        marketplacePaletteManager?.querySelectorAll('input,[data-marketplace-palette-action],[data-marketplace-remove-color]').forEach(control => {
            if (control === marketplacePaletteDefault) return;
            control.disabled = readonly;
        });
        marketplaceDefaultPalette.innerHTML = Object.entries(selected.palettes).map(([id, palette]) => `<option value="${attr(id)}" ${id === draft.default_palette ? 'selected' : ''}>${esc(palette.name || id)}</option>`).join('');
        marketplacePalettes.value = JSON.stringify(selected.palettes, null, 2);
    }

    function marketplacePaletteUniqueId(base = 'palette') {
        const selected = marketplaceSelectedPalette();
        const palettes = selected?.palettes || {};
        const root = marketplaceSlug(base) || 'palette';
        let id = root, index = 2;
        while (palettes[id]) id = `${root}-${index++}`;
        return id;
    }

    function marketplacePaletteAction(action) {
        const studio = state.marketplaceStudio;
        const draft = studio?.draft;
        const selected = marketplaceSelectedPalette();
        if (!studio || !draft?.editable || !selected) return;
        if (action === 'add') {
            const id = marketplacePaletteUniqueId('new-palette');
            selected.palettes[id] = { name: 'New Palette', colors: clone(selected.palette.colors || {}) };
            studio.paletteId = id;
        } else if (action === 'duplicate') {
            const id = marketplacePaletteUniqueId(`${selected.id}-copy`);
            selected.palettes[id] = { name: `${selected.palette.name || selected.id} Copy`, colors: clone(selected.palette.colors || {}) };
            studio.paletteId = id;
        } else if (action === 'delete') {
            if (Object.keys(selected.palettes).length <= 1) {
                toast('A theme must contain at least one palette.', 'warn');
                return;
            }
            delete selected.palettes[selected.id];
            if (draft.default_palette === selected.id) draft.default_palette = Object.keys(selected.palettes)[0];
            studio.paletteId = draft.default_palette;
        } else if (action === 'default') {
            draft.default_palette = selected.id;
        } else if (action === 'add-color') {
            let index = 1, name = 'custom_color';
            while (Object.prototype.hasOwnProperty.call(selected.palette.colors, name)) name = `custom_color_${++index}`;
            selected.palette.colors[name] = '#64748b';
        }
        draft.palettes = selected.palettes;
        renderMarketplacePaletteManager();
        scheduleMarketplacePreview();
        renderMarketplaceValidation();
    }

    function marketplacePaletteInput(event) {
        const studio = state.marketplaceStudio;
        const draft = studio?.draft;
        const selected = marketplaceSelectedPalette();
        if (!draft?.editable || !selected) return false;
        if (event.target === marketplacePaletteName) {
            selected.palette.name = marketplacePaletteName.value.trim() || selected.id;
            const activeLabel = marketplacePaletteList?.querySelector(`[data-marketplace-palette-id="${cssEscape(selected.id)}"] span`);
            if (activeLabel) activeLabel.textContent = selected.palette.name;
            marketplaceDefaultPalette.querySelector(`option[value="${cssEscape(selected.id)}"]`)?.replaceChildren(document.createTextNode(selected.palette.name));
            return true;
        }
        const keyInput = event.target.closest('[data-marketplace-color-key]');
        if (keyInput) {
            if (event.type !== 'change') return true;
            const oldKey = keyInput.dataset.marketplaceColorKey;
            const newKey = String(keyInput.value || '').trim().toLowerCase().replace(/[^a-z0-9_-]+/g, '_');
            if (newKey && newKey !== oldKey) {
                if (Object.prototype.hasOwnProperty.call(selected.palette.colors, newKey)) {
                    toast('That color token already exists.', 'warn');
                    keyInput.value = oldKey;
                } else {
                    const entries = Object.entries(selected.palette.colors).map(([key, value]) => [key === oldKey ? newKey : key, value]);
                    selected.palette.colors = Object.fromEntries(entries);
                    renderMarketplacePaletteManager();
                }
            }
            draft.palettes = selected.palettes;
            scheduleMarketplacePreview();
            renderMarketplaceValidation();
            return true;
        }
        const valueInput = event.target.closest('[data-marketplace-color-value]');
        if (valueInput) {
            const key = valueInput.dataset.marketplaceColorValue;
            selected.palette.colors[key] = valueInput.value.trim();
            const picker = marketplacePaletteColors.querySelector(`[data-marketplace-color-picker="${cssEscape(key)}"]`);
            if (picker && /^#[0-9a-f]{6}$/i.test(valueInput.value.trim())) picker.value = valueInput.value.trim();
            draft.palettes = selected.palettes;
            marketplacePalettes.value = JSON.stringify(selected.palettes, null, 2);
            updateMarketplacePreviewColors();
            renderMarketplaceValidation();
            return true;
        }
        const picker = event.target.closest('[data-marketplace-color-picker]');
        if (picker) {
            const key = picker.dataset.marketplaceColorPicker;
            selected.palette.colors[key] = picker.value;
            const text = marketplacePaletteColors.querySelector(`[data-marketplace-color-value="${cssEscape(key)}"]`);
            if (text) text.value = picker.value;
            draft.palettes = selected.palettes;
            marketplacePalettes.value = JSON.stringify(selected.palettes, null, 2);
            updateMarketplacePreviewColors();
            renderMarketplaceValidation();
            return true;
        }
        return false;
    }

    function updateMarketplacePreviewColors() {
        const studio = state.marketplaceStudio;
        const selected = marketplaceSelectedPalette();
        if (!studio || studio.tab !== 'theme' || !selected || !marketplacePreview) return;
        const root = marketplacePreview.contentDocument?.querySelector('.tvi-theme-preview');
        if (!root) {
            scheduleMarketplacePreview();
            return;
        }
        for (const [name, value] of Object.entries(formThemeColorMap(selected.palette.colors || {}))) {
            root.style.setProperty(`--thv-theme-${String(name).replace(/_/g, '-')}`, String(value));
        }
    }

    function newMarketplaceAsset() {
        const studio = state.marketplaceStudio;
        if (!studio) return;
        studio.selectedId = null;
        studio.componentKey = 'field';
        studio.draft = marketplaceDraftFromAsset(null, false);
        studio.paletteId = studio.draft.default_palette || 'default';
        renderMarketplaceStudio();
    }

    function copyMarketplaceAsset() {
        const studio = state.marketplaceStudio;
        const asset = marketplaceAssetById(studio?.selectedId);
        if (!studio || !asset) return;
        studio.selectedId = null;
        studio.componentKey = 'field';
        studio.draft = marketplaceDraftFromAsset(asset, true);
        studio.paletteId = studio.draft.default_palette || 'default';
        renderMarketplaceStudio();
        marketplaceId?.focus();
    }

    function loadMarketplaceAsset(id) {
        const studio = state.marketplaceStudio;
        const asset = marketplaceAssetById(id);
        if (!studio || !asset) return;
        studio.selectedId = asset.id;
        studio.componentKey = 'field';
        studio.draft = marketplaceDraftFromAsset(asset, false);
        studio.draft.custom = !!asset.custom;
        studio.draft.editable = !!asset.editable;
        studio.draft.source = asset.source || (asset.custom ? 'custom' : 'built-in');
        studio.draft.isNew = false;
        studio.paletteId = studio.draft.default_palette || Object.keys(studio.draft.palettes || {})[0] || 'default';
        renderMarketplaceStudio();
    }

    function renderMarketplaceAssetList() {
        const studio = state.marketplaceStudio;
        if (!marketplaceList || !studio) return;
        document.querySelectorAll('[data-marketplace-tab]').forEach(button => button.classList.toggle('is-active', button.dataset.marketplaceTab === studio.tab));
        if (studio.loading) {
            marketplaceList.innerHTML = '<p class="tvi-muted">Loading marketplace assets…</p>';
            return;
        }
        const query = String(marketplaceSearch?.value || '').trim().toLowerCase();
        const assets = marketplaceAssets().filter(asset => !query || `${asset.name} ${asset.id} ${asset.description} ${(asset.tags || []).join(' ')}`.toLowerCase().includes(query));
        marketplaceList.innerHTML = assets.map(asset => `<button type="button" class="tvi-marketplace-list-item ${String(asset.id) === String(studio.selectedId) ? 'is-active' : ''}" data-marketplace-asset-id="${attr(asset.id)}"><span><strong>${esc(asset.name || asset.id)}</strong><small>${esc(asset.description || '')}</small></span><span class="tvi-marketplace-asset-badges"><b class="${asset.custom ? 'is-custom' : 'is-builtin'}">${asset.custom ? 'Custom' : 'Built-in'}</b><code>${esc(asset.version || '1.0.0')}</code></span></button>`).join('') || '<p class="tvi-muted">No assets match this search.</p>';
    }

    function renderMarketplaceStudio() {
        const studio = state.marketplaceStudio;
        const draft = studio?.draft;
        if (!studio || !draft) return;
        renderMarketplaceAssetList();
        const readonly = !draft.editable;
        if (marketplaceLayout) {
            const focusedColumn = String(studio.focusedColumn || '');
            marketplaceLayout.dataset.focusedColumn = focusedColumn;
            ['sidebar','editor','preview'].forEach(column => {
                const columnElement = marketplaceLayout.querySelector(`[data-marketplace-column="${column}"]`);
                const focused = focusedColumn === column;
                const rail = focusedColumn !== '' && !focused;
                marketplaceLayout.classList.toggle(`is-${column}-focused`, focused);
                if (columnElement) {
                    columnElement.classList.toggle('is-focused', focused);
                    columnElement.classList.toggle('is-rail', rail);
                    columnElement.setAttribute('aria-expanded', rail ? 'false' : 'true');
                }
            });
            marketplaceLayout.querySelectorAll('[data-marketplace-focus]').forEach(button => {
                const column = button.dataset.marketplaceFocus;
                const focused = focusedColumn === column;
                button.textContent = focused ? '↔' : '⤢';
                button.setAttribute('aria-pressed', focused ? 'true' : 'false');
                button.setAttribute('aria-label', focused ? 'Restore all columns' : `Expand ${column} column and collapse the others`);
                button.title = focused ? 'Restore all columns' : `Expand ${column} column`;
            });
        }
        marketplaceReadonlyNote.hidden = !readonly;
        marketplaceThemeEditor.hidden = studio.tab !== 'theme';
        marketplaceSnippetEditor.hidden = studio.tab !== 'snippet';
        marketplaceId.value = draft.id || '';
        marketplaceName.value = draft.name || '';
        marketplaceVersion.value = draft.version || '1.0.0';
        marketplaceAuthor.value = draft.author || '';
        marketplaceWebsite.value = draft.website || '';
        marketplaceLicense.value = draft.license || 'Commercial';
        marketplaceCategory.value = draft.category || (studio.tab === 'theme' ? 'Forms' : 'General');
        marketplaceTags.value = Array.isArray(draft.tags) ? draft.tags.join(', ') : String(draft.tags || '');
        marketplaceOrder.value = Number(draft.order) || 100;
        marketplaceDescription.value = draft.description || '';
        if (studio.tab === 'theme') {
            marketplaceEnsurePalettes(draft);
            marketplacePreviewHtml.value = draft.preview_html || '';
            marketplaceThemeCss.value = draft.css || '';
            marketplaceThemeJs.value = draft.js || '';
            marketplaceComponentSelect.value = studio.componentKey;
            marketplaceComponentCode.value = draft.components?.[studio.componentKey] || '';
            renderMarketplaceComponentTokens();
            renderMarketplacePaletteManager();
        } else {
            marketplaceSnippetHtml.value = draft.html || '';
            marketplaceSnippetCss.value = draft.css || '';
            marketplaceSnippetJs.value = draft.js || '';
        }
        marketplaceModal.querySelectorAll('.tvi-marketplace-editor input,.tvi-marketplace-editor textarea').forEach(control => {
            const type = String(control.type || '').toLowerCase();
            const mustDisable = ['color','checkbox','radio','file','range'].includes(type);
            control.disabled = readonly && mustDisable;
            control.readOnly = readonly && !mustDisable;
            control.setAttribute('aria-readonly', readonly ? 'true' : 'false');
            control.style.pointerEvents = 'auto';
        });
        marketplaceModal.querySelectorAll('.tvi-marketplace-editor select').forEach(control => { control.disabled = readonly; });
        if (marketplaceComponentSelect) marketplaceComponentSelect.disabled = false;
        marketplaceCopyButton.hidden = !readonly;
        marketplaceSaveButton.hidden = readonly;
        marketplaceDeleteButton.hidden = readonly || draft.isNew;
        marketplaceExportButton.hidden = readonly || draft.isNew;
        marketplaceSourceBadge.textContent = readonly ? 'Built-in · read-only' : draft.isNew ? 'Unsaved custom' : 'Custom package';
        scheduleMarketplacePreview();
        renderMarketplaceValidation();
    }

    function marketplaceStudioClick(event) {
        const studio = state.marketplaceStudio;
        if (!studio) return;
        const focusColumn = event.target.closest('[data-marketplace-focus]');
        if (focusColumn) {
            const column = focusColumn.dataset.marketplaceFocus;
            studio.focusedColumn = studio.focusedColumn === column ? '' : column;
            renderMarketplaceStudio();
            return;
        }
        const expand = event.target.closest('[data-marketplace-expand]');
        if (expand) {
            openMarketplaceCodeEditor(expand.dataset.marketplaceExpand, expand.dataset.marketplaceMode || 'text/plain', expand.dataset.marketplaceTitle || 'Asset source');
            return;
        }
        const palette = event.target.closest('[data-marketplace-palette-id]');
        if (palette) {
            studio.paletteId = palette.dataset.marketplacePaletteId;
            renderMarketplacePaletteManager();
            scheduleMarketplacePreview();
            return;
        }
        const paletteAction = event.target.closest('[data-marketplace-palette-action]');
        if (paletteAction) {
            marketplacePaletteAction(paletteAction.dataset.marketplacePaletteAction);
            return;
        }
        const removeColor = event.target.closest('[data-marketplace-remove-color]');
        if (removeColor) {
            const selected = marketplaceSelectedPalette();
            if (selected && studio.draft?.editable) {
                delete selected.palette.colors[removeColor.dataset.marketplaceRemoveColor];
                studio.draft.palettes = selected.palettes;
                renderMarketplacePaletteManager();
                scheduleMarketplacePreview();
                renderMarketplaceValidation();
            }
            return;
        }
        const tab = event.target.closest('[data-marketplace-tab]');
        if (tab) {
            event.preventDefault();
            studio.tab = tab.dataset.marketplaceTab === 'snippet' ? 'snippet' : 'theme';
            studio.selectedId = null;
            studio.componentKey = 'field';
            const assets = marketplaceAssets();
            const selected = assets.find(item => item.custom) || assets[0];
            if (selected) loadMarketplaceAsset(selected.id); else newMarketplaceAsset();
            return;
        }
        const item = event.target.closest('[data-marketplace-asset-id]');
        if (item) loadMarketplaceAsset(item.dataset.marketplaceAssetId);
    }

    function marketplaceStudioInput(event) {
        const studio = state.marketplaceStudio;
        const draft = studio?.draft;
        if (!studio || !draft || !draft.editable) return;
        if (event.target.closest?.('#tvi-marketplace-palette-manager') && marketplacePaletteInput(event)) return;
        if (event.target === marketplaceComponentSelect) {
            if (draft.components) draft.components[studio.componentKey] = marketplaceComponentCode.value;
            studio.componentKey = marketplaceComponentSelect.value || 'field';
            marketplaceComponentCode.value = draft.components?.[studio.componentKey] || '';
            renderMarketplaceComponentTokens();
            renderMarketplaceValidation();
            return;
        }
        readMarketplaceDraft();
        scheduleMarketplacePreview();
        renderMarketplaceValidation();
    }

    function readMarketplaceDraft() {
        const studio = state.marketplaceStudio;
        const draft = studio?.draft;
        if (!draft) return;
        draft.id = marketplaceSlug(marketplaceId.value);
        draft.name = marketplaceName.value.trim();
        draft.version = marketplaceVersion.value.trim();
        draft.author = marketplaceAuthor.value.trim();
        draft.website = marketplaceWebsite.value.trim();
        draft.license = marketplaceLicense.value.trim();
        draft.category = marketplaceCategory.value.trim();
        draft.tags = marketplaceTags.value.split(',').map(value => value.trim()).filter(Boolean);
        draft.order = Number(marketplaceOrder.value) || 100;
        draft.description = marketplaceDescription.value.trim();
        if (studio.tab === 'theme') {
            marketplaceEnsurePalettes(draft);
            draft.preview_html = marketplacePreviewHtml.value;
            draft.css = marketplaceThemeCss.value;
            draft.js = marketplaceThemeJs.value;
            draft.components ||= {};
            draft.components[studio.componentKey] = marketplaceComponentCode.value;
        } else {
            draft.html = marketplaceSnippetHtml.value;
            draft.css = marketplaceSnippetCss.value;
            draft.js = marketplaceSnippetJs.value;
        }
    }

    function marketplaceParsedPalettes(draft) {
        if (!draft || typeof draft !== 'object') return null;
        const palettes = marketplacePalettesObject(draft.palettes || {});
        return Object.keys(palettes).length ? palettes : null;
    }

    function marketplaceValidationResults() {
        const studio = state.marketplaceStudio;
        const draft = studio?.draft;
        const results = [];
        if (!draft) return results;
        const error = message => results.push({ level: 'error', message });
        const warning = message => results.push({ level: 'warning', message });
        const pass = message => results.push({ level: 'pass', message });
        if (!marketplaceSlug(draft.id)) error('A package ID is required.'); else pass('Package ID is a valid slug.');
        if (!String(draft.name || '').trim()) error('A display name is required.');
        if (!String(draft.version || '').trim()) warning('Add a package version before publishing.');
        if (!String(draft.author || '').trim()) warning('Add an author or company for marketplace attribution.');
        if (studio.tab === 'theme') {
            if (!String(draft.preview_html || '').trim()) error('Preview form HTML is required.');
            else if (!String(draft.preview_html).includes('tvi-theme-preview')) warning('The server will wrap the preview in .tvi-theme-preview.');
            if (!String(draft.css || '').trim()) error('Theme CSS is required.');
            else if (!String(draft.css).includes('{{scope}}')) error('Theme CSS must use {{scope}} for isolation.');
            else pass('Theme CSS uses the required scope token.');
            const palettes = marketplaceParsedPalettes(draft);
            if (!palettes || !Object.keys(palettes).length) error('The theme must contain at least one named palette.');
            else pass(`${Object.keys(palettes).length} palette${Object.keys(palettes).length === 1 ? '' : 's'} ready.`);
            const requirements = marketplaceComponentRequirements();
            Object.entries(requirements).forEach(([component, tokens]) => {
                const code = String(draft.components?.[component] || '');
                tokens.forEach(token => { if (!code.includes(token)) error(`${component} template is missing ${token}.`); });
            });
            if (!results.some(item => item.level === 'error' && item.message.includes('template'))) pass('All component templates contain their required tokens.');
            if (/\bdocument\.(querySelector|querySelectorAll|getElementById)\s*\(/.test(String(draft.js || ''))) warning('Prefer scoped theme JavaScript instead of document-wide selectors.');
        } else {
            const html = String(draft.html || '');
            if (!html.trim()) error('Snippet HTML is required.');
            if (/<\/?(?:html|head|body)\b/i.test(html)) error('Use an HTML fragment, not a complete document.');
            if (/<(?:script|style)\b/i.test(html)) error('Move style and script tags into the CSS and JavaScript editors.');
            if (!/<\/?(?:html|head|body)\b/i.test(html) && !/<(?:script|style)\b/i.test(html) && html.trim()) pass('HTML is a reusable fragment.');
            if (/\bdocument\.(querySelector|querySelectorAll|getElementById)\s*\(/.test(String(draft.js || ''))) warning('Use root.querySelector(...) so each snippet instance remains isolated.');
            else pass('JavaScript is ready for the component root wrapper.');
        }
        return results;
    }

    function renderMarketplaceValidation() {
        const results = marketplaceValidationResults();
        if (!marketplaceValidation) return;
        marketplaceValidation.innerHTML = results.map(item => `<div class="tvi-marketplace-check is-${item.level}"><span>${item.level === 'error' ? '×' : item.level === 'warning' ? '!' : '✓'}</span><p>${esc(item.message)}</p></div>`).join('');
        const hasErrors = results.some(item => item.level === 'error');
        if (marketplaceSaveButton && state.marketplaceStudio?.draft?.editable) marketplaceSaveButton.disabled = hasErrors;
    }

    function marketplaceComponentRequirements() {
        return {
            field:['{{wrapper_attributes}}','{{label_html}}','{{control_html}}','{{error_html}}'],
            textarea:['{{wrapper_attributes}}','{{label_html}}','{{control_html}}','{{error_html}}'],
            select:['{{wrapper_attributes}}','{{label_html}}','{{control_html}}','{{error_html}}'],
            checkbox:['{{wrapper_attributes}}','{{label_html}}','{{control_html}}','{{error_html}}'],
            button:['{{wrapper_attributes}}','{{control_html}}'],
            form:['{{wrapper_attributes}}','{{children_html}}']
        };
    }

    function renderMarketplaceComponentTokens() {
        const key = state.marketplaceStudio?.componentKey || 'field';
        const tokens = marketplaceComponentRequirements()[key] || [];
        marketplaceComponentTokens.innerHTML = tokens.map(token => `<code>${esc(token)}</code>`).join('');
    }

    function scheduleMarketplacePreview() {
        clearTimeout(state.marketplacePreviewTimer);
        state.marketplacePreviewTimer = setTimeout(renderMarketplacePreview, 120);
    }

    function renderMarketplacePreview() {
        const studio = state.marketplaceStudio;
        const draft = studio?.draft;
        if (!draft || !marketplacePreview) return;
        if (studio.tab === 'theme') {
            const selected = marketplaceSelectedPalette();
            marketplacePreview.srcdoc = formThemePreviewDocument({ preview_html:draft.preview_html, css:draft.css, js:draft.js }, formThemeColorMap(selected?.palette?.colors || {}));
        } else {
            const colors = clone(state.marketplaceStudio?.starter?.theme_colors || {});
            marketplacePreview.srcdoc = htmlSnippetPreviewDocument({ html:draft.html, css:draft.css, js:draft.js }, colors);
        }
    }

    function resetMarketplacePalette() {
        const studio = state.marketplaceStudio;
        const draft = studio?.draft;
        if (!draft || studio.tab !== 'theme' || !draft.editable) return;
        draft.palettes = { default: { name:'Default', colors:clone(studio.starter?.theme_colors || marketplaceStarterTheme().palettes.default.colors) } };
        draft.default_palette = 'default';
        studio.paletteId = 'default';
        renderMarketplaceStudio();
    }

    async function saveMarketplaceAsset() {
        const studio = state.marketplaceStudio;
        const draft = studio?.draft;
        if (!draft || !draft.editable) return;
        readMarketplaceDraft();
        const validation = marketplaceValidationResults();
        if (validation.some(item => item.level === 'error')) {
            renderMarketplaceValidation();
            toast('Resolve the package errors before saving.', 'warn');
            return;
        }
        const payload = clone(draft);
        delete payload.editable; delete payload.custom; delete payload.source; delete payload.isNew;
        if (studio.tab === 'theme') {
            payload.palettes = marketplaceParsedPalettes(draft);
            delete payload.palettes_text;
        }
        const form = new FormData();
        form.append('asset_json', JSON.stringify(payload));
        marketplaceSaveButton.disabled = true;
        try {
            const response = await fetch(`${cfg.apiBase}/marketplace-${studio.tab}-save`, { method:'POST', body:form });
            const data = await response.json();
            if (!response.ok || !data.ok) { showIssues(data); return; }
            applyMarketplaceStudioData(data);
            const id = data.result?.id || payload.id;
            const warnings = data.result?.warnings || [];
            loadMarketplaceAsset(id);
            toast(`${studio.tab === 'theme' ? 'Theme' : 'Snippet'} saved as a custom marketplace package.`, 'success');
            warnings.forEach(message => toast(message, 'warn'));
        } catch (error) {
            toast(error.message, 'fail');
        } finally {
            if (marketplaceSaveButton) marketplaceSaveButton.disabled = false;
        }
    }

    async function deleteMarketplaceAsset() {
        const studio = state.marketplaceStudio;
        const draft = studio?.draft;
        if (!draft?.custom || draft.isNew) return;
        if (!confirm(`Delete the custom ${studio.tab} “${draft.name || draft.id}”? This does not affect built-in assets.`)) return;
        const form = new FormData(); form.append('asset_id', draft.id);
        try {
            const response = await fetch(`${cfg.apiBase}/marketplace-${studio.tab}-delete`, { method:'POST', body:form });
            const data = await response.json();
            if (!response.ok || !data.ok) { showIssues(data); return; }
            applyMarketplaceStudioData(data);
            studio.selectedId = null;
            const assets = marketplaceAssets();
            if (assets[0]) loadMarketplaceAsset(assets[0].id); else newMarketplaceAsset();
            toast('Custom marketplace asset deleted.', 'success');
        } catch (error) { toast(error.message, 'fail'); }
    }

    async function exportMarketplaceAsset() {
        const studio = state.marketplaceStudio;
        const draft = studio?.draft;
        if (!draft?.custom || draft.isNew) return;
        const form = new FormData(); form.append('asset_id', draft.id);
        try {
            const response = await fetch(`${cfg.apiBase}/marketplace-${studio.tab}-export`, { method:'POST', body:form });
            if (!response.ok) {
                const data = await response.json(); showIssues(data); return;
            }
            const suffix = studio.tab === 'theme' ? 'form-theme' : 'html-snippet';
            downloadBlob(await response.blob(), `${safeDownloadName(draft.id)}.${suffix}.zip`, 'application/zip');
            toast('Marketplace package exported.', 'success');
        } catch (error) { toast(error.message, 'fail'); }
    }

    async function importMarketplacePackage(event) {
        const file = event.target.files?.[0];
        event.target.value = '';
        const studio = state.marketplaceStudio;
        if (!file || !studio) return;
        const form = new FormData();
        form.append('marketplace_archive', file);
        form.append('asset_type', studio.tab);
        try {
            const response = await fetch(`${cfg.apiBase}/marketplace-package-import`, { method:'POST', body:form });
            const data = await response.json();
            if (!response.ok || !data.ok) { showIssues(data); return; }
            applyMarketplaceStudioData(data);
            studio.tab = data.result?.type === 'snippet' ? 'snippet' : 'theme';
            loadMarketplaceAsset(data.result?.id);
            toast('Marketplace package imported as a custom asset.', 'success');
            (data.result?.warnings || []).forEach(message => toast(message, 'warn'));
        } catch (error) { toast(error.message, 'fail'); }
    }

    function formThemeById(id) {
        return formThemes.find(theme => String(theme.id) === String(id))
            || formThemes.find(theme => String(theme.id) === 'classic')
            || formThemes[0]
            || null;
    }

    function formThemePaletteById(theme, id) {
        const palettes = Array.isArray(theme?.palettes) ? theme.palettes : [];
        return palettes.find(palette => String(palette.id) === String(id))
            || palettes.find(palette => String(palette.id) === String(theme?.default_palette || 'default'))
            || palettes[0]
            || { id: 'default', name: 'Default', colors: {} };
    }

    function formThemeColorMap(value) {
        const result = {};
        if (!value || typeof value !== 'object' || Array.isArray(value)) return result;
        for (const [name, color] of Object.entries(value)) {
            const cleanName = String(name).trim().toLowerCase().replace(/[^a-z0-9_-]+/g, '_');
            const cleanColor = String(color || '').trim();
            if (cleanName && cleanColor) result[cleanName] = cleanColor;
        }
        return result;
    }

    function paletteMapsEqual(left, right) {
        const a = formThemeColorMap(left);
        const b = formThemeColorMap(right);
        const keys = new Set([...Object.keys(a), ...Object.keys(b)]);
        return [...keys].every(key => String(a[key] || '').toLowerCase() === String(b[key] || '').toLowerCase());
    }

    function storedPaletteSelection(node, theme, palette, colors) {
        const requested = String(node?.data?.form_theme_palette_selection || '');
        const requestedOption = htmlSnippetPaletteOption(theme, requested, palette?.id || 'default');
        if (requested && requestedOption?.value === requested) return requested;
        const matchingPreset = htmlSnippetPalettePresets.find(preset => paletteMapsEqual(preset.colors, colors));
        return matchingPreset ? `preset:${matchingPreset.id}` : `theme:${palette?.id || 'default'}`;
    }

    function lookFormThemeTools(node) {
        const theme = formThemeById(node?.data?.form_theme_id || 'classic');
        if (!theme) return '';
        const palette = formThemePaletteById(theme, node?.data?.form_theme_palette_id || theme.default_palette || 'default');
        const colors = { ...formThemeColorMap(palette.colors), ...formThemeColorMap(node?.data?.form_theme_colors) };
        const selection = storedPaletteSelection(node, theme, palette, colors);
        const option = htmlSnippetPaletteOption(theme, selection, palette.id);
        return `<div class="tvi-view-tools tvi-form-theme-tool"><h4>Shared form input theme</h4><p><strong>${esc(theme.name)}</strong> · ${esc(option?.name || palette.name || 'Default')}</p><button type="button" data-inspector-action="open-form-theme">Choose Form Theme…</button><small>This Look supplies one theme and color palette to every connected View and reusable partial.</small></div>`;
    }

    function openFormTheme(nodeId) {
        const node = nodeById(nodeId, 'main') || nodeById(nodeId);
        if (!node || node.type !== 'looks.look') {
            toast('Select a Look node before choosing a form theme.', 'warn');
            return;
        }
        const theme = formThemeById(node.data.form_theme_id || 'classic');
        if (!theme) {
            toast('No form theme packages are installed.', 'fail');
            return;
        }
        const palette = formThemePaletteById(theme, node.data.form_theme_palette_id || theme.default_palette || 'default');
        const colors = {
            ...formThemeColorMap(palette.colors),
            ...formThemeColorMap(node.data.form_theme_colors)
        };
        state.formThemeEditor = {
            nodeId: node.id,
            themeId: theme.id,
            paletteId: palette.id,
            paletteSelection: storedPaletteSelection(node, theme, palette, colors),
            colors
        };
        renderFormThemeEditor();
        formThemeModal.hidden = false;
        document.body.classList.add('tvi-modal-open');
    }

    function closeFormTheme() {
        if (formThemeModal) formThemeModal.hidden = true;
        state.formThemeEditor = null;
        if (![...document.querySelectorAll('.tvi-modal')].some(modal => !modal.hidden)) {
            document.body.classList.remove('tvi-modal-open');
        }
    }

    function renderFormThemeEditor() {
        const editor = state.formThemeEditor;
        if (!editor) return;
        const theme = formThemeById(editor.themeId);
        if (!theme) return;
        const palette = formThemePaletteById(theme, editor.paletteId);
        const option = htmlSnippetPaletteOption(theme, editor.paletteSelection, palette.id);
        if (option) {
            editor.paletteSelection = option.value;
            if (option.source === 'theme') editor.paletteId = option.id;
        }
        formThemeList.innerHTML = formThemes.map(item => `<button type="button" class="tvi-form-theme-card ${String(item.id) === String(theme.id) ? 'is-active' : ''}" data-form-theme-id="${attr(item.id)}"><strong>${esc(item.name)}</strong><small>${esc(item.description || '')}</small><code>form-themes/${esc(item.package_path || item.id)}</code></button>`).join('') || '<p class="tvi-muted">No form themes are installed.</p>';
        const paletteOptions = htmlSnippetPaletteOptions(theme);
        const themeOptions = paletteOptions.filter(item => item.source === 'theme').map(item => `<option value="${attr(item.value)}" ${item.value === editor.paletteSelection ? 'selected' : ''}>${esc(item.name)}</option>`).join('');
        const presetOptions = paletteOptions.filter(item => item.source === 'preset').map(item => `<option value="${attr(item.value)}" ${item.value === editor.paletteSelection ? 'selected' : ''}>${esc(item.name)}</option>`).join('');
        formThemePalette.innerHTML = `<optgroup label="Form theme palettes">${themeOptions}</optgroup><optgroup label="Reusable color presets">${presetOptions}</optgroup>`;
        formThemeColors.innerHTML = Object.entries(editor.colors).map(([name, value]) => {
            const isHex = /^#[0-9a-f]{6}$/i.test(String(value));
            return `<div class="tvi-form-theme-color"><label><span>${esc(name.replace(/[_-]+/g, ' '))}</span><input type="text" value="${attr(value)}" data-form-theme-color="${attr(name)}"></label><input type="color" value="${attr(isHex ? value : '#000000')}" data-form-theme-color-picker="${attr(name)}" title="Choose ${attr(name)} color" aria-label="Choose ${attr(name)} color"></div>`;
        }).join('');
        formThemePreviewName.textContent = theme.name || theme.id;
        formThemePreviewDescription.textContent = theme.description || '';
        renderFormThemePreview();
    }

    function formThemeListClick(event) {
        const button = event.target.closest('[data-form-theme-id]');
        if (!button || !state.formThemeEditor) return;
        const theme = formThemeById(button.dataset.formThemeId);
        if (!theme) return;
        const palette = formThemePaletteById(theme, theme.default_palette || 'default');
        const currentTheme = formThemeById(state.formThemeEditor.themeId);
        const currentOption = htmlSnippetPaletteOption(currentTheme, state.formThemeEditor.paletteSelection, state.formThemeEditor.paletteId);
        state.formThemeEditor.themeId = theme.id;
        state.formThemeEditor.paletteId = palette.id;
        if (currentOption?.source === 'preset') {
            state.formThemeEditor.paletteSelection = currentOption.value;
        } else {
            state.formThemeEditor.paletteSelection = `theme:${palette.id}`;
            state.formThemeEditor.colors = formThemeColorMap(palette.colors);
        }
        renderFormThemeEditor();
    }

    function formThemePaletteChange() {
        const editor = state.formThemeEditor;
        if (!editor) return;
        const theme = formThemeById(editor.themeId);
        const option = htmlSnippetPaletteOption(theme, formThemePalette.value, editor.paletteId);
        if (!option) return;
        editor.paletteSelection = option.value;
        if (option.source === 'theme') editor.paletteId = option.id;
        editor.colors = formThemeColorMap(option.colors);
        renderFormThemeEditor();
    }

    function formThemeColorInput(event) {
        const editor = state.formThemeEditor;
        if (!editor) return;
        const textName = event.target.dataset.formThemeColor;
        const pickerName = event.target.dataset.formThemeColorPicker;
        const name = textName || pickerName;
        if (!name) return;
        editor.colors[name] = event.target.value;
        if (pickerName) {
            const text = formThemeColors.querySelector(`[data-form-theme-color="${cssEscape(name)}"]`);
            if (text) text.value = event.target.value;
        } else if (/^#[0-9a-f]{6}$/i.test(event.target.value)) {
            const picker = formThemeColors.querySelector(`[data-form-theme-color-picker="${cssEscape(name)}"]`);
            if (picker) picker.value = event.target.value;
        }
        updateFormThemePreviewColors();
    }

    function updateFormThemePreviewColors() {
        const editor = state.formThemeEditor;
        if (!editor || !formThemePreview) return;
        const documentRef = formThemePreview.contentDocument;
        const root = documentRef?.querySelector('.tvi-theme-preview');
        if (!root) {
            renderFormThemePreview();
            return;
        }
        for (const [name, value] of Object.entries(formThemeColorMap(editor.colors))) {
            root.style.setProperty(`--thv-theme-${String(name).replace(/_/g, '-')}`, String(value));
        }
    }

    function resetFormThemeColors() {
        const editor = state.formThemeEditor;
        if (!editor) return;
        const theme = formThemeById(editor.themeId);
        const option = htmlSnippetPaletteOption(theme, editor.paletteSelection, editor.paletteId);
        if (!option) return;
        editor.colors = formThemeColorMap(option.colors);
        renderFormThemeEditor();
    }

    function previewIframeLinkGuardScript() {
        return `document.addEventListener('click',function(event){const link=event.target&&event.target.closest?event.target.closest('a[href]'):null;if(!link)return;event.preventDefault();event.stopImmediatePropagation();},true);document.addEventListener('auxclick',function(event){const link=event.target&&event.target.closest?event.target.closest('a[href]'):null;if(!link)return;event.preventDefault();event.stopImmediatePropagation();},true);`;
    }

    function formThemePreviewDocument(theme, colors) {
        const scope = '.tvi-theme-preview';
        const variables = Object.entries(colors).map(([name, value]) => `--thv-theme-${String(name).replace(/_/g, '-')}:${String(value)};`).join('');
        const base = `*{box-sizing:border-box}body{margin:0;padding:28px;background:#eef2f7;color:#0f172a}.tvi-theme-preview{${variables};max-width:820px;margin:auto}.thv-grid{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:var(--thv-gap,1rem);align-items:start}.thv-form{margin:0}.thv-field{display:grid;gap:.4rem;min-width:0}.thv-field>:where(label,.thv-label){font-weight:600}.thv-field :where(input:not([type=checkbox]):not([type=radio]),textarea,select){width:100%;max-width:100%;padding:.7rem .8rem;border:1px solid #cbd5e1;border-radius:.45rem;background:inherit;color:inherit;font:inherit}.thv-field textarea{min-height:7rem;resize:vertical}.thv-checkbox{display:flex;align-items:flex-start;gap:.55rem}.thv-button{display:inline-flex;align-items:center;justify-content:center;padding:.7rem 1rem;border:1px solid transparent;border-radius:.45rem;cursor:pointer;font:inherit}.thv-col-mobile-12{grid-column:span 12}@media(min-width:768px){.thv-col-medium-6{grid-column:span 6}}`;
        const css = String(theme.css || '').replaceAll('{{scope}}', scope);
        const js = String(theme.js || '').replaceAll('{{scope}}', scope);
        return `<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><style>${base}\n${css}</style></head><body>${String(theme.preview_html || '<div class="tvi-theme-preview"><p>No preview.html was supplied.</p></div>')}<script>${previewIframeLinkGuardScript()}${js.replace(/<\/script/gi, '<\\/script')}<\/script></body></html>`;
    }

    function renderFormThemePreview() {
        const editor = state.formThemeEditor;
        if (!editor || !formThemePreview) return;
        const theme = formThemeById(editor.themeId);
        if (!theme) return;
        formThemePreview.srcdoc = formThemePreviewDocument(theme, editor.colors);
    }

    function applyFormTheme() {
        const editor = state.formThemeEditor;
        if (!editor) return;
        const node = nodeById(editor.nodeId, 'main');
        if (!node) return;
        snapshot();
        node.data.form_theme_id = editor.themeId;
        node.data.form_theme_palette_id = editor.paletteId;
        node.data.form_theme_palette_selection = editor.paletteSelection || `theme:${editor.paletteId}`;
        node.data.form_theme_colors = formThemeColorMap(editor.colors);
        changed();
        closeFormTheme();
        renderAll();
        toast('Form input theme applied to the Look.', 'success');
    }

    function exportFormThemeColors() {
        const editor = state.formThemeEditor;
        if (!editor) return;
        const theme = formThemeById(editor.themeId);
        const payload = {
            format: 'thunder-visual-ide-form-palette',
            format_version: 1,
            theme_id: editor.themeId,
            theme_name: theme?.name || editor.themeId,
            palette_id: editor.paletteId,
            palette_selection: editor.paletteSelection,
            colors: formThemeColorMap(editor.colors)
        };
        downloadBlob(JSON.stringify(payload, null, 2), `${String(editor.themeId).replace(/[^a-z0-9_-]+/gi, '-')}-palette.json`, 'application/json');
    }

    function importFormThemeColors(event) {
        const file = event.target.files?.[0];
        const importMode = state.formThemeImportMode || 'full';
        state.formThemeImportMode = 'full';
        event.target.value = '';
        if (!file || !state.formThemeEditor) return;
        const reader = new FileReader();
        reader.onload = () => {
            try {
                const payload = JSON.parse(String(reader.result || '{}'));
                if (!payload || typeof payload !== 'object' || !payload.colors || typeof payload.colors !== 'object') {
                    throw new Error('The file does not contain a form-theme color palette.');
                }
                if (importMode === 'colors-only') {
                    state.formThemeEditor.colors = {
                        ...formThemeColorMap(state.formThemeEditor.colors),
                        ...formThemeColorMap(payload.colors)
                    };
                    renderFormThemeEditor();
                    toast('Colors imported without changing the selected form design or palette.', 'success');
                    return;
                }
                const importedTheme = formThemeById(payload.theme_id);
                if (importedTheme) state.formThemeEditor.themeId = importedTheme.id;
                const theme = formThemeById(state.formThemeEditor.themeId);
                const palette = formThemePaletteById(theme, payload.palette_id || theme?.default_palette || 'default');
                const requestedSelection = payload.palette_selection || `theme:${palette.id}`;
                const option = htmlSnippetPaletteOption(theme, requestedSelection, palette.id);
                state.formThemeEditor.paletteId = option?.source === 'theme' ? option.id : palette.id;
                state.formThemeEditor.paletteSelection = option?.value || `theme:${palette.id}`;
                state.formThemeEditor.colors = {
                    ...formThemeColorMap(option?.colors || palette.colors),
                    ...formThemeColorMap(payload.colors)
                };
                renderFormThemeEditor();
                toast('Theme design and colors imported.', 'success');
            } catch (error) {
                toast(error.message || 'Unable to import theme colors.', 'fail');
            }
        };
        reader.readAsText(file);
    }

    function viewComponentTools(node) {
        const graph = state.project.graphs[node.data.graph_id];
        if (!graph || graph.kind !== 'view') return '';
        const childIds = new Set(graph.edges.map(e => e.to));
        const components = graph.nodes.filter(n => ['view.component','view.form','view.pagination','view.html_component'].includes(n.type) && !childIds.has(n.id)).map(n => String(n.data.component_name || '').trim()).filter(Boolean);
        if (!components.length) return '<div class="tvi-view-tools"><h4>Visual components</h4><p class="tvi-muted">Open the View Builder and add a Form, Component, Pagination Component, or HTML Design Component node.</p></div>';
        return `<div class="tvi-view-tools"><h4>Visual components</h4><p>Insert a marker at the end of the View source. You may move it anywhere in the fullscreen editor.</p>${[...new Set(components)].map(name => `<button type="button" data-inspector-action="insert-component-marker" data-component-name="${attr(name)}">Insert ${esc(name)}</button>`).join('')}</div>`;
    }

    function viewAssetTools(node) {
        const graph = mainGraph();
        const connectedLookIds = new Set();

        for (const edge of graph.edges) {
            const otherId = edge.from === node.id
                ? edge.to
                : edge.to === node.id
                    ? edge.from
                    : null;

            if (otherId && nodeById(otherId, 'main')?.type === 'looks.look') {
                connectedLookIds.add(otherId);
            }
        }

        const assets = graph.nodes.filter(asset => {
            if (asset.type !== 'looks.text_asset') return false;
            if (!connectedLookIds.size) return true;

            return graph.edges.some(edge => {
                if (edge.from === asset.id && connectedLookIds.has(edge.to)) return true;
                if (edge.to === asset.id && connectedLookIds.has(edge.from)) return true;
                return false;
            });
        });

        if (!assets.length) {
            return '<div class="tvi-view-tools"><h4>CSS / JavaScript assets</h4><p class="tvi-muted">Add a Code Asset node and connect it to this View\'s Look.</p></div>';
        }

        const buttons = assets.map(asset => {
            const name = String(asset.data.asset_name || asset.data.title || 'asset')
                .trim()
                .toLowerCase()
                .replace(/[^a-z0-9_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            const type = String(asset.data.asset_type || 'CSS');
            const filename = String(asset.data.filename || '');

            return `<button type="button" data-inspector-action="insert-asset-marker" data-asset-name="${attr(name)}">Insert ${esc(type)} · ${esc(filename || name)}</button>`;
        }).join('');

        return `<div class="tvi-view-tools"><h4>CSS / JavaScript assets</h4><p>Insert a linked asset marker at the end of the View source, then move it to the preferred position.</p>${buttons}</div>`;
    }

    function refSelect(node,p,type,labelKey) {
        const options = mainGraph().nodes.filter(n => n.type === type);
        const value = node.data[p.name] || '';
        return `<select data-property="${attr(p.name)}" data-inspector-node-id="${attr(node.id)}"><option value="">Select…</option>${options.map(n => `<option value="${attr(n.id)}" ${n.id === value ? 'selected' : ''}>${esc(n.data[labelKey] || nodeTitle(n))}</option>`).join('')}</select>`;
    }

    function codeAssetSelect(node, p) {
        const graph = mainGraph();
        const assets = graph.nodes
            .filter(item => item.type === 'looks.text_asset' && !nodeIsMuted(item))
            .map(asset => {
                const look = graph.edges
                    .filter(edge => edge.from === asset.id || edge.to === asset.id)
                    .map(edge => nodeById(edge.from === asset.id ? edge.to : edge.from, 'main'))
                    .find(item => item?.type === 'looks.look');
                const title = String(asset.data?.title || asset.data?.asset_name || 'Code Asset').trim();
                const type = String(asset.data?.asset_type || 'CSS').trim();
                const filename = String(asset.data?.filename || '').trim();
                const lookLabel = String(look?.data?.title || look?.data?.name || look?.data?.folder || 'main').trim();
                return {
                    id: asset.id,
                    label: [title, type, filename, `Look: ${lookLabel}`].filter(Boolean).join(' · ')
                };
            })
            .sort((a, b) => a.label.localeCompare(b.label));
        const value = String(node.data[p.name] || '');
        const emptyLabel = assets.length ? 'Select Code Asset…' : 'No Code Asset nodes available';
        return `<select data-property="${attr(p.name)}" data-inspector-node-id="${attr(node.id)}"><option value="">${esc(emptyLabel)}</option>${assets.map(asset => `<option value="${attr(asset.id)}" ${asset.id === value ? 'selected' : ''}>${esc(asset.label)}</option>`).join('')}</select>`;
    }

    function activeGraphRefSelect(node,p,type,labelKey) {
        const options = activeGraph().nodes.filter(n => n.type === type && n.id !== node.id);
        const value = node.data[p.name] || '';
        return `<select data-property="${attr(p.name)}" data-inspector-node-id="${attr(node.id)}"><option value="">Select…</option>${options.map(n => `<option value="${attr(n.id)}" ${n.id === value ? 'selected' : ''}>${esc(n.data[labelKey] || nodeTitle(n))}</option>`).join('')}</select>`;
    }

    function methodSelect(node,p) {
        const model = nodeById(node.data.model_node_id,'main'); const methods = normalizeMethods(parseJson(model?.data?.methods_json,[]));
        return `<select data-property="${attr(p.name)}" data-inspector-node-id="${attr(node.id)}"><option value="">Select…</option>${methods.map(m => `<option value="${attr(m.name || '')}" ${String(node.data[p.name]) === String(m.name) ? 'selected' : ''}>${esc(methodSignature(m))}</option>`).join('')}</select>`;
    }

    function classMethodSelect(node,p) {
        const classNode = nodeById(node.data.class_node_id,'main'); const methods = normalizeMethods(parseJson(classNode?.data?.methods_json,[]));
        return `<select data-property="${attr(p.name)}" data-inspector-node-id="${attr(node.id)}"><option value="">Select…</option>${methods.map(m => `<option value="${attr(m.name || '')}" ${String(node.data[p.name]) === String(m.name) ? 'selected' : ''}>${esc(methodSignature(m))}</option>`).join('')}</select>`;
    }

    function viewForms(viewNodeId) {
        const view = nodeById(viewNodeId, 'main');
        const graph = state.project.graphs[view?.data?.graph_id];
        if (!graph || graph.kind !== 'view') return [];
        return graph.nodes.filter(n => n.type === 'view.form').map(n => ({
            id: n.id,
            name: String(n.data.schema_name || n.data.component_name || n.data.title || 'form').trim(),
            node: n
        })).filter(item => item.name);
    }
    function formSelect(node,p) {
        const forms = viewForms(node.data.view_node_id);
        const value = String(node.data[p.name] || '');
        return `<select data-property="${attr(p.name)}" data-inspector-node-id="${attr(node.id)}"><option value="">Select…</option>${forms.map(item => `<option value="${attr(item.name)}" ${value === item.name ? 'selected' : ''}>${esc(item.name)}</option>`).join('')}</select>`;
    }

    function javascriptOwnerView(node) {
        const graph = activeGraph();
        if (graph.kind !== 'javascript') return null;
        const owner = projectNodeById(graph.owner_node_id);
        return ['lifecycle.view','lifecycle.reusable_view'].includes(owner?.type) ? owner : null;
    }

    function javascriptViewComponentSelect(node, p, requiredType = '') {
        const owner = javascriptOwnerView(node);
        const graph = state.project.graphs[owner?.data?.graph_id];
        const value = String(node.data[p.name] || '');
        const options = (graph?.nodes || []).filter(item => !requiredType || item.type === requiredType);
        const rows = options.map(item => `<option value="${attr(item.id)}" ${item.id === value ? 'selected' : ''}>${esc(nodeTitle(item))} · ${esc(defFor(item)?.label || item.type)}</option>`).join('');
        return `<select data-property="${attr(p.name)}" data-inspector-node-id="${attr(node.id)}"><option value="">Select…</option>${rows}${value && !options.some(item => item.id === value) ? `<option value="${attr(value)}" selected>${esc(value)} (missing)</option>` : ''}</select>`;
    }

    function methodsControl(node, p) {
        const methods = normalizeMethods(parseJson(node.data[p.name], []));
        const labels = methods.slice(0, 5).map(methodSignature);
        const noun = p.type === 'class_methods' ? 'class methods' : 'custom methods';
        return `<div class="tvi-methods-control"><div>${labels.length ? labels.map(v => `<code>${esc(v)}</code>`).join('') : `<span class="tvi-muted">No ${noun}.</span>`}${methods.length > 5 ? `<small>+${methods.length - 5} more</small>` : ''}</div><button type="button" data-inspector-action="manage-methods" data-methods-property="${attr(p.name)}">Manage Methods</button></div>`;
    }

    function normalizeMethods(methods) {
        return (Array.isArray(methods) ? methods : []).filter(m => m && typeof m === 'object').map(m => ({
            ...m,
            name: String(m.name || ''),
            params: normalizeMethodParams(m.params || []),
            return_type: String(m.return_type || ''),
            visibility: ['public','protected','private'].includes(m.visibility) ? m.visibility : 'public',
            static: Boolean(m.static),
            graph_id: String(m.graph_id || ''),
            body: String(m.body || ''),
            help: String(m.help || m.description || ''),
            chainable: Object.prototype.hasOwnProperty.call(m, 'chainable') ? Boolean(m.chainable) : ['self','static'].includes(String(m.return_type || '').replace(/^\?/, '').trim().toLowerCase()),
            implementation: m.implementation === 'flow' ? 'flow' : (m.implementation === 'body' ? 'body' : (m.graph_id ? 'flow' : 'body'))
        }));
    }
    function normalizeMethodParams(params) {
        return (Array.isArray(params) ? params : []).map(param => typeof param === 'string'
            ? { name: sanitize(param), type: '', default: '' }
            : { ...param, name: sanitize(param?.name || ''), type: String(param?.type || '').trim(), default: String(param?.default ?? '').trim(), optional: Boolean(param?.optional), spread: Boolean(param?.spread || param?.variadic) }
        ).filter(param => param.name);
    }
    function methodSignature(method) {
        const params = (Array.isArray(method.params) ? method.params : []).map(raw => {
            const p = typeof raw === 'string' ? { name: sanitize(raw) } : raw || {};
            const name = sanitize(p.name || '');
            if (!name) return '';
            const type = Object.prototype.hasOwnProperty.call(p, 'signature_type') ? String(p.signature_type || '') : String(p.type || '');
            const variadic = p.variadic || p.spread ? '...' : '';
            const defaultValue = Object.prototype.hasOwnProperty.call(p, 'default') ? ` = ${p.default}` : '';
            return `${type ? type + ' ' : ''}${variadic}$${name}${defaultValue}`;
        }).filter(Boolean).join(', ');
        const prefix = method.static ? 'static ' : '';
        return `${prefix}${method.name || 'unnamed'}(${params})${method.return_type ? ': ' + method.return_type : ''}`;
    }
    function phpTypeToPort(type) {
        const values = String(type || '').replace(/^\?/, '').toLowerCase().split('|').map(value => value.trim()).filter(Boolean);
        const one = value => {
            if (value === 'string') return 'string';
            if (['int','integer','float','double'].includes(value)) return 'number';
            if (['bool','boolean'].includes(value)) return 'boolean';
            if (value === 'array' || value === 'iterable') return 'array';
            if (value === 'object' || value === 'self' || value === 'static' || value.includes('\\')) return 'object';
            return 'mixed';
        };
        const ports = [...new Set(values.map(one))];
        return ports.length === 1 ? ports[0] : 'mixed';
    }

    function controllerForGraph(graphId = state.activeGraphId) {
        const graph = state.project.graphs?.[graphId];
        if (!graph?.owner_node_id) return null;
        const owner = projectNodeById(graph.owner_node_id);
        return owner?.type === 'lifecycle.controller' ? owner : null;
    }

    function viewForGraph(graphId = state.activeGraphId) {
        const graph = state.project.graphs?.[graphId];
        if (!graph?.owner_node_id) return null;
        const owner = projectNodeById(graph.owner_node_id);
        return ['lifecycle.view', 'lifecycle.reusable_view'].includes(owner?.type) ? owner : null;
    }

    function lookForView(viewNode) {
        return connectedArchitectureNode(viewNode, 'looks.look') || projectNodeByType('looks.look');
    }

    function lookThemeState(lookNode) {
        const theme = formThemeById(lookNode?.data?.form_theme_id || 'classic');
        if (!theme) return { look: lookNode || null, theme: null, palette: { id: 'default', name: 'Default', colors: {} }, colors: {} };
        const palette = formThemePaletteById(theme, lookNode?.data?.form_theme_palette_id || theme.default_palette || 'default');
        return {
            look: lookNode || null,
            theme,
            palette,
            colors: {
                ...formThemeColorMap(palette.colors),
                ...formThemeColorMap(lookNode?.data?.form_theme_colors)
            }
        };
    }

    function viewThemeState(viewNode) {
        return lookThemeState(lookForView(viewNode));
    }

    function themeVariableDeclarations(colors) {
        return Object.entries(formThemeColorMap(colors))
            .map(([name, value]) => `--thv-theme-${String(name).replace(/_/g, '-')}:${String(value)};`)
            .join('');
    }

    function controllerDataVariable(controller) {
        return phpVariableName(controller?.data?.data_variable) || 'data';
    }

    function controllerDataVariableForGraph(graphId = state.activeGraphId) {
        return controllerDataVariable(controllerForGraph(graphId));
    }

    function isFilterController(controller) {
        return controller?.type === 'lifecycle.controller' && controller?.data?.hook_type === 'filter';
    }

    function isProtectedNode(node, graphId = state.activeGraphId) {
        if (!node) return false;
        if (node.type === 'flow.start') return true;
        const controller = controllerForGraph(graphId);
        return isFilterController(controller) && node.type === 'flow.filter_return';
    }

    function canMuteNode(node, graphId = state.activeGraphId) {
        const structuralTypes = new Set([
            'visual.folder', 'flow.start', 'migration.start',
            'database.query_builder', 'session.session', 'request.request', 'images.image'
        ]);
        if (!node || structuralTypes.has(node.type)) return false;
        return !isProtectedNode(node, graphId);
    }

    function nodeIsMuted(node) {
        return Boolean(node?.data?.muted);
    }

    function toggleMuteSelected(force = null) {
        const candidates = selectedNodes().filter(node => canMuteNode(node));
        if (!candidates.length) {
            toast('The selected nodes cannot be muted.', 'warn');
            return;
        }
        const shouldMute = force === null ? !candidates.every(nodeIsMuted) : Boolean(force);
        snapshot();
        for (const node of candidates) {
            node.data ||= {};
            node.data.muted = shouldMute;
        }
        changed();
        renderAll();
        toast(`${candidates.length} node${candidates.length === 1 ? '' : 's'} ${shouldMute ? 'muted' : 'unmuted'}.`, 'success');
    }

    function executionOutputPorts(node) {
        return (defFor(node)?.ports?.outputs || []).filter(port => port.type === 'exec').map(port => String(port.id || 'exec'));
    }

    function ensureFilterReturnConnections(graph, startNode, returnNode) {
        const reachable = new Set([startNode.id]);
        const queue = [startNode.id];
        while (queue.length) {
            const current = queue.shift();
            const sourceNode = graph.nodes.find(node => node.id === current);
            const execPorts = new Set(executionOutputPorts(sourceNode));
            for (const edge of graph.edges || []) {
                if (edge.from !== current || !execPorts.has(String(edge.from_port || 'exec'))) continue;
                if (!reachable.has(edge.to)) { reachable.add(edge.to); queue.push(edge.to); }
            }
        }
        for (const nodeId of reachable) {
            if (nodeId === returnNode.id) continue;
            const node = graph.nodes.find(item => item.id === nodeId);
            if (!node) continue;
            for (const portId of executionOutputPorts(node)) {
                const hasNext = graph.edges.some(edge => edge.from === node.id && edge.from_port === portId);
                if (!hasNext) {
                    graph.edges.push({ id: uid('edge'), from: node.id, from_port: portId, to: returnNode.id, to_port: 'exec_in', port_type: 'exec' });
                }
            }
        }
    }

    function ensureControllerFlowStructure(controller, connectDangling = true) {
        if (!controller?.data?.graph_id) return;
        const graph = state.project.graphs[controller.data.graph_id];
        if (!graph || graph.kind !== 'flow') return;
        graph.owner_node_id = controller.id;
        let startNode = graph.nodes.find(node => node.type === 'flow.start');
        if (isFilterController(controller)) {
            const extraStartIds = new Set(graph.nodes.filter(node => node.type === 'flow.start' && node.id !== startNode?.id).map(node => node.id));
            if (extraStartIds.size) {
                graph.nodes = graph.nodes.filter(node => !extraStartIds.has(node.id));
                graph.edges = graph.edges.filter(edge => !extraStartIds.has(edge.from) && !extraStartIds.has(edge.to));
            }
            if (!startNode) {
                startNode = { id: uid('start'), type: 'flow.start', x: 70, y: 100, data: { title: 'Start' } };
                graph.nodes.push(startNode);
            }
            let returnNode = graph.nodes.find(node => node.type === 'flow.filter_return');
            const extraReturnIds = new Set(graph.nodes.filter(node => node.type === 'flow.filter_return' && node.id !== returnNode?.id).map(node => node.id));
            if (extraReturnIds.size) {
                graph.nodes = graph.nodes.filter(node => !extraReturnIds.has(node.id));
                graph.edges = graph.edges.filter(edge => !extraReturnIds.has(edge.from) && !extraReturnIds.has(edge.to));
            }
            if (!returnNode) {
                const right = graph.nodes.reduce((max, node) => Math.max(max, Number(node.x) || 0), 70);
                returnNode = { id: uid('filter-return'), type: 'flow.filter_return', x: right + 300, y: Number(startNode.y) || 100, data: { title: 'Return Filter Data' } };
                graph.nodes.push(returnNode);
            }
            returnNode.data = { ...(defFor(returnNode)?.defaults || {}), ...(returnNode.data || {}) };
            if (connectDangling) ensureFilterReturnConnections(graph, startNode, returnNode);
        } else {
            const returnIds = new Set(graph.nodes.filter(node => node.type === 'flow.filter_return').map(node => node.id));
            if (returnIds.size) {
                graph.nodes = graph.nodes.filter(node => !returnIds.has(node.id));
                graph.edges = graph.edges.filter(edge => !returnIds.has(edge.from) && !returnIds.has(edge.to));
            }
        }
    }

    function synchronizeControllerFlowStructures(connectDangling = true) {
        for (const controller of mainGraph().nodes.filter(node => node.type === 'lifecycle.controller')) {
            ensureControllerFlowStructure(controller, connectDangling);
        }
    }

    function folderMembers(folder) {
        const members = new Set(folder.data.member_ids || []); const rows = activeGraph().nodes.filter(n => n.id !== folder.id && n.type !== 'visual.folder').map(n => `<label class="tvi-member"><input type="checkbox" data-folder-member="${attr(n.id)}" ${members.has(n.id) ? 'checked' : ''}><span>${esc(nodeTitle(n))}</span></label>`).join('');
        return `<div class="tvi-folder-members"><h4>Folder members</h4>${rows || '<p>No eligible nodes.</p>'}<button type="button" data-inspector-action="clear-folder">Clear members</button></div>`;
    }

    function projectNodeById(id) {
        if (!id) return null;
        for (const graph of Object.values(state.project.graphs || {})) {
            const node = (graph.nodes || []).find(item => item.id === id);
            if (node) return node;
        }
        return null;
    }

    function graphContext(graph) {
        if (!graph || graph.id === 'main') return null;
        const owner = projectNodeById(graph.owner_node_id);
        const labels = {
            'lifecycle.controller': owner?.data?.hook_type === 'filter' ? 'Filter flow' : 'Action flow',
            'lifecycle.view': graph.kind === 'javascript' ? 'JavaScript flow' : 'View builder',
            'lifecycle.reusable_view': graph.kind === 'javascript' ? 'JavaScript flow' : 'Reusable view builder',
            'custom.function_definition': 'Function flow',
            'models.model': 'Model method flow',
            'custom.class_definition': 'Class method flow',
            'migration.definition': 'Migration graph'
        };
        return {
            type: labels[owner?.type] || (graph.kind === 'view' ? 'View builder' : graph.kind === 'javascript' ? 'JavaScript flow' : graph.kind === 'migration' ? 'Migration graph' : 'Execution flow'),
            name: owner ? nodeTitle(owner) : String(graph.name || 'Nested graph')
        };
    }

    function renderMeta() {
        const graph = activeGraph();
        const context = graphContext(graph);
        document.getElementById('tvi-graph-title').textContent = context ? context.name : graph.name;
        document.getElementById('tvi-graph-kind').textContent = graph.kind === 'flow' ? 'Execution Flow' : graph.kind === 'view' ? 'View Builder' : graph.kind === 'javascript' ? 'JavaScript Flow' : graph.kind === 'migration' ? 'Migration Graph' : 'Architecture';
        graphContextBanner.hidden = !context;
        if (context) {
            graphContextType.textContent = context.type;
            graphContextName.textContent = context.name;
        }
        document.getElementById('tvi-back-graph').hidden = state.activeGraphId === 'main';
        document.getElementById('tvi-node-count').textContent = `${graph.nodes.length} nodes`;
        document.getElementById('tvi-edge-count').textContent = `${graph.edges.length} connections`;
        const ws=workspace();document.getElementById('tvi-save-state').textContent = state.dirty ? `Unsaved${ws.project_name?' · '+ws.project_name:''}` : ws.project_name ? `Saved · ${ws.project_name}` : 'Not server-saved';
        document.getElementById('tvi-selection-hint').textContent = state.connecting ? 'Choose a compatible input socket' : state.selectedNodeIds.length ? `${state.selectedNodeIds.length} selected` : 'Select a node to edit it';
        emptyState.hidden = graph.nodes.length > 0;
        document.getElementById('tvi-zoom-label').textContent = `${Math.round(graph.viewport.zoom*100)}%`;
    }

    function nodeTitle(node) { return String(node.data?.title || node.data?.name || node.data?.class_name || defFor(node)?.label || node.type); }
    function nodeSummary(node) {
        const d=node.data||{};
        switch(node.type){
            case 'project.plugin': return `${d.id || ''} · ${d.type || ''}`;
            case 'routing.route': return `${d.method || 'GET'} ${d.path || '/'}`;
            case 'migration.definition': return d.filename_slug || 'migration';
            case 'migration.form_table': return `${d.table || 'table'} ← ${d.form_name || 'form'}`;
            case 'migration.create_table': case 'migration.add_column': case 'migration.modify_column': case 'migration.rename_column': case 'migration.drop_column': case 'migration.add_index': case 'migration.add_foreign_key': return d.table || '';
            case 'migration.raw': return 'Custom up/down SQL';
            case 'data.array_combine': return `${d.mode || 'replace'} · ${d.array_count || 2} arrays`;
            case 'data.concatenate': return `${d.input_count || 2} values${d.separator ? ` · separator ${JSON.stringify(d.separator)}` : ''}`;
            case 'logic.boolean_group': return `${String(d.operator || 'AND').toUpperCase()} · ${d.condition_count || 2} conditions`; 
            case 'lifecycle.controller': return `${d.hook_type === 'filter' ? 'filter' : 'action'} ${d.hook_name || 'controller'} · $${phpVariableName(d.data_variable) || 'data'} · ${d.priority || 10}`;
            case 'looks.look': { const theme=formThemeById(d.form_theme_id||'classic'); return `${d.folder || 'main'} · ${theme?.name || d.form_theme_id || 'Classic Clean'}`; }
            case 'lifecycle.view': case 'lifecycle.reusable_view': return d.filename || '';
            case 'models.model': return `${d.class_name || ''} → ${d.table || ''}`;
            case 'models.method_call': return d.method_name || 'Choose method';
            case 'custom.class_definition': return d.class_name || 'Custom class';
            case 'custom.class_instance': return d.class_node_id ? 'Create instance' : 'Choose class';
            case 'custom.class_method_call': return d.method_name || 'Choose method';
            case 'data.string': return String(d.value || '').slice(0,36);
            case 'data.variable': { const base=d.source_type==='constant'?(d.constant_name||'ROOT'):`$${d.variable_name||'value'}`; return `${base}${d.path?'.'+d.path:''}`; }
            case 'data.hook_argument': { const name=controllerDataVariableForGraph(state.activeGraphId); return `$${name}${d.path ? '.'+d.path : ''}`; }
            case 'flow.start': { const owner=controllerForGraph(state.activeGraphId); return owner?.data?.hook_type === 'filter' ? `$${controllerDataVariable(owner)} enters filter` : ''; }
            case 'flow.filter_return': return `return $${controllerDataVariableForGraph(state.activeGraphId)}`;
            case 'flow.exit': return `${d.function === 'die' ? 'die' : 'exit'}${d.use_value ? '(value)' : ''}`;
            case 'debug.display_data': return 'dd(data)';
            case 'routing.url': return d.source === 'segment' ? `URL(${d.segment_index})` : d.param_name || '';
            case 'view.form': return `${d.method || 'POST'} · ${d.schema_name || d.component_name || 'inline'}`;
            case 'view.text': return String(d.text || '').slice(0,36);
            case 'view.old_input': return d.field || '';
            case 'view.empty_state': return d.path || '';
            case 'request.is_ajax': return 'Core\\Request::is_ajax()';
            case 'response.json': return `HTTP ${d.status_code || 200}`;
            case 'forms.data': case 'forms.validate': return d.form_name || 'Choose form';
            case 'view.component': return d.component_name || 'component';
            case 'view.pagination': return `${d.template_id || 'minimal'} · $${d.pager_variable || 'pager'}`;
            case 'view.html_component': return d.component_name || 'custom-component';
            case 'view.html_boilerplate': return '<tvi-content /> document shell';
            case 'view.text_input': case 'view.email_input': case 'view.password_input': case 'view.number_input': case 'view.date_input': case 'view.file_input': case 'view.textarea': case 'view.select': case 'view.checkbox': return d.name || '';
            case 'view.variable': return d.path || 'value';
            case 'view.loop': return d.collection_path || 'items';
            case 'view.if': return d.path || 'condition';
            case 'authorization.all_permissions': return `${catalogItems(node, 'permission').length} permissions`;
            case 'authorization.all_roles': return `${catalogItems(node, 'role').length} roles`;
            case 'authorization.has_permission': return d.permission || 'Choose permission';
            case 'authorization.has_role': return d.role || 'Choose role';
            case 'authorization.set_permissions': return 'user_permissions filter';
            case 'authorization.set_roles': return 'user_roles filter';
            case 'data.cast': return `to ${d.target_type || 'array'}`;
            case 'images.image': return `JPEG ${d.jpeg_quality ?? 85} · WEBP ${d.webp_quality ?? 80}`;
            case 'images.method': return d.method || 'resize';
            case 'request.request': return 'Core\\Request';
            case 'request.method': return d.method || 'post';
            case 'database.query_builder': return 'Core\\Database + QueryBuilder';
            case 'database.query_builder_method': return d.method || 'table';
            case 'charts.dataset': return `${d.label || 'Dataset'} · ${d.chart_type || 'inherit'}`;
            case 'charts.builder': return `${d.chart_type || 'bar'} · ${d.dataset_count || 1} dataset${Number(d.dataset_count || 1) === 1 ? '' : 's'}`;
            case 'view.chart': return d.data_key || 'chart';
            case 'data.array_column': return d.column || 'value';
            case 'visual.folder': return `${(d.member_ids || []).length} members`;
            default: return defFor(node)?.description || '';
        }
    }

    function getPorts(node) {
        const def = defFor(node); const ports = clone(def?.ports || {inputs:[],outputs:[]}); ports.inputs ||= []; ports.outputs ||= [];
        if (def?.dynamic_ports === 'arguments') {
            const count=clamp(parseInt(node.data.param_count)||0,0,20); for(let i=1;i<=count;i++) ports.inputs.push({id:`arg_${i}`,label:`Arg ${i}`,type:'mixed',direction:'in'});
        }
        if (def?.dynamic_ports === 'array') {
            const count=clamp(parseInt(node.data.item_count)||0,0,30); const keys=String(node.data.keys||'').split(',').map(s=>s.trim());
            for(let i=1;i<=count;i++) ports.inputs.push({id:`item_${i}`,label:node.data.mode==='associative'?(keys[i-1]||`Item ${i}`):`Item ${i}`,type:'mixed',direction:'in'});
        }
        if (def?.dynamic_ports === 'array_combine') {
            const count=clamp(parseInt(node.data.array_count)||2,2,20);
            for(let i=1;i<=count;i++) ports.inputs.push({id:`array_${i}`,label:`Array ${i}`,type:'array',direction:'in'});
        }
        if (def?.dynamic_ports === 'chart_datasets') {
            const count=clamp(parseInt(node.data.dataset_count)||1,1,20);
            for(let i=1;i<=count;i++) ports.inputs.push({id:`dataset_${i}`,label:`Dataset ${i}`,type:'chart-dataset',direction:'in'});
        }
        if (def?.dynamic_ports === 'extreme_values') {
            const count=clamp(parseInt(node.data.input_count)||2,2,30);
            for(let i=1;i<=count;i++) ports.inputs.push({id:`value_${i}`,label:`Value ${i}`,type:'mixed',direction:'in'});
        }
        if (def?.dynamic_ports === 'concatenate') {
            const count=clamp(parseInt(node.data.input_count)||2,2,30);
            for(let i=1;i<=count;i++) ports.inputs.push({id:`value_${i}`,label:`Value ${i}`,type:'mixed',direction:'in'});
        }
        if (def?.dynamic_ports === 'js_array') {
            const count=clamp(parseInt(node.data.item_count)||0,0,30);
            for(let i=1;i<=count;i++) ports.inputs.push({id:`item_${i}`,label:`Item ${i}`,type:'js-value',direction:'in'});
        }
        if (def?.dynamic_ports === 'js_object') {
            const count=clamp(parseInt(node.data.property_count)||0,0,30); const keys=String(node.data.keys||'').split(/[\r\n,]+/).map(s=>s.trim());
            for(let i=1;i<=count;i++) ports.inputs.push({id:`property_${i}`,label:keys[i-1]||`Property ${i}`,type:'js-value',direction:'in'});
        }
        if (def?.dynamic_ports === 'js_concatenate') {
            const count=clamp(parseInt(node.data.input_count)||2,2,30);
            for(let i=1;i<=count;i++) ports.inputs.push({id:`value_${i}`,label:`Value ${i}`,type:'js-value',direction:'in'});
        }
        if (def?.dynamic_ports === 'js_code_inputs') {
            const count=clamp(parseInt(node.data.input_count)||0,0,20); const names=String(node.data.input_names||'').split(/[\r\n,]+/).map(s=>s.trim());
            for(let i=1;i<=count;i++) ports.inputs.push({id:`value_${i}`,label:names[i-1]||`Value ${i}`,type:'js-value',direction:'in'});
        }
        if (def?.dynamic_ports === 'boolean_group') {
            const count=clamp(parseInt(node.data.condition_count)||2,2,20);
            for(let i=1;i<=count;i++) ports.inputs.push({id:`condition_${i}`,label:`Condition ${i}`,type:'boolean',direction:'in'});
        }
        if (def?.dynamic_ports === 'model_method') {
            const model=nodeById(node.data.model_node_id,'main'); const methods=normalizeMethods(parseJson(model?.data?.methods_json,[])); const method=methods.find(m=>m.name===node.data.method_name);
            const chainable = methodIsChainable(method);
            if (chainable) {
                ports.inputs.push({id:'model',label:'Model',type:'object',direction:'in',multiple:false});
                ports.outputs.push({id:'model',label:'Continue',type:'object',direction:'out',multiple:true});
            }
            for(const p of (method?.params||[])) ports.inputs.push({id:`param_${sanitize(p.name)}`,label:`${p.name}${p.type ? ': ' + p.type : ''}${p.default ? ' = ' + p.default : ''}`,type:phpTypeToPort(p.type),direction:'in'});
            const result = ports.outputs.find(p => p.id === 'result'); if (result) { result.type = phpTypeToPort(method?.return_type); if (chainable) result.label = 'Chain Result'; }
        }
        if (def?.dynamic_ports === 'function_call') {
            const fn=nodeById(node.data.function_node_id,'main'); const params=String(fn?.data?.parameters||'').split(',').map(s=>s.trim()).filter(Boolean);
            for(const p of params) ports.inputs.push({id:`param_${sanitize(p)}`,label:p,type:'mixed',direction:'in'});
        }
        if (def?.dynamic_ports === 'class_constructor') {
            const classNode=nodeById(node.data.class_node_id,'main'); const methods=normalizeMethods(parseJson(classNode?.data?.methods_json,[])); const method=methods.find(m=>m.name==='__construct');
            for(const p of (method?.params||[])) ports.inputs.push({id:`param_${sanitize(p.name)}`,label:`${p.name}${p.type ? ': ' + p.type : ''}${p.default ? ' = ' + p.default : ''}`,type:phpTypeToPort(p.type),direction:'in'});
        }
        if (def?.dynamic_ports === 'class_method') {
            const classNode=nodeById(node.data.class_node_id,'main'); const methods=normalizeMethods(parseJson(classNode?.data?.methods_json,[])); const method=methods.find(m=>m.name===node.data.method_name);
            if (method?.static) ports.inputs = ports.inputs.filter(p => p.id !== 'object');
            for(const p of (method?.params||[])) ports.inputs.push({id:`param_${sanitize(p.name)}`,label:`${p.name}${p.type ? ': ' + p.type : ''}${p.default ? ' = ' + p.default : ''}`,type:phpTypeToPort(p.type),direction:'in'});
            const result = ports.outputs.find(p => p.id === 'result'); if (result) { result.type = phpTypeToPort(method?.return_type); if (methodIsChainable(method)) result.label = 'Continue'; }
        }
        if (def?.dynamic_ports === 'cast') {
            const result = ports.outputs.find(port => port.id === 'value');
            const types = { array: 'array', object: 'object', string: 'string', int: 'number', float: 'number', bool: 'boolean', json_to_array: 'array', json_to_object: 'object' };
            if (result) result.type = types[node.data.target_type] || 'mixed';
        }
        if (def?.dynamic_ports === 'image_method') {
            const methods = Array.isArray(def.methods) ? def.methods : [];
            const method = methods.find(item => item?.name === node.data.method) || methods[0];
            for (const parameter of (method?.params || [])) {
                const name = String(parameter?.name || '').replace(/[^A-Za-z0-9_]/g, '');
                if (!name) continue;
                const label = String(parameter?.label || parameter?.name || name);
                const type = phpTypeToPort(parameter?.type);
                ports.inputs.push({
                    id: `param_${name}`,
                    label: parameter?.type ? `${label}: ${parameter.type}` : label,
                    type,
                    direction: 'in'
                });
            }
            const chainable = methodIsChainable(method);
            const continuation = ports.outputs.find(port => port.id === 'image');
            if (continuation && chainable) continuation.label = 'Continue';
            if (!chainable) ports.outputs = ports.outputs.filter(port => port.id !== 'image');
            const result = ports.outputs.find(port => port.id === 'result');
            if (result) result.type = phpTypeToPort(method?.return_type);
        }
        if (def?.dynamic_ports === 'request_method') {
            const methods = Array.isArray(def.methods) ? def.methods : [];
            const method = methods.find(item => item?.name === node.data.method) || methods[0];
            for (const parameter of (method?.params || [])) {
                const name = String(parameter?.name || '').replace(/[^A-Za-z0-9_]/g, '');
                if (!name) continue;
                const label = String(parameter?.label || parameter?.name || name);
                const type = phpTypeToPort(parameter?.type);
                ports.inputs.push({
                    id: `param_${name}`,
                    label: parameter?.type ? `${label}: ${parameter.type}` : label,
                    type,
                    direction: 'in'
                });
            }
            const chainable = methodIsChainable(method);
            const continuation = ports.outputs.find(port => port.id === 'request');
            if (continuation && chainable) continuation.label = 'Continue';
            if (!chainable) ports.outputs = ports.outputs.filter(port => port.id !== 'request');
            const result = ports.outputs.find(port => port.id === 'result');
            if (result) result.type = phpTypeToPort(method?.return_type);
        }
        if (def?.dynamic_ports === 'session_method') {
            const methods = Array.isArray(def.methods) ? def.methods : [];
            const method = methods.find(item => item?.name === node.data.method) || methods[0];
            for (const parameter of (method?.params || [])) {
                const name = String(parameter?.name || '').replace(/[^A-Za-z0-9_]/g, '');
                if (!name) continue;
                const label = String(parameter?.label || parameter?.name || name);
                const type = phpTypeToPort(parameter?.type);
                ports.inputs.push({
                    id: `param_${name}`,
                    label: parameter?.type ? `${label}: ${parameter.type}` : label,
                    type,
                    direction: 'in'
                });
            }
            const chainable = methodIsChainable(method);
            const continuation = ports.outputs.find(port => port.id === 'session');
            if (continuation && chainable) continuation.label = 'Continue';
            if (!chainable) ports.outputs = ports.outputs.filter(port => port.id !== 'session');
            const result = ports.outputs.find(port => port.id === 'result');
            if (result) result.type = phpTypeToPort(method?.return_type);
        }
        if (def?.dynamic_ports === 'query_builder_method') {
            const methods = Array.isArray(def.methods) ? def.methods : [];
            const method = methods.find(item => item?.name === node.data.method) || methods[0];
            for (const parameter of (method?.params || [])) {
                const name = String(parameter?.name || '').replace(/[^A-Za-z0-9_]/g, '');
                if (!name) continue;
                const label = String(parameter?.label || parameter?.name || name);
                const type = phpTypeToPort(parameter?.type);
                ports.inputs.push({ id: `param_${name}`, label: parameter?.type ? `${label}: ${parameter.type}` : label, type, direction: 'in' });
            }
            const chainable = methodIsChainable(method);
            const continuation = ports.outputs.find(port => port.id === 'builder');
            if (continuation && chainable) continuation.label = 'Continue';
            if (!chainable) ports.outputs = ports.outputs.filter(port => port.id !== 'builder');
            const result = ports.outputs.find(port => port.id === 'result');
            if (result) result.type = phpTypeToPort(method?.return_type);
        }
        if (def?.dynamic_ports === 'form_schema_data') {
            const selected = viewForms(node.data.view_node_id).find(item => item.name === node.data.form_name) || viewForms(node.data.view_node_id)[0];
            if (selected) {
                const graph = state.project.graphs[nodeById(node.data.view_node_id,'main')?.data?.graph_id];
                const fieldTypes = {'view.number_input':'number'};
                const visited = new Set();
                const walk = id => {
                    if (visited.has(id)) return; visited.add(id);
                    for (const edge of graph.edges.filter(e => e.from === id && e.port_type === 'view-child')) {
                        const child = graph.nodes.find(n => n.id === edge.to); if (!child) continue;
                        const name = String(child.data?.name || '').trim();
                        if (name && ['view.text_input','view.email_input','view.password_input','view.number_input','view.date_input','view.file_input','view.textarea','view.select','view.checkbox','view.hidden_input'].includes(child.type)) {
                            ports.outputs.push({id:`field_${sanitize(name)}`,label:name,type:fieldTypes[child.type]||'string',direction:'out',multiple:true});
                        }
                        walk(child.id);
                    }
                };
                walk(selected.id);
            }
        }
        return ports;
    }

    function migrationTimestamp(){const d=new Date(),pad=v=>String(v).padStart(2,'0');return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}_${pad(d.getHours())}${pad(d.getMinutes())}${pad(d.getSeconds())}`;}
    function addNode(type,x,y) {
        const def=definitions[type]; if(!def || !(def.allowed_graphs||[]).includes(activeGraph().kind)) return;
        if(def.singleton && activeGraph().nodes.some(n=>n.type===type)){toast('Only one of this node is allowed in the current graph.','fail');return;}
        snapshot(); const node={id:uid(type.replace(/\W/g,'-')),type,x,y,data:clone(def.defaults||{})};if(type==='migration.definition'&&!node.data.timestamp)node.data.timestamp=migrationTimestamp();
        activeGraph().nodes.push(node); if(def.nested_graph) createNestedGraph(node,true); selectNodes([node.id]); changed(); renderAll();
    }
    function addNodeAtCenter(type){const r=viewport.getBoundingClientRect(),p=screenToWorld(r.left+r.width/2,r.top+r.height/2);addNode(type,p.x-110,p.y-50);}
    function createNestedGraph(node, includeStart=true){const def=defFor(node)||{};const kind=def.nested_graph_kind||'flow';const label=def.nested_graph_label||(kind==='view'?'View Builder':'Flow');const id=node.data.graph_id||uid('graph');node.data.graph_id=id;if(!state.project.graphs[id])state.project.graphs[id]={id,name:`${nodeTitle(node)} ${label}`,kind,owner_node_id:node.id,nodes:[],edges:[],viewport:{x:80,y:80,zoom:1}};const g=state.project.graphs[id];g.kind=kind;g.owner_node_id=node.id;if(includeStart&&kind==='flow'&&!g.nodes.some(n=>n.type==='flow.start'))g.nodes.push({id:uid('start'),type:'flow.start',x:70,y:100,data:{title:'Start'}});if(includeStart&&kind==='migration'&&!g.nodes.some(n=>n.type==='migration.start'))g.nodes.push({id:uid('migration-start'),type:'migration.start',x:70,y:100,data:{title:'Migration Start'}});if(node.type==='lifecycle.controller')ensureControllerFlowStructure(node,includeStart);}
    function createJavaScriptGraph(node, includeStart=true){
        const id=node.data.javascript_graph_id||uid('javascript-graph');
        node.data.javascript_graph_id=id;
        if(!state.project.graphs[id])state.project.graphs[id]={id,name:`${nodeTitle(node)} JavaScript Flow`,kind:'javascript',owner_node_id:node.id,nodes:[],edges:[],viewport:{x:80,y:80,zoom:1}};
        const graph=state.project.graphs[id];graph.kind='javascript';graph.owner_node_id=node.id;
        if(includeStart&&!graph.nodes.some(item=>item.type==='javascript.start'))graph.nodes.push({id:uid('js-start'),type:'javascript.start',x:70,y:100,data:{title:'JS Start'}});
        return id;
    }
    function nestedGraphIds(node){
        const ids=[];
        if(node?.data?.graph_id)ids.push(String(node.data.graph_id));
        if(node?.data?.javascript_graph_id)ids.push(String(node.data.javascript_graph_id));
        if(['custom.class_definition','models.model'].includes(node?.type))for(const method of normalizeMethods(parseJson(node.data.methods_json,[])))if(method.graph_id)ids.push(method.graph_id);
        return [...new Set(ids)];
    }
    function remapNestedGraphReferences(node,graphMap){
        if(node?.data?.graph_id&&graphMap[node.data.graph_id])node.data.graph_id=graphMap[node.data.graph_id];
        if(node?.data?.javascript_graph_id&&graphMap[node.data.javascript_graph_id])node.data.javascript_graph_id=graphMap[node.data.javascript_graph_id];
        if(['custom.class_definition','models.model'].includes(node?.type)){
            const methods=normalizeMethods(parseJson(node.data.methods_json,[]));
            for(const method of methods)if(method.graph_id&&graphMap[method.graph_id])method.graph_id=graphMap[method.graph_id];
            node.data.methods_json=JSON.stringify(methods,null,2);
        }
    }

    function portElement(nodeId,portId,direction){return nodesLayer.querySelector(`.tvi-port[data-node-id="${cssEscape(nodeId)}"][data-port-id="${cssEscape(portId)}"][data-port-direction="${direction}"]`);}
    function portWorldCenter(el){
        const r=el.getBoundingClientRect();
        const direction=el.dataset.portDirection;
        // Anchor to the visible socket pseudo-element rather than the centre of the label button.
        // The socket centre is approximately one pixel outside the button edge.
        const x=direction==='in'?r.left-1:r.right+1;
        return screenToWorld(x,r.top+r.height/2);
    }
    function edgeType(edge){const n=nodeById(edge.from);const p=getPorts(n).outputs.find(p=>p.id===edge.from_port);return p?.type||'mixed';}

    function nodeClick(e) {
        const toggle=e.target.closest('[data-folder-toggle]'); if(toggle){e.stopPropagation();toggleFolder(toggle.dataset.folderToggle);return;}
        const port=e.target.closest('.tvi-port'); if(port){e.stopPropagation();handlePort(port);return;}
        const el=e.target.closest('.tvi-node'); if(!el)return; e.stopPropagation();
        const id=el.dataset.nodeId;
        if(e.shiftKey||e.ctrlKey||e.metaKey) toggleSelection(id); else selectNodes([id]);
        state.selectedEdgeId=null;
        renderSelectionClasses(); renderInspector(); renderMeta(); renderEdges();
    }

    function nodeDoubleClick(e){
        if(e.target.closest('.tvi-port,.tvi-folder-toggle'))return;
        const el=e.target.closest('.tvi-node');if(!el)return;
        e.preventDefault();e.stopPropagation();
        const node=nodeById(el.dataset.nodeId);const def=defFor(node);
        if(node?.type==='view.html_component'){selectNodes([node.id]);renderSelectionClasses();renderInspector();openHtmlDesigner(node.id);return;}
        if(methodBrowserSource(node).supported){selectNodes([node.id]);renderSelectionClasses();renderInspector();openMethodBrowser(node.id);return;}
        if(def?.nested_graph){if(!node.data.graph_id)createNestedGraph(node,true);openGraph(node.data.graph_id);return;}
        if(['models.model','custom.class_definition'].includes(node.type)){selectNodes([node.id]);renderSelectionClasses();renderInspector();openMethods('methods_json');}
    }

    function handlePort(el) {
        const nodeId=el.dataset.nodeId,portId=el.dataset.portId,direction=el.dataset.portDirection;
        if(direction==='out'){state.connecting={nodeId,portId};setStatus('Choose a compatible input socket');renderMeta();return;}
        if(!state.connecting){setStatus('Start from an output socket');return;}
        if(state.connecting.nodeId===nodeId){state.connecting=null;renderMeta();return;}
        const fromNode=nodeById(state.connecting.nodeId),toNode=nodeById(nodeId);const fromPort=getPorts(fromNode).outputs.find(p=>p.id===state.connecting.portId),toPort=getPorts(toNode).inputs.find(p=>p.id===portId);
        if(!fromPort||!toPort||!portsCompatible(fromPort,toPort)){toast(`Cannot connect ${fromPort?.type||'?'} to ${toPort?.type||'?'}`,'fail');return;}
        snapshot(); if(!toPort.multiple) activeGraph().edges=activeGraph().edges.filter(e=>!(e.to===nodeId&&e.to_port===portId));
        activeGraph().edges.push({id:uid('edge'),from:state.connecting.nodeId,from_port:state.connecting.portId,to:nodeId,to_port:portId,port_type:fromPort.type});state.connecting=null;synchronizeAllLifecyclePriorities(false);changed();renderAll();
    }
    function portsCompatible(a,b){if(a.direction!=='out'||b.direction!=='in')return false;if(['exec','js-exec'].includes(a.type)||['exec','js-exec'].includes(b.type))return a.type===b.type;if(String(a.type).startsWith('js-')||String(b.type).startsWith('js-')){if(a.type==='js-value'||b.type==='js-value')return a.type!=='js-exec'&&b.type!=='js-exec';return a.type===b.type;}if(['controller','view','asset'].includes(a.type)||['controller','view','asset'].includes(b.type))return a.type===b.type;if(a.type==='arguments'||b.type==='arguments')return a.type===b.type;return a.type===b.type||a.type==='mixed'||b.type==='mixed';}

    function edgeClick(e){const t=e.target.closest('[data-edge-id]');if(!t)return;e.stopPropagation();selectNodes([]);state.selectedEdgeId=t.dataset.edgeId;renderAll();}
    function removeEdge(id){const i=activeGraph().edges.findIndex(e=>e.id===id);if(i<0)return;snapshot();activeGraph().edges.splice(i,1);state.selectedEdgeId=null;synchronizeAllLifecyclePriorities(false);changed();renderAll();}

    function preventCanvasSelection(e){e.preventDefault();}
    function preventCanvasNativeDrag(e){if(!e.dataTransfer?.types?.includes?.('application/x-thunder-node'))e.preventDefault();}
    function clearNativeSelection(){const selection=window.getSelection?.();if(selection&&!selection.isCollapsed)selection.removeAllRanges();}

    function nodePointerDown(e){if(e.button!==0||e.target.closest('.tvi-port,.tvi-folder-toggle'))return;if(state.spacePressed)return;const el=e.target.closest('.tvi-node');if(!el)return;e.preventDefault();e.stopPropagation();clearNativeSelection();const id=el.dataset.nodeId;if(!state.selectedNodeIds.includes(id)&&!e.shiftKey&&!e.ctrlKey&&!e.metaKey)selectNodes([id]);const ids=new Set(state.selectedNodeIds);const node=nodeById(id);if(node?.type==='visual.folder')for(const mid of folderDescendants(node))ids.add(mid);const starts={};for(const nid of ids){const n=nodeById(nid);if(n)starts[nid]={x:n.x,y:n.y};}state.interaction={type:'move',pointerId:e.pointerId,startX:e.clientX,startY:e.clientY,starts,moved:false};viewport.classList.add('is-interacting');el.setPointerCapture?.(e.pointerId);window.addEventListener('pointermove',interactionMove);window.addEventListener('pointerup',interactionEnd,{once:true});}

    function viewportPointerDown(e){if(e.button===1||state.spacePressed){e.preventDefault();clearNativeSelection();beginPan(e);return;}if(e.button!==0||e.target.closest('.tvi-node,.tvi-edge,.tvi-edge-delete'))return;e.preventDefault();clearNativeSelection();beginSelectionBox(e);}
    function beginPan(e){const v=activeGraph().viewport;state.interaction={type:'pan',pointerId:e.pointerId,startX:e.clientX,startY:e.clientY,x:v.x,y:v.y};viewport.classList.add('is-panning','is-interacting');window.addEventListener('pointermove',interactionMove);window.addEventListener('pointerup',interactionEnd,{once:true});}
    function beginSelectionBox(e){const additive=e.shiftKey||e.ctrlKey||e.metaKey;if(!additive){selectNodes([]);state.selectedEdgeId=null;renderSelectionClasses();renderInspector();renderMeta();renderEdges();}const box=document.createElement('div');box.className='tvi-selection-box';viewport.appendChild(box);viewport.classList.add('is-interacting');state.interaction={type:'box',pointerId:e.pointerId,startX:e.clientX,startY:e.clientY,box,additive,initial:[...state.selectedNodeIds]};window.addEventListener('pointermove',interactionMove);window.addEventListener('pointerup',interactionEnd,{once:true});}
    function interactionMove(e){const i=state.interaction;if(!i)return;if(i.type==='pan'){activeGraph().viewport.x=i.x+(e.clientX-i.startX);activeGraph().viewport.y=i.y+(e.clientY-i.startY);applyViewport();renderEdges();return;}if(i.type==='move'){const z=activeGraph().viewport.zoom,dx=(e.clientX-i.startX)/z,dy=(e.clientY-i.startY)/z;if(Math.abs(dx)+Math.abs(dy)>2)i.moved=true;for(const [id,s] of Object.entries(i.starts)){const n=nodeById(id);if(n){n.x=s.x+dx;n.y=s.y+dy;const el=nodesLayer.querySelector(`[data-node-id="${cssEscape(id)}"]`);if(el)el.style.transform=`translate(${n.x}px,${n.y}px)`;}}renderEdges();return;}if(i.type==='box'){const left=Math.min(i.startX,e.clientX),top=Math.min(i.startY,e.clientY),right=Math.max(i.startX,e.clientX),bottom=Math.max(i.startY,e.clientY);Object.assign(i.box.style,{left:`${left-viewport.getBoundingClientRect().left}px`,top:`${top-viewport.getBoundingClientRect().top}px`,width:`${right-left}px`,height:`${bottom-top}px`});const hit=[];nodesLayer.querySelectorAll('.tvi-node').forEach(el=>{const r=el.getBoundingClientRect();if(r.right>=left&&r.left<=right&&r.bottom>=top&&r.top<=bottom)hit.push(el.dataset.nodeId);});state.selectedNodeIds=i.additive?[...new Set([...i.initial,...hit])]:hit;state.selectedEdgeId=null;renderSelectionClasses();renderInspector();renderMeta();renderEdges();}}
    function interactionEnd(){
        const i=state.interaction;if(!i)return;
        window.removeEventListener('pointermove',interactionMove);viewport.classList.remove('is-panning','is-interacting');clearNativeSelection();
        if(i.type==='box')i.box.remove();
        if(i.type==='move'&&i.moved){snapshotFromBeforeMove(i);synchronizeAllLifecyclePriorities(false);changed();state.interaction=null;renderAll();return;}
        state.interaction=null;
        if(i.type==='box'||i.type==='pan')renderAll();
    }
    function snapshotFromBeforeMove(i){pushHistory(clone(state.project));state.changeBase=null;}

    function keyDown(e){if(e.key==='Escape'&&!contextMenu.hidden){hideContextMenu();e.preventDefault();return;}if(e.key==='Escape'&&state.openMenu){e.preventDefault();closeMenus();return;}if(e.key==='Escape'&&htmlSnippetPreviewIsFullscreen()){e.preventDefault();exitHtmlSnippetPreviewFullscreen();return;}if(e.key==='Escape'&&closeTopModal()){e.preventDefault();return;}if(e.code==='Space'&&!isTyping(e.target)){e.preventDefault();state.spacePressed=true;viewport.classList.add('is-pan-ready');}if(isTyping(e.target))return;if((e.ctrlKey||e.metaKey)&&e.key.toLowerCase()==='z'){e.preventDefault();e.shiftKey?redo():undo();}else if((e.ctrlKey||e.metaKey)&&e.key.toLowerCase()==='c'){e.preventDefault();copySelectedNodes();}else if((e.ctrlKey||e.metaKey)&&e.key.toLowerCase()==='v'){e.preventDefault();pasteCopiedNodes();}else if((e.ctrlKey||e.metaKey)&&e.key.toLowerCase()==='d'){e.preventDefault();duplicateSelected();}else if(e.key==='Delete'||e.key==='Backspace'){e.preventDefault();if(state.selectedEdgeId)removeEdge(state.selectedEdgeId);else deleteSelected();}else if(e.key==='Escape'){state.connecting=null;selectNodes([]);state.selectedEdgeId=null;renderAll();}}
    function keyUp(e){if(e.code==='Space'){state.spacePressed=false;viewport.classList.remove('is-pan-ready');}}
    function isTyping(el){return !!el?.closest?.('input,textarea,select,[contenteditable="true"]');}

    function handleWheel(e){e.preventDefault();const old=activeGraph().viewport.zoom,newZ=clamp(old*(e.deltaY<0?1.1:.9),.25,2.2);const r=viewport.getBoundingClientRect(),sx=e.clientX-r.left,sy=e.clientY-r.top;const wx=(sx-activeGraph().viewport.x)/old,wy=(sy-activeGraph().viewport.y)/old;activeGraph().viewport.zoom=newZ;activeGraph().viewport.x=sx-wx*newZ;activeGraph().viewport.y=sy-wy*newZ;applyViewport();renderEdges();renderMeta();}
    function zoomBy(f){activeGraph().viewport.zoom=clamp(activeGraph().viewport.zoom*f,.25,2.2);applyViewport();renderEdges();renderMeta();}
    function resetZoom(){activeGraph().viewport={x:80,y:70,zoom:1};applyViewport();renderEdges();renderMeta();}
    function fitNodeIds(ids, maximumZoom=1.25){
        const graph=activeGraph();const wanted=new Set(ids);const nodes=graph.nodes.filter(node=>wanted.has(node.id)&&!hiddenNodeIds(graph).has(node.id));
        if(!nodes.length){toast('There are no visible nodes to fit.','warn');return;}
        const bounds=nodes.reduce((box,node)=>{const element=nodesLayer.querySelector(`[data-node-id="${cssEscape(node.id)}"]`);const width=element?element.offsetWidth:235;const height=element?element.offsetHeight:100;box.minX=Math.min(box.minX,node.x);box.minY=Math.min(box.minY,node.y);box.maxX=Math.max(box.maxX,node.x+width);box.maxY=Math.max(box.maxY,node.y+height);return box;},{minX:Infinity,minY:Infinity,maxX:-Infinity,maxY:-Infinity});
        const rect=viewport.getBoundingClientRect(),padding=70,width=Math.max(1,bounds.maxX-bounds.minX),height=Math.max(1,bounds.maxY-bounds.minY);
        const zoom=clamp(Math.min((rect.width-padding*2)/width,(rect.height-padding*2)/height,maximumZoom),.25,2.2);
        graph.viewport.zoom=zoom;graph.viewport.x=(rect.width-width*zoom)/2-bounds.minX*zoom;graph.viewport.y=(rect.height-height*zoom)/2-bounds.minY*zoom;
        applyViewport();renderEdges();renderMeta();
    }
    function fitSelectedNodes(){if(!state.selectedNodeIds.length){toast('Select one or more nodes first.','warn');return;}fitNodeIds(state.selectedNodeIds);}
    function fitAllNodes(){const hidden=hiddenNodeIds(activeGraph());fitNodeIds(activeGraph().nodes.filter(node=>!hidden.has(node.id)).map(node=>node.id));}
    function hideAllExceptSelected(){if(!state.selectedNodeIds.length){toast('Select one or more nodes first.','warn');return;}snapshot();const keep=new Set(state.selectedNodeIds);activeGraph().hidden_node_ids=activeGraph().nodes.filter(node=>!keep.has(node.id)).map(node=>node.id);changed();renderAll();fitSelectedNodes();}
    function showAllNodes(){snapshot();activeGraph().hidden_node_ids=[];changed();renderAll();fitAllNodes();}
    function applyViewport(){const v=activeGraph().viewport;world.style.transform=`translate(${v.x}px,${v.y}px) scale(${v.zoom})`;}
    function screenToWorld(x,y){const r=viewport.getBoundingClientRect(),v=activeGraph().viewport;return{x:(x-r.left-v.x)/v.zoom,y:(y-r.top-v.y)/v.zoom};}

    function inspectorInput(e){
        const ownerId=e.target.dataset.inspectorNodeId;
        const ownerNode=ownerId?nodeById(ownerId):selectedNodes()[0];
        const validationProperty=e.target.dataset.validationProperty;
        if(validationProperty&&ownerNode){
            const fields=validationFields(ownerNode,validationProperty);
            const fieldIndex=Number(e.target.dataset.validationFieldIndex);
            const ruleIndex=e.target.dataset.validationRuleIndex===undefined?null:Number(e.target.dataset.validationRuleIndex);
            const target=String(e.target.dataset.validationTarget||'');
            const field=fields[fieldIndex];
            if(!field||!target)return;
            if(e.type==='input'&&(target==='field_name'||target==='rule'))return;
            snapshotDebounced();
            if(ruleIndex===null){
                field[target]=e.target.value;
            }else{
                const rule=field.rules[ruleIndex];
                if(!rule)return;
                if(target==='rule_select'){
                    const nextRule=e.target.value==='__custom__'?'custom_rule':e.target.value;
                    rule.rule=nextRule;
                    rule.param='';rule.param_second='';rule.unique={table:'',column:'',primary_key:'',ignore_value:'',ignore_from_data:'',where:'',whereNot:''};
                }else if(target.startsWith('unique.')){
                    rule.unique ||= {table:'',column:'',primary_key:'',ignore_value:'',ignore_from_data:'',where:'',whereNot:''};
                    rule.unique[target.slice(7)]=e.target.value;
                }else{
                    rule[target]=e.target.value;
                }
            }
            writeValidationFields(ownerNode,validationProperty,fields);
            changed(false);renderNodes();renderMeta();
            if(e.type==='change'&&(target==='rule_select'||target==='field_name'||target==='rule')){renderInspector();if(state.managerEditor)renderManagerEditor();}
            return;
        }
        const columnProperty=e.target.dataset.columnProperty;
        if(columnProperty&&ownerNode){const index=Number(e.target.dataset.columnIndex),field=e.target.dataset.columnField,items=migrationColumns(ownerNode,columnProperty);if(items[index]&&field){snapshotDebounced();items[index][field]=e.target.type==='checkbox'?e.target.checked:e.target.value;if(field==='type'&&String(items[index].type).toUpperCase()==='ENUM'&&!String(items[index].enum_values||'').trim())items[index].enum_values='draft,published';ownerNode.data[columnProperty]=JSON.stringify(items,null,2);changed(false);renderNodes();renderMeta();if(e.type==='change'&&field==='type'){renderInspector();if(state.managerEditor)renderManagerEditor();}}return;}
        const catalogKind=e.target.dataset.catalogKind;
        if(catalogKind&&ownerNode){const property=e.target.dataset.catalogProperty;const index=Number(e.target.dataset.catalogIndex);const field=e.target.dataset.catalogField;const items=parseJson(ownerNode.data[property],[]);if(items[index]&&field){snapshotDebounced();items[index][field]=e.target.value;ownerNode.data[property]=JSON.stringify(items,null,2);changed(false);renderNodes();renderMeta();}return;}
        const dependencyProperty=e.target.dataset.dependencyProperty;
        if(dependencyProperty&&ownerNode){const index=Number(e.target.dataset.dependencyIndex),field=e.target.dataset.dependencyField,items=dependencyItems(ownerNode,dependencyProperty);if(items[index]&&field){snapshotDebounced();items[index][field]=e.target.type==='checkbox'?e.target.checked:e.target.value;writeDependencyItems(ownerNode,dependencyProperty,items);changed(false);renderNodes();renderMeta();}return;}
        const paginationPaletteField=e.target.dataset.paginationPaletteField || e.target.dataset.paginationPalettePicker;
        if(paginationPaletteField&&ownerNode){
            snapshotDebounced();
            ownerNode.data[paginationPaletteField]=e.target.value;
            if(e.target.dataset.paginationPalettePicker){
                const text=inspector.querySelector(`[data-pagination-palette-field="${cssEscape(paginationPaletteField)}"]`);
                if(text)text.value=e.target.value;
            }
            changed(false);renderNodes();updatePaginationPreview(ownerNode.id);renderMeta();
            if(e.type==='change'&&paginationPaletteField==='color_mode')renderInspector();
            return;
        }
        const breakpointProp=e.target.dataset.breakpointProperty;
        if(breakpointProp){const node=ownerNode;if(!node)return;snapshotDebounced();node.data[breakpointProp]={...(node.data[breakpointProp]||{}),[e.target.dataset.breakpoint]:Number(e.target.value)};changed(false);renderNodes();renderMeta();return;}
        const prop=e.target.dataset.property;
        if(prop){const node=ownerNode;if(!node)return;snapshotDebounced();const previous=node.data[prop];node.data[prop]=e.target.type==='checkbox'?e.target.checked:e.target.type==='number'?Number(e.target.value):e.target.value;if(node.type==='charts.builder'&&prop==='dataset_count'){const next=clamp(parseInt(node.data.dataset_count)||1,1,20);node.data.dataset_count=next;if(next<Number(previous||1))pruneChartDatasetEdges(node,next);}pruneDynamicJavaScriptEdges(node,prop,previous);if(node.type==='looks.text_asset'&&prop==='asset_type'&&e.type==='change'){const oldPath=String(node.data.filename||'');if(!oldPath||['assets/css/main.css','assets/js/main.js'].includes(oldPath)){node.data.filename=String(node.data.asset_type).toLowerCase().includes('java')?'assets/js/main.js':'assets/css/main.css';}}if(node.type==='lifecycle.controller'&&['hook_type','data_variable'].includes(prop)){ensureControllerFlowStructure(node,prop==='hook_type');}changed(false);renderNodes();if(node.type==='view.pagination')updatePaginationPreview(node.id);if(e.type==='change'&&['model_node_id','method_name','function_node_id','class_node_id','view_node_id','form_name','param_count','item_count','array_count','dataset_count','condition_count','input_count','property_count','input_names','mode','method','keys','target_type','asset_type','template_id','hook_type','source_type'].includes(prop))renderInspector();renderMeta();return;}
        const member=e.target.dataset.folderMember;if(member){const folder=selectedNodes()[0];if(!folder||folder.type!=='visual.folder')return;snapshot();for(const f of activeGraph().nodes.filter(n=>n.type==='visual.folder'))f.data.member_ids=(f.data.member_ids||[]).filter(id=>id!==member);if(e.target.checked)folder.data.member_ids=[...(folder.data.member_ids||[]),member];changed();renderAll();return;}
        const fileProp=e.target.dataset.fileProperty;if(fileProp&&e.target.files?.[0]){const node=ownerNode;if(!node)return;const file=e.target.files[0];if(node.type==='libraries.package'){uploadLibraryArchive(node,file);return;}const reader=new FileReader();reader.onload=()=>{snapshot();node.data.original_name=file.name;node.data.content_base64=String(reader.result||'');if(node.type==='looks.asset'&&(!node.data.filename||node.data.filename==='assets/css/main.css'))node.data.filename=`assets/${file.name}`;changed();renderAll();};reader.readAsDataURL(file);}
    }
    function inspectorClick(e){
        const editableOption=e.target.closest('[data-editable-select-option]');
        if(editableOption){
            const wrapper=editableOption.closest('[data-editable-select]');
            const input=wrapper?.querySelector('input[data-property]');
            if(input){
                input.value=String(editableOption.dataset.editableSelectOption||'');
                input.dispatchEvent(new Event('change',{bubbles:true}));
                setEditableSelectOpen(wrapper,false);
                input.focus();
            }
            return;
        }
        const editableToggle=e.target.closest('[data-editable-select-toggle]');
        if(editableToggle){
            const wrapper=editableToggle.closest('[data-editable-select]');
            const menu=wrapper?.querySelector('[data-editable-select-menu]');
            setEditableSelectOpen(wrapper,Boolean(menu?.hidden));
            return;
        }
        const a=e.target.closest('[data-inspector-action]');if(!a)return;const actionNode=a.dataset.inspectorNodeId?nodeById(a.dataset.inspectorNodeId):selectedNodes()[0];if(a.dataset.inspectorAction==='export-pagination-colors'){exportPaginationColors(actionNode);return;}if(a.dataset.inspectorAction==='import-pagination-colors'){importPaginationColors(actionNode);return;}if(a.dataset.inspectorAction==='open-method-browser'){openMethodBrowser(actionNode?.id);return;}if(a.dataset.inspectorAction==='open-manager-editor'){openManagerEditor(a.dataset.managerKind,a.dataset.inspectorNodeId,a.dataset.managerProperty);return;}if(a.dataset.inspectorAction==='open-javascript-flow'){const n=actionNode;if(!n)return;snapshot();const id=createJavaScriptGraph(n,true);changed();openGraph(id);return;}const validationAction=a.dataset.inspectorAction;if(['add-validation-field','remove-validation-field','add-validation-rule','remove-validation-rule'].includes(validationAction)){const node=actionNode;if(!node)return;const property=a.dataset.validationProperty||'rules_json';const fields=validationFields(node,property);const fieldIndex=Number(a.dataset.validationFieldIndex);const ruleIndex=Number(a.dataset.validationRuleIndex);snapshot();if(validationAction==='add-validation-field'){const used=new Set(fields.map(field=>String(field.field_name||'')));let name='field_name',suffix=2;while(used.has(name))name=`field_name_${suffix++}`;fields.push({field_name:name,display_name:'',rules:[validationRuleFromRaw('required')]});}else if(validationAction==='remove-validation-field')fields.splice(fieldIndex,1);else if(validationAction==='add-validation-rule'&&fields[fieldIndex])fields[fieldIndex].rules.push(validationRuleFromRaw('required'));else if(validationAction==='remove-validation-rule'&&fields[fieldIndex])fields[fieldIndex].rules.splice(ruleIndex,1);writeValidationFields(node,property,fields);changed();renderAll();return;}const dependencyAction=a.dataset.inspectorAction;if(['add-dependency','remove-dependency'].includes(dependencyAction)){const node=selectedNodes()[0];if(!node)return;const property=a.dataset.dependencyProperty||'dependencies';const items=dependencyItems(node,property);snapshot();if(dependencyAction==='add-dependency')items.push({id:'plugin-id',name:'Plugin Name',version:'1.0.0',required:false});else items.splice(Number(a.dataset.dependencyIndex),1);writeDependencyItems(node,property,items);changed();renderAll();return;}const migrationActionName=a.dataset.inspectorAction;if(['add-migration-column','copy-migration-column','remove-migration-column'].includes(migrationActionName)){const node=actionNode;if(!node)return;const property=a.dataset.columnProperty||'columns_json';const items=migrationColumns(node,property);snapshot();if(migrationActionName==='add-migration-column')items.push({name:'new_column',type:'VARCHAR',length:'255',enum_values:'',unsigned:false,nullable:false,auto_increment:false,default:'',extra:''});else if(migrationActionName==='copy-migration-column'){const index=Number(a.dataset.columnIndex);const source=items[index];if(source){const copy=clone(source);const base=String(source.name||'column').replace(/_copy(?:_\d+)?$/,'');const used=new Set(items.map(item=>String(item.name||'')));let name=`${base}_copy`,suffix=2;while(used.has(name))name=`${base}_copy_${suffix++}`;copy.name=name;items.splice(index+1,0,copy);}}else items.splice(Number(a.dataset.columnIndex),1);node.data[property]=JSON.stringify(items,null,2);changed();renderAll();return;}const catalogAction=a.dataset.inspectorAction;if(['add-catalog-item','copy-catalog-item','remove-catalog-item'].includes(catalogAction)){const node=actionNode;if(!node)return;const property=a.dataset.catalogProperty;const kind=a.dataset.catalogKind;const items=parseJson(node.data[property],[]);snapshot();if(catalogAction==='add-catalog-item')items.push(kind==='permission'?{name:'New Permission',slug:'new-permission',group:'General',description:''}:{name:'New Role',slug:'new-role',description:''});else if(catalogAction==='copy-catalog-item'){const index=Number(a.dataset.catalogIndex);const source=items[index];if(source){const copy=clone(source);const baseName=String(source.name||'Permission').replace(/ Copy(?: \d+)?$/,'');const usedNames=new Set(items.map(item=>String(item.name||'')));let name=`${baseName} Copy`,nameSuffix=2;while(usedNames.has(name))name=`${baseName} Copy ${nameSuffix++}`;copy.name=name;const baseSlug=String(source.slug||'permission').replace(/-copy(?:-\d+)?$/,'');const usedSlugs=new Set(items.map(item=>String(item.slug||'')));let slug=`${baseSlug}-copy`,slugSuffix=2;while(usedSlugs.has(slug))slug=`${baseSlug}-copy-${slugSuffix++}`;copy.slug=slug;items.splice(index+1,0,copy);}}else items.splice(Number(a.dataset.catalogIndex),1);node.data[property]=JSON.stringify(items,null,2);changed();renderAll();return;}const actions={'delete-edge':()=>removeEdge(state.selectedEdgeId),'delete-node':deleteSelected,duplicate:duplicateSelected,'open-flow':()=>{const n=selectedNodes()[0];if(n?.data.graph_id)openGraph(n.data.graph_id);},'clear-folder':()=>{const n=selectedNodes()[0];if(n){snapshot();n.data.member_ids=[];changed();renderAll();}},'open-code-editor':()=>openCodeEditor(a.dataset.editorProperty),'open-html-designer':()=>openHtmlDesigner(selectedNodes()[0]?.id),'open-form-theme':()=>openFormTheme(selectedNodes()[0]?.id),'manage-methods':()=>openMethods(a.dataset.methodsProperty),'insert-component-marker':()=>{const n=selectedNodes()[0];if(!n||n.type!=='lifecycle.view')return;const name=String(a.dataset.componentName||'').trim();if(!name)return;insertInspectorMarker(n,'content',`<tvi-component name="${name}" />`);toast(`Component marker “${name}” inserted.`,'success');},'insert-asset-marker':()=>{const n=selectedNodes()[0];if(!n||n.type!=='lifecycle.view')return;const name=String(a.dataset.assetName||'').trim();if(!name)return;insertInspectorMarker(n,'content',`<tvi-asset name="${name}" />`);toast(`Asset marker “${name}” inserted.`,'success');},'apply-pagination-template':()=>{const n=selectedNodes()[0];if(!n||n.type!=='view.pagination')return;snapshot();applyPaginationTemplate(n,n.data.template_id);changed();renderAll();toast('Pagination design defaults applied.','success');},'add-chart-dataset':()=>adjustChartDatasetCount(1),'remove-chart-dataset':()=>adjustChartDatasetCount(-1),'save-view-tree-preset':saveSelectedViewTreePreset,'move-view-earlier':()=>moveViewNode(-1),'move-view-later':()=>moveViewNode(1),'move-hook-earlier':()=>moveLifecycleNode(a.dataset.routeId,-1),'move-hook-later':()=>moveLifecycleNode(a.dataset.routeId,1)};actions[a.dataset.inspectorAction]?.();}


    function openManagerEditor(kind,nodeId,property){
        const node=nodeById(nodeId);
        if(!node)return;
        state.managerEditor={kind:String(kind||''),nodeId:node.id,property:String(property||'')};
        renderManagerEditor();
        managerEditorModal.hidden=false;
        document.body.classList.add('tvi-modal-open');
    }
    function closeManagerEditor(){
        if(managerEditorModal)managerEditorModal.hidden=true;
        state.managerEditor=null;
        document.body.classList.remove('tvi-modal-open');
        renderInspector();
    }
    function renderManagerEditor(){
        const editor=state.managerEditor;
        if(!editor||!managerEditorBody)return;
        const node=nodeById(editor.nodeId);
        const def=defFor(node);
        const property=def?.properties?.find(item=>item.name===editor.property);
        if(!node||!property){closeManagerEditor();return;}
        if(editor.kind==='migration-columns'){
            managerEditorTitle.textContent=`${nodeTitle(node)} · Columns`;
            managerEditorSummary.textContent='Edit a large table definition with the same controls used by the Inspector.';
            managerEditorBody.className='tvi-manager-editor-body is-migration-columns';
            managerEditorBody.innerHTML=migrationColumnsControl(node,property);
        }else{
            managerEditorTitle.textContent=`${nodeTitle(node)} · Validation Rules`;
            managerEditorSummary.textContent='Manage fields, parameters, unique constraints, and custom messages with more workspace.';
            managerEditorBody.className='tvi-manager-editor-body is-validation-rules';
            managerEditorBody.innerHTML=validationRulesControl(node,property);
        }
    }

    function startPanJoystick(e){
        if(e.button!==0)return;
        e.preventDefault();
        const rect=panJoystick.getBoundingClientRect();
        state.panJoystick={
            pointerId:e.pointerId,
            centerX:rect.left+rect.width/2,
            centerY:rect.top+rect.height/2,
            startX:e.clientX,
            startY:e.clientY,
            lastX:e.clientX,
            lastY:e.clientY
        };
        panJoystick.setPointerCapture?.(e.pointerId);
        panJoystick.classList.add('is-active');
        window.addEventListener('pointermove',movePanJoystick);
        window.addEventListener('pointerup',stopPanJoystick,{once:true});
        window.addEventListener('pointercancel',stopPanJoystick,{once:true});
    }
    function movePanJoystick(e){
        const joystick=state.panJoystick;if(!joystick||e.pointerId!==joystick.pointerId)return;
        const ratio=1;
        const moveX=(e.clientX-joystick.lastX)*ratio;
        const moveY=(e.clientY-joystick.lastY)*ratio;
        joystick.lastX=e.clientX;
        joystick.lastY=e.clientY;
        if(moveX||moveY){
            activeGraph().viewport.x+=moveX;
            activeGraph().viewport.y+=moveY;
            applyViewport();
            renderEdges();
        }
        const max=12;
        const visualX=clamp(e.clientX-joystick.startX,-max,max);
        const visualY=clamp(e.clientY-joystick.startY,-max,max);
        panJoystick.style.setProperty('--pan-x',`${visualX}px`);
        panJoystick.style.setProperty('--pan-y',`${visualY}px`);
    }
    function stopPanJoystick(e){
        const joystick=state.panJoystick;if(!joystick)return;
        if(e&&e.pointerId!==undefined&&e.pointerId!==joystick.pointerId)return;
        window.removeEventListener('pointermove',movePanJoystick);
        window.removeEventListener('pointerup',stopPanJoystick);
        window.removeEventListener('pointercancel',stopPanJoystick);
        panJoystick.classList.remove('is-active');
        panJoystick.style.removeProperty('--pan-x');
        panJoystick.style.removeProperty('--pan-y');
        state.panJoystick=null;
        renderMeta();
    }
    function panJoystickKeyDown(e){
        const amount=e.shiftKey?100:40;
        const moves={ArrowLeft:[-amount,0],ArrowRight:[amount,0],ArrowUp:[0,-amount],ArrowDown:[0,amount]};
        const move=moves[e.key];if(!move)return;
        e.preventDefault();activeGraph().viewport.x+=move[0];activeGraph().viewport.y+=move[1];applyViewport();renderEdges();renderMeta();
    }


    async function uploadLibraryArchive(node, file) {
        if (!String(file.name || '').toLowerCase().endsWith('.zip')) {
            toast('Library packages must be ZIP archives.', 'fail');
            return;
        }
        const form = new FormData();
        form.append('library_archive', file);
        setStatus(`Uploading ${file.name}…`);
        try {
            const response = await fetch(`${cfg.apiBase}/library-upload`, { method: 'POST', body: form });
            const data = await response.json();
            if (!response.ok || !data.ok) {
                showIssues(data);
                return;
            }
            snapshot();
            node.data.archive_id = String(data.archive_id || '');
            node.data.original_name = String(data.original_name || file.name);
            node.data.archive_size = Number(data.size || file.size || 0);
            node.data.content_base64 = '';
            if (!String(node.data.title || '').trim() || node.data.title === 'PHP Library') {
                node.data.title = file.name.replace(/\.zip$/i, '');
            }
            changed();
            renderAll();
            setStatus('Library package uploaded');
            toast(`${file.name} stored for this project.`, 'success');
        } catch (error) {
            setStatus('Library upload failed');
            toast(error.message || 'Library upload failed.', 'fail');
        }
    }


    function setupCodeEditors() {
        if (!window.CodeMirror) return;
        state.previewEditor = window.CodeMirror.fromTextArea(previewCode, {
            lineNumbers: true,
            readOnly: true,
            mode: 'text/plain',
            lineWrapping: state.codeWordWrap,
            cursorBlinkRate: -1,
            viewportMargin: 30,
            theme: state.codeTheme
        });
        state.previewEditor.setSize('100%', '100%');
        state.fullCodeEditor = window.CodeMirror.fromTextArea(codeModalTextarea, {
            lineNumbers: true,
            mode: 'application/x-httpd-php',
            lineWrapping: state.codeWordWrap,
            matchBrackets: true,
            autoCloseBrackets: true,
            styleActiveLine: true,
            indentUnit: 4,
            tabSize: 4,
            indentWithTabs: false,
            theme: state.codeTheme,
            extraKeys: {
                'Tab': cm => cm.somethingSelected() ? cm.indentSelection('add') : cm.replaceSelection('    ', 'end'),
                'Shift-Tab': cm => cm.indentSelection('subtract'),
                'Ctrl-D': duplicateEditorLines,
                'Cmd-D': duplicateEditorLines,
                'Ctrl-Alt-Up': cm => addEditorCursor(cm, -1),
                'Ctrl-Alt-Down': cm => addEditorCursor(cm, 1),
                'Cmd-Alt-Up': cm => addEditorCursor(cm, -1),
                'Cmd-Alt-Down': cm => addEditorCursor(cm, 1),
                'Ctrl-Shift-F': selectNextEditorOccurrence,
                'Cmd-Shift-F': selectNextEditorOccurrence
            }
        });
        state.fullCodeEditor.setSize('100%', '100%');
        state.fullCodeEditor.getWrapperElement().addEventListener('mousedown', event => {
            if (!event.altKey || event.button !== 0) return;
            event.preventDefault(); event.stopPropagation();
            const position = state.fullCodeEditor.coordsChar({ left: event.clientX, top: event.clientY }, 'window');
            state.fullCodeEditor.getDoc().addSelection(position);
            state.fullCodeEditor.focus();
        }, true);
    }

    function duplicateEditorLines(cm) {
        const blocks = [], seen = new Set();
        for (const range of cm.listSelections()) {
            const from = Math.min(range.anchor.line, range.head.line);
            let to = Math.max(range.anchor.line, range.head.line);
            if (range.head.ch === 0 && to > from) to--;
            const key = `${from}:${to}`;
            if (!seen.has(key)) { seen.add(key); blocks.push({ from, to }); }
        }
        blocks.sort((a, b) => b.from - a.from);
        cm.operation(() => {
            for (const block of blocks) {
                const lines = [];
                for (let line = block.from; line <= block.to; line++) lines.push(cm.getLine(line));
                const endLine = block.to, endCh = cm.getLine(endLine).length;
                cm.replaceRange('\n' + lines.join('\n'), { line: endLine, ch: endCh });
            }
        });
    }

    function addEditorCursor(cm, delta) {
        const doc = cm.getDoc(), ranges = doc.listSelections(), additions = [];
        for (const range of ranges) {
            const line = range.head.line + delta;
            if (line < doc.firstLine() || line > doc.lastLine()) continue;
            additions.push({ anchor: { line, ch: Math.min(range.head.ch, doc.getLine(line).length) } });
        }
        if (additions.length) doc.setSelections([...ranges, ...additions]);
    }

    function compareEditorPositions(a, b) {
        return a.line === b.line ? a.ch - b.ch : a.line - b.line;
    }

    function normalizeEditorRange(range) {
        return compareEditorPositions(range.anchor, range.head) <= 0
            ? { from: range.anchor, to: range.head }
            : { from: range.head, to: range.anchor };
    }

    function selectNextEditorOccurrence(cm) {
        const doc = cm.getDoc();
        const primaryFrom = doc.getCursor('from');
        const primaryTo = doc.getCursor('to');
        let needle = doc.getRange(primaryFrom, primaryTo);

        if (!needle) {
            const word = cm.findWordAt(doc.getCursor());
            const from = compareEditorPositions(word.anchor, word.head) <= 0 ? word.anchor : word.head;
            const to = compareEditorPositions(word.anchor, word.head) <= 0 ? word.head : word.anchor;
            needle = doc.getRange(from, to);
            if (!needle || /^\s+$/.test(needle)) {
                toast('Select some text before adding its next occurrence.', 'warn');
                return;
            }
            doc.setSelection(from, to);
            cm.scrollIntoView({ from, to }, 80);
            cm.focus();
            return;
        }

        const source = doc.getValue();
        const selections = doc.listSelections();
        const occupied = selections.map(range => {
            const normalized = normalizeEditorRange(range);
            return {
                start: doc.indexFromPos(normalized.from),
                end: doc.indexFromPos(normalized.to)
            };
        });
        const matching = selections.map(range => {
            const normalized = normalizeEditorRange(range);
            return {
                text: doc.getRange(normalized.from, normalized.to),
                end: doc.indexFromPos(normalized.to)
            };
        }).filter(selection => selection.text === needle);
        const primaryEnd = doc.indexFromPos(primaryTo);
        const searchStart = matching.length
            ? Math.max(...matching.map(selection => selection.end))
            : primaryEnd;
        const overlapsSelection = (start, end) => occupied.some(range => start < range.end && end > range.start);
        const findAvailable = (from, until) => {
            let index = source.indexOf(needle, from);
            while (index !== -1 && index < until) {
                const end = index + needle.length;
                if (!overlapsSelection(index, end)) return index;
                index = source.indexOf(needle, index + 1);
            }
            return -1;
        };

        let nextIndex = findAvailable(searchStart, source.length);
        if (nextIndex === -1 && searchStart > 0) nextIndex = findAvailable(0, searchStart);
        if (nextIndex === -1) {
            toast('All occurrences are already selected.', 'warn');
            return;
        }

        const from = doc.posFromIndex(nextIndex);
        const to = doc.posFromIndex(nextIndex + needle.length);
        doc.addSelection(from, to);
        cm.scrollIntoView({ from, to }, 100);
        cm.focus();
    }

    function codeEditorMode(node, property) {
        const filename = String(node?.data?.filename || node?.data?.path || '').toLowerCase();
        const ext = filename.includes('.') ? filename.split('.').pop() : '';
        if (node?.type === 'looks.text_asset') {
            return String(node.data.asset_type || 'CSS').toLowerCase().includes('java')
                ? 'javascript'
                : 'css';
        }
        if (node?.type === 'lifecycle.view' || String(node?.type || '').startsWith('view.') || ['php','phtml'].includes(ext)) return 'application/x-httpd-php';
        if (ext === 'css') return 'css';
        if (ext === 'js') return 'javascript';
        if (ext === 'json') return { name: 'javascript', json: true };
        if (['html','htm','xml','svg'].includes(ext)) return 'htmlmixed';
        if (['md','markdown'].includes(ext)) return 'markdown';
        if (String(property).includes('content') || String(property).includes('body') || String(node?.type || '').startsWith('custom.')) return 'application/x-httpd-php';
        return 'text/plain';
    }

    function openCodeEditor(property) {
        const node = selectedNodes()[0]; if (!node || !property) return;
        state.codeEditor = { graphId: state.activeGraphId, nodeId: node.id, property, readonly: false };
        if (codeModalSave) codeModalSave.disabled = false;
        codeModalTitle.textContent = `${nodeTitle(node)} · ${property}`;
        const value = String(node.data[property] ?? '');
        codeModalTextarea.value = value;
        if (state.fullCodeEditor) {
            state.fullCodeEditor.setOption('mode', codeEditorMode(node, property));
            state.fullCodeEditor.setOption('readOnly', false);
            state.fullCodeEditor.setValue(value);
            state.fullCodeEditor.clearHistory();
        }
        codeModal.hidden = false;
        requestAnimationFrame(() => {
            if (state.fullCodeEditor) { state.fullCodeEditor.refresh(); state.fullCodeEditor.focus(); state.fullCodeEditor.setCursor({ line: 0, ch: 0 }); }
            else { codeModalTextarea.focus(); codeModalTextarea.setSelectionRange(0, 0); }
        });
    }
    function openMarketplaceCodeEditor(targetId, mode = 'text/plain', title = 'Asset source') {
        const target = document.getElementById(targetId);
        if (!target) return;
        const readonly = !state.marketplaceStudio?.draft?.editable;
        state.codeEditor = { kind: 'marketplace', targetId, mode, readonly };
        if (codeModalSave) codeModalSave.disabled = readonly;
        target.disabled = false;
        target.readOnly = readonly;
        codeModal.classList.add('tvi-modal--nested-editor');
        codeModal.style.zIndex = '100120';
        if (codeModal.parentElement) codeModal.parentElement.appendChild(codeModal);
        codeModalTitle.textContent = `Marketplace Asset Studio · ${title}`;
        const value = String(target.value || '');
        codeModalTextarea.value = value;
        if (state.fullCodeEditor) {
            state.fullCodeEditor.setOption('mode', mode);
            state.fullCodeEditor.setOption('readOnly', readonly ? 'nocursor' : false);
            state.fullCodeEditor.setValue(value);
            state.fullCodeEditor.clearHistory();
        }
        codeModal.hidden = false;
        requestAnimationFrame(() => {
            if (state.fullCodeEditor) { state.fullCodeEditor.refresh(); state.fullCodeEditor.focus(); state.fullCodeEditor.setCursor({ line: 0, ch: 0 }); }
            else { codeModalTextarea.focus(); codeModalTextarea.setSelectionRange(0, 0); }
        });
    }

    function saveCodeEditor() {
        const ctx = state.codeEditor; if (!ctx) return closeCodeEditor();
        if (ctx.readonly) return closeCodeEditor();
        const value = state.fullCodeEditor ? state.fullCodeEditor.getValue() : codeModalTextarea.value;
        if (ctx.kind === 'marketplace') {
            const target = document.getElementById(ctx.targetId);
            if (target) {
                target.value = value;
                target.dispatchEvent(new Event('input', { bubbles: true }));
            }
            closeCodeEditor();
            return;
        }
        const node = nodeById(ctx.nodeId, ctx.graphId); if (!node) return closeCodeEditor();
        snapshot(); node.data[ctx.property] = value; changed(); closeCodeEditor(); renderAll();
    }
    function closeCodeEditor(){codeModal.hidden=true;codeModal.style.zIndex='';codeModal.classList.remove('tvi-modal--nested-editor');if(codeModalSave)codeModalSave.disabled=false;if(state.fullCodeEditor)state.fullCodeEditor.setOption('readOnly',false);state.codeEditor=null;}
    function codeEditorKeyDown(e){if(e.key==='Tab'){e.preventDefault();const el=e.target,start=el.selectionStart,end=el.selectionEnd;el.value=el.value.slice(0,start)+'    '+el.value.slice(end);el.selectionStart=el.selectionEnd=start+4;}}

    function openMethods(property='methods_json') {
        const node = selectedNodes()[0];
        if (!node || !['models.model','custom.class_definition'].includes(node.type)) return;
        const mode = node.type === 'custom.class_definition' ? 'class' : 'model';
        state.methodEditor = { graphId: state.activeGraphId, nodeId: node.id, property, mode, methods: normalizeMethods(parseJson(node.data[property], [])), editingIndex: -1 };
        methodsTitle.textContent = mode === 'class' ? `${nodeTitle(node)} · Class Methods` : `${nodeTitle(node)} · Model Methods`;
        methodVisibilityField.hidden = mode !== 'class';
        if(addModelDefaultsButton)addModelDefaultsButton.hidden = mode !== 'model';
        methodStaticField.hidden = mode !== 'class';
        methodImplementation.value = mode === 'class' ? 'flow' : 'body';
        updateMethodImplementationUi();
        methodsModal.hidden = false;
        renderMethodsModal();
        newMethod();
    }
    function queryBuilderDefaultMethods() {
        return [
            {name:'all',params:[],return_type:'array',implementation:'body',help:'Returns every row from this model table.\n\n```php\n$rows = $model->all();\n```',body:'return $this->get();'},
            {name:'findById',params:[{name:'id',type:'int|string'}],return_type:'?object',implementation:'body',help:'Finds one row by its `id` column and returns the row object or null.',body:"return $this->where('id', $id)->first();"},
            {name:'findBy',params:[{name:'column',type:'string'},{name:'value',type:'mixed'}],return_type:'?object',implementation:'body',help:'Finds the first row matching a column and value.',body:'return $this->where($column, $value)->first();'},
            {name:'createRecord',params:[{name:'data',type:'array'}],return_type:'mixed',implementation:'body',help:'Filters insert data through `$fillableInsert`, creates a row, and returns its insert ID.',body:'$data = $this->filterInsertData($data);\n$this->insert($data);\nreturn $this->getLastInsertId();'},
            {name:'updateById',params:[{name:'id',type:'int|string'},{name:'data',type:'array'}],return_type:'int',implementation:'body',help:'Filters update data through `$fillableUpdate` and updates one row by ID.',body:"$data = $this->filterUpdateData($data);\nreturn $this->where('id', $id)->update($data);"},
            {name:'deleteById',params:[{name:'id',type:'int|string'}],return_type:'int',implementation:'body',help:'Deletes one row by its `id` column and returns the affected-row count.',body:"return $this->where('id', $id)->delete();"},
            {name:'countAll',params:[],return_type:'int',implementation:'body',help:'Counts all rows in the model table.',body:'return $this->count();'}
        ];
    }
    function addModelDefaults(){const ctx=state.methodEditor;if(!ctx||ctx.mode!=='model')return;const existing=new Set(ctx.methods.map(method=>method.name));const additions=queryBuilderDefaultMethods().filter(method=>!existing.has(method.name));if(!additions.length){toast('All Query Builder starter methods are already present.','warn');return;}snapshot();ctx.methods.push(...additions);const node=nodeById(ctx.nodeId,ctx.graphId);node.data[ctx.property]=JSON.stringify(ctx.methods,null,2);changed();renderMethodsModal();renderNodes();renderInspector();toast(`${additions.length} Query Builder methods added.`,'success');}

    function closeMethods(){methodsModal.hidden=true;state.methodEditor=null;}
    function renderMethodsModal(){
        const ctx=state.methodEditor;if(!ctx)return;
        methodsSummary.textContent=`${ctx.methods.length} ${ctx.mode === 'class' ? 'class' : 'model'} method${ctx.methods.length===1?'':'s'}`;
        methodsList.innerHTML=ctx.methods.map((m,i)=>`<article class="tvi-method-row ${ctx.editingIndex===i?'is-active':''}"><div><strong>${esc(m.visibility && ctx.mode === 'class' ? m.visibility + ' ' : '')}${esc(methodSignature(m))}</strong><small>${(m.implementation || (ctx.mode === 'class' ? 'flow' : 'body')) === 'flow' ? 'Execution graph' : 'PHP body'}${m.chainable ? ' · Chainable' : ''}</small></div><div>${(m.implementation || (ctx.mode === 'class' ? 'flow' : 'body')) === 'flow' ? `<button type="button" data-method-action="open-flow" data-method-index="${i}">Flow</button>` : ''}<button type="button" data-method-action="edit" data-method-index="${i}">Edit</button><button type="button" class="tvi-danger" data-method-action="delete" data-method-index="${i}">Delete</button></div></article>`).join('')||'<p class="tvi-muted">No methods yet. Create the first one.</p>';
        const flowButton = methodFlowField.querySelector('[data-action="open-method-flow"]');
        if (flowButton) flowButton.textContent = ctx.editingIndex >= 0 ? 'Open Method Flow' : 'Save & Open Method Flow';
    }
    function newMethod(){
        if(!state.methodEditor)return;
        state.methodEditor.editingIndex=-1;
        methodName.value='';methodReturnType.value='';methodVisibility.value='public';methodStatic.checked=false;methodChainable.checked=false;methodBody.value='';methodHelp.value='';methodImplementation.value=state.methodEditor.mode==='class'?'flow':'body';
        renderMethodParamRows([{name:'',type:'',default:''}]);
        updateMethodImplementationUi();methodName.focus();renderMethodsModal();
    }
    function renderMethodParamRows(params){
        const rows=(params&&params.length?params:[{name:'',type:'',default:''}]);
        methodParamsList.innerHTML=rows.map((p,i)=>`<div class="tvi-method-param-row" data-method-param-row><input type="text" data-method-param-name value="${attr(p.name||'')}" placeholder="parameter"><input type="text" data-method-param-type value="${attr(p.type||'')}" placeholder="type (optional)"><input type="text" data-method-param-default value="${attr(p.default||'')}" placeholder="default PHP value (optional)"><button type="button" class="tvi-danger" data-method-param-remove="${i}" title="Remove parameter">×</button></div>`).join('');
    }
    function readMethodParams(){
        return [...methodParamsList.querySelectorAll('[data-method-param-row]')].map(row=>({name:sanitize(row.querySelector('[data-method-param-name]')?.value||''),type:String(row.querySelector('[data-method-param-type]')?.value||'').trim(),default:String(row.querySelector('[data-method-param-default]')?.value||'').trim()})).filter(p=>p.name);
    }
    function addMethodParam(){const params=readMethodParams();params.push({name:'',type:'',default:''});renderMethodParamRows(params);methodParamsList.querySelector('[data-method-param-row]:last-child [data-method-param-name]')?.focus();}
    function saveMethod(showToast=true){
        const ctx=state.methodEditor;if(!ctx)return false;
        const name=sanitizeMethodName(methodName.value);if(!name){toast('Enter a valid method name.','fail');return false;}
        const params=readMethodParams();
        const existing=ctx.editingIndex>=0?ctx.methods[ctx.editingIndex]:null;
        const node=nodeById(ctx.nodeId,ctx.graphId);if(!node)return false;
        snapshot();
        const implementation=methodImplementation.value==='flow'?'flow':'body';
        const method={name,params,return_type:String(methodReturnType.value||'').trim(),implementation,help:String(methodHelp.value||'').trim(),chainable:Boolean(methodChainable.checked)};
        if(ctx.mode==='class'){
            method.visibility=['public','protected','private'].includes(methodVisibility.value)?methodVisibility.value:'public';
            method.static=Boolean(methodStatic.checked);
        }
        method.body=methodBody.value.trim();
        if(implementation==='flow'){method.graph_id=existing?.graph_id||method.graph_id||uid('graph');ensureMethodGraph(nodeById(ctx.nodeId,ctx.graphId),method);}
        if(ctx.editingIndex>=0)ctx.methods[ctx.editingIndex]=method;else{ctx.methods.push(method);ctx.editingIndex=ctx.methods.length-1;}
        node.data[ctx.property]=JSON.stringify(ctx.methods,null,2);changed();renderNodes();renderInspector();renderMethodsModal();if(showToast)toast(`${ctx.mode==='class'?'Class':'Model'} method saved.`,'success');return true;
    }
    function ensureMethodGraph(node,method){
        if(!node||!method.graph_id)return;
        const id=method.graph_id;
        if(!state.project.graphs[id])state.project.graphs[id]={id,name:`${nodeTitle(node)}::${method.name}`,kind:'flow',owner_node_id:node.id,nodes:[],edges:[],viewport:{x:80,y:80,zoom:1}};
        const graph=state.project.graphs[id];graph.name=`${nodeTitle(node)}::${method.name}`;graph.owner_node_id=node.id;
        if(!graph.nodes.some(n=>n.type==='flow.start'))graph.nodes.push({id:uid('start'),type:'flow.start',x:70,y:100,data:{title:'Start'}});
    }
    function updateMethodImplementationUi(){
        const useFlow=methodImplementation.value==='flow';
        methodBodyField.hidden=useFlow;
        methodFlowField.hidden=!useFlow;
    }
    function openMethodFlow(){
        const ctx=state.methodEditor;if(!ctx)return;
        methodImplementation.value='flow';updateMethodImplementationUi();
        if(!saveMethod(false))return;
        const method=ctx.methods[ctx.editingIndex];if(!method?.graph_id){toast('Unable to create the method flow graph.','fail');return;}
        closeMethods();openGraph(method.graph_id);
    }
    function methodModalClick(e){
        const remove=e.target.closest('[data-method-param-remove]');if(remove){const params=readMethodParams();params.splice(Number(remove.dataset.methodParamRemove),1);renderMethodParamRows(params);return;}
        const b=e.target.closest('[data-method-action]');if(!b||!state.methodEditor)return;const i=Number(b.dataset.methodIndex);if(!Number.isInteger(i)||!state.methodEditor.methods[i])return;
        if(b.dataset.methodAction==='open-flow'){snapshot();const method=state.methodEditor.methods[i];const node=nodeById(state.methodEditor.nodeId,state.methodEditor.graphId);method.implementation='flow';method.graph_id=method.graph_id||uid('graph');ensureMethodGraph(node,method);node.data[state.methodEditor.property]=JSON.stringify(state.methodEditor.methods,null,2);changed();closeMethods();openGraph(method.graph_id);return;}
        if(b.dataset.methodAction==='edit'){
            const m=state.methodEditor.methods[i];state.methodEditor.editingIndex=i;methodName.value=m.name||'';methodReturnType.value=m.return_type||'';methodVisibility.value=m.visibility||'public';methodStatic.checked=Boolean(m.static);methodChainable.checked=Boolean(m.chainable);methodBody.value=m.body||'';methodHelp.value=m.help||m.description||'';methodImplementation.value=m.implementation||(state.methodEditor.mode==='class'?'flow':'body');updateMethodImplementationUi();renderMethodParamRows(normalizeMethodParams(m.params||[]));renderMethodsModal();methodName.focus();
        }else if(b.dataset.methodAction==='delete'){
            if(!confirm(`Delete method ${state.methodEditor.methods[i].name||''}?`))return;
            snapshot();const removed=state.methodEditor.methods.splice(i,1)[0];if(removed?.graph_id)delete state.project.graphs[removed.graph_id];
            const node=nodeById(state.methodEditor.nodeId,state.methodEditor.graphId);node.data[state.methodEditor.property]=JSON.stringify(state.methodEditor.methods,null,2);changed();renderNodes();renderInspector();newMethod();
        }
    }

    function htmlEditorOptions(mode) {
        return {
            lineNumbers: true,
            mode,
            lineWrapping: state.codeWordWrap,
            matchBrackets: true,
            autoCloseBrackets: true,
            styleActiveLine: true,
            indentUnit: 4,
            tabSize: 4,
            indentWithTabs: false,
            theme: state.codeTheme,
            extraKeys: {
                'Tab': cm => cm.somethingSelected() ? cm.indentSelection('add') : cm.replaceSelection('    ', 'end'),
                'Shift-Tab': cm => cm.indentSelection('subtract'),
                'Ctrl-D': duplicateEditorLines,
                'Cmd-D': duplicateEditorLines,
                'Ctrl-Alt-Up': cm => addEditorCursor(cm, -1),
                'Ctrl-Alt-Down': cm => addEditorCursor(cm, 1),
                'Cmd-Alt-Up': cm => addEditorCursor(cm, -1),
                'Cmd-Alt-Down': cm => addEditorCursor(cm, 1),
                'Ctrl-Shift-F': selectNextEditorOccurrence,
                'Cmd-Shift-F': selectNextEditorOccurrence
            }
        };
    }

    function ensureHtmlEditors() {
        if (state.htmlEditors || !window.CodeMirror) return;

        state.htmlEditors = {
            html: window.CodeMirror.fromTextArea(htmlDesignerHtml, htmlEditorOptions('htmlmixed')),
            css: window.CodeMirror.fromTextArea(htmlDesignerCss, htmlEditorOptions('css')),
            js: window.CodeMirror.fromTextArea(htmlDesignerJs, htmlEditorOptions('javascript'))
        };

        Object.values(state.htmlEditors).forEach(editor => {
            editor.setSize('100%', '100%');
            editor.on('change', scheduleHtmlPreview);
            editor.getWrapperElement().addEventListener('mousedown', event => {
                if (!event.altKey || event.button !== 0) return;
                event.preventDefault();
                event.stopPropagation();
                const position = editor.coordsChar({ left: event.clientX, top: event.clientY }, 'window');
                editor.getDoc().addSelection(position);
                editor.focus();
            }, true);
        });
    }

    function openHtmlDesigner(nodeId = null) {
        if (activeGraph()?.kind !== 'view') {
            toast('Open a View Builder before creating an HTML component.', 'warn');
            return;
        }

        let node = nodeId ? nodeById(nodeId) : selectedNodes()[0];
        if (!node || node.type !== 'view.html_component') {
            const rect = viewport.getBoundingClientRect();
            const point = screenToWorld(rect.left + rect.width / 2, rect.top + rect.height / 2);
            addNode('view.html_component', point.x - 110, point.y - 55);
            node = selectedNodes()[0];
        }

        if (!node || node.type !== 'view.html_component') return;

        state.htmlDesigner = {
            nodeId: node.id,
            graphId: state.activeGraphId
        };
        ensureHtmlEditors();
        htmlDesignerTitle.value = String(node.data.title || 'HTML Component');
        htmlDesignerName.value = String(node.data.component_name || 'custom-component');
        if (state.htmlEditors) {
            state.htmlEditors.html.setValue(String(node.data.html_code || ''));
            state.htmlEditors.css.setValue(String(node.data.css_code || ''));
            state.htmlEditors.js.setValue(String(node.data.js_code || ''));
        } else {
            htmlDesignerHtml.value = String(node.data.html_code || '');
            htmlDesignerCss.value = String(node.data.css_code || '');
            htmlDesignerJs.value = String(node.data.js_code || '');
        }
        htmlDesignerDialog?.classList.remove('is-preview-expanded', 'is-panel-expanded');
        htmlDesignerDialog?.classList.add('is-js-collapsed');
        if (htmlDesignerDialog) delete htmlDesignerDialog.dataset.expandedPanel;
        state.htmlPanelPreviousJsCollapsed = true;
        refreshHtmlDesignerComponentOptions();
        syncHtmlPanelButtons();
        syncHtmlJsToggle(true);
        syncHtmlPreviewWidth();
        htmlDesignerModal.hidden = false;
        document.documentElement.classList.add('tvi-html-designer-open');
        requestAnimationFrame(() => {
            Object.values(state.htmlEditors || {}).forEach(editor => editor.refresh());
            updateHtmlPreview();
            state.htmlEditors?.html.focus();
        });
    }

    function htmlDesignerNode() {
        const editor = state.htmlDesigner;
        return editor ? nodeById(editor.nodeId, editor.graphId) : null;
    }

    function htmlDesignerValues() {
        return {
            title: String(htmlDesignerTitle?.value || 'HTML Component').trim() || 'HTML Component',
            component_name: componentSlug(htmlDesignerName?.value || 'custom-component') || 'custom-component',
            html_code: state.htmlEditors?.html.getValue() ?? String(htmlDesignerHtml?.value || ''),
            css_code: state.htmlEditors?.css.getValue() ?? String(htmlDesignerCss?.value || ''),
            js_code: state.htmlEditors?.js.getValue() ?? String(htmlDesignerJs?.value || '')
        };
    }

    function htmlDesignerComponentEntries() {
        const graphId = state.htmlDesigner?.graphId || state.activeGraphId;
        const graph = state.project.graphs?.[graphId];
        if (!graph || graph.kind !== 'view') return [];

        const childIds = new Set((graph.edges || []).map(edge => String(edge.to || '')).filter(Boolean));
        const currentNodeId = String(state.htmlDesigner?.nodeId || '');
        const currentName = componentSlug(htmlDesignerName?.value || '');

        return (graph.nodes || [])
            .filter(node => !childIds.has(String(node.id || '')))
            .map(node => ({
                node,
                name: componentSlug(node.data?.component_name || ''),
                title: String(node.data?.title || node.data?.component_name || 'Component')
            }))
            .filter(entry => entry.name && String(entry.node.id || '') !== currentNodeId && entry.name !== currentName)
            .sort((a, b) => a.title.localeCompare(b.title) || a.name.localeCompare(b.name));
    }

    function refreshHtmlDesignerComponentOptions() {
        if (!htmlDesignerComponent) return;
        const previous = String(htmlDesignerComponent.value || '');
        const entries = htmlDesignerComponentEntries();
        htmlDesignerComponent.innerHTML = entries.length
            ? `<option value="">Choose a component…</option>${entries.map(entry => `<option value="${attr(entry.name)}">${esc(entry.title)} · ${esc(entry.name)}</option>`).join('')}`
            : '<option value="">No other components in this View</option>';
        if (entries.some(entry => entry.name === previous)) htmlDesignerComponent.value = previous;
    }

    function insertHtmlComponentMarker() {
        const name = componentSlug(htmlDesignerComponent?.value || '');
        if (!name) {
            toast('Choose a component to insert.', 'warn');
            return;
        }

        const marker = `<tvi-component name="${name}" />`;
        const editor = state.htmlEditors?.html;
        if (editor) {
            const doc = editor.getDoc();
            doc.replaceSelection(marker, 'end');
            editor.focus();
        } else if (htmlDesignerHtml) {
            const hasCursor = Number.isInteger(htmlDesignerHtml.selectionStart)
                && Number.isInteger(htmlDesignerHtml.selectionEnd);
            const start = hasCursor ? htmlDesignerHtml.selectionStart : htmlDesignerHtml.value.length;
            const end = hasCursor ? htmlDesignerHtml.selectionEnd : start;
            const before = htmlDesignerHtml.value.slice(0, start);
            const after = htmlDesignerHtml.value.slice(end);
            const prefix = hasCursor ? '' : (before.trim() ? '\n\n' : '');
            htmlDesignerHtml.value = before + prefix + marker + after;
            const cursor = before.length + prefix.length + marker.length;
            htmlDesignerHtml.setSelectionRange(cursor, cursor);
            htmlDesignerHtml.focus();
        }

        scheduleHtmlPreview();
        toast(`Component marker “${name}” inserted.`, 'success');
    }

    function htmlDesignerHasChanges() {
        const node = htmlDesignerNode();
        if (!node) return false;
        const values = htmlDesignerValues();
        return ['title', 'component_name', 'html_code', 'css_code', 'js_code']
            .some(key => String(node.data[key] ?? '') !== String(values[key] ?? ''));
    }

    function closeHtmlDesigner(force = false) {
        if (!htmlDesignerModal || htmlDesignerModal.hidden) return;
        if (!force && htmlDesignerHasChanges() && !confirm('Close the HTML Component Designer without saving its latest changes?')) return;
        clearTimeout(state.htmlPreviewTimer);
        closeHtmlSnippets();
        htmlDesignerModal.hidden = true;
        document.documentElement.classList.remove('tvi-html-designer-open');
        htmlDesignerDialog?.classList.remove('is-preview-expanded', 'is-panel-expanded', 'is-js-collapsed');
        if (htmlDesignerDialog) delete htmlDesignerDialog.dataset.expandedPanel;
        state.htmlDesigner = null;
        if (![...document.querySelectorAll('.tvi-modal')].some(modal => !modal.hidden)) {
            document.body.classList.remove('tvi-modal-open');
        }
    }

    function saveHtmlDesigner() {
        const node = htmlDesignerNode();
        if (!node) return;
        const values = htmlDesignerValues();
        snapshot();
        Object.assign(node.data, values);
        htmlDesignerName.value = values.component_name;
        refreshHtmlDesignerComponentOptions();
        changed();
        renderAll();
        toast(`HTML component “${values.component_name}” saved.`, 'success');
    }

    function saveHtmlComponentPreset() {
        const node = htmlDesignerNode();
        if (!node) return;
        saveHtmlDesigner();
        selectNodes([node.id]);
        renderAll();
        saveSelectedViewTreePreset();
    }

    function openHtmlSnippets() {
        if (!htmlSnippetsModal) return;
        const view = viewForGraph(state.htmlDesigner?.graphId || state.activeGraphId);
        if (!view) {
            toast('The snippet library needs an owning View so it can resolve its Look theme.', 'warn');
            return;
        }
        const themeState = viewThemeState(view);
        if (!themeState.look) {
            toast('Connect the View to a Look before editing shared theme colors.', 'warn');
            return;
        }
        state.htmlSnippetPaletteEditor = {
            lookNodeId: themeState.look.id,
            themeId: themeState.theme?.id || 'classic',
            paletteId: themeState.palette.id || 'default',
            paletteSelection: storedPaletteSelection(themeState.look, themeState.theme, themeState.palette, themeState.colors),
            colors: { ...themeState.colors }
        };
        state.htmlSnippetPage = 1;
        state.htmlSnippetCategory = 'All';
        state.htmlSnippetSelectedId = htmlSnippets[0]?.id || null;
        if (htmlSnippetSearch) htmlSnippetSearch.value = '';
        syncHtmlSnippetPaletteState();
        renderHtmlSnippets();
        htmlSnippetsModal.hidden = false;
        document.body.classList.add('tvi-modal-open');
        requestAnimationFrame(() => htmlSnippetSearch?.focus());
    }

    function closeHtmlSnippets() {
        exitHtmlSnippetPreviewFullscreen();
        if (htmlSnippetsModal) htmlSnippetsModal.hidden = true;
        state.htmlSnippetPaletteEditor = null;
        if (![...document.querySelectorAll('.tvi-modal')].some(modal => !modal.hidden)) {
            document.body.classList.remove('tvi-modal-open');
        }
    }

    function syncHtmlSnippetPaletteState() {
        const minimized = Boolean(state.htmlSnippetPaletteMinimized);
        htmlSnippetBrowserLayout?.classList.toggle('is-palette-minimized', minimized);
        htmlSnippetPalettePanel?.classList.toggle('is-minimized', minimized);
        if (!htmlSnippetPaletteToggle) return;
        htmlSnippetPaletteToggle.setAttribute('aria-expanded', minimized ? 'false' : 'true');
        htmlSnippetPaletteToggle.setAttribute('aria-label', minimized ? 'Open color palette' : 'Minimize color palette');
        htmlSnippetPaletteToggle.title = minimized ? 'Open color palette' : 'Minimize color palette';
        const use = htmlSnippetPaletteToggle.querySelector('use');
        if (use) use.setAttribute('href', minimized ? '#tvi-icon-left' : '#tvi-icon-right');
    }

    function toggleHtmlSnippetPalette() {
        state.htmlSnippetPaletteMinimized = !state.htmlSnippetPaletteMinimized;
        localStorage.setItem('tvi_html_snippet_palette_minimized', state.htmlSnippetPaletteMinimized ? '1' : '0');
        syncHtmlSnippetPaletteState();
        requestAnimationFrame(() => {
            if (!state.htmlSnippetPaletteMinimized) htmlSnippetPalette?.focus();
        });
    }

    function htmlSnippetFullscreenElement() {
        return document.fullscreenElement || document.webkitFullscreenElement || null;
    }

    function htmlSnippetPreviewIsFullscreen() {
        return htmlSnippetFullscreenElement() === htmlSnippetPreviewPanel
            || htmlSnippetPreviewPanel?.classList.contains('is-fullscreen-fallback');
    }

    function syncHtmlSnippetPreviewFullscreenState() {
        if (!htmlSnippetPreviewPanel || !htmlSnippetPreviewFullscreen) return;
        const active = htmlSnippetPreviewIsFullscreen();
        htmlSnippetPreviewPanel.classList.toggle('is-native-fullscreen', htmlSnippetFullscreenElement() === htmlSnippetPreviewPanel);
        htmlSnippetPreviewFullscreen.setAttribute('aria-pressed', active ? 'true' : 'false');
        htmlSnippetPreviewFullscreen.setAttribute('aria-label', active ? 'Exit full-screen preview' : 'View preview full screen');
        htmlSnippetPreviewFullscreen.title = active ? 'Exit full-screen preview' : 'View preview full screen';
        const use = htmlSnippetPreviewFullscreen.querySelector('use');
        if (use) use.setAttribute('href', active ? '#tvi-icon-contract' : '#tvi-icon-expand');
    }

    async function toggleHtmlSnippetPreviewFullscreen() {
        if (!htmlSnippetPreviewPanel) return;
        if (htmlSnippetPreviewIsFullscreen()) {
            exitHtmlSnippetPreviewFullscreen();
            return;
        }

        const requestFullscreen = htmlSnippetPreviewPanel.requestFullscreen
            || htmlSnippetPreviewPanel.webkitRequestFullscreen;
        if (requestFullscreen) {
            try {
                await requestFullscreen.call(htmlSnippetPreviewPanel);
                syncHtmlSnippetPreviewFullscreenState();
                return;
            } catch (error) {
                console.warn('Could not enter native full screen. Using the IDE fallback.', error);
            }
        }

        htmlSnippetPreviewPanel.classList.add('is-fullscreen-fallback');
        document.body.classList.add('tvi-snippet-preview-fullscreen-open');
        syncHtmlSnippetPreviewFullscreenState();
    }

    function exitHtmlSnippetPreviewFullscreen() {
        if (!htmlSnippetPreviewPanel) return;
        htmlSnippetPreviewPanel.classList.remove('is-fullscreen-fallback', 'is-native-fullscreen');
        document.body.classList.remove('tvi-snippet-preview-fullscreen-open');

        if (htmlSnippetFullscreenElement() === htmlSnippetPreviewPanel) {
            const exitFullscreen = document.exitFullscreen || document.webkitExitFullscreen;
            if (exitFullscreen) {
                try {
                    const result = exitFullscreen.call(document);
                    if (result?.catch) result.catch(() => {});
                } catch (error) {
                    console.warn('Could not exit native full screen.', error);
                }
            }
        }
        syncHtmlSnippetPreviewFullscreenState();
    }

    function clearHtmlDesigner() {
        const values = htmlDesignerValues();
        const hasCode = [values.html_code, values.css_code, values.js_code].some(value => String(value).trim() !== '');
        if (hasCode && !confirm('Clear all HTML, CSS and JavaScript code from this component?')) return;
        if (state.htmlEditors) {
            state.htmlEditors.html.setValue('');
            state.htmlEditors.css.setValue('');
            state.htmlEditors.js.setValue('');
            state.htmlEditors.html.focus();
        } else {
            if (htmlDesignerHtml) htmlDesignerHtml.value = '';
            if (htmlDesignerCss) htmlDesignerCss.value = '';
            if (htmlDesignerJs) htmlDesignerJs.value = '';
            htmlDesignerHtml?.focus();
        }
        scheduleHtmlPreview();
        toast('HTML, CSS and JavaScript cleared.', 'success');
    }

    function iconMarkup(name) {
        return `<svg class="tvi-icon" aria-hidden="true"><use href="#tvi-icon-${attr(name)}"></use></svg>`;
    }

    function setIconButton(button, icon, label) {
        if (!button) return;
        button.innerHTML = iconMarkup(icon);
        button.title = label;
        button.setAttribute('aria-label', label);
    }

    function syncHtmlJsToggle(collapsed) {
        setIconButton(htmlJsToggle, collapsed ? 'code' : 'contract', collapsed ? 'Expand JavaScript' : 'Minimise JavaScript');
    }

    function htmlPreviewWidthLabel(width = state.htmlPreviewWidth) {
        const labels = {
            auto: 'Fluid',
            1200: 'Desktop · 1200px',
            768: 'Tablet · 768px',
            375: 'Mobile · 375px'
        };
        return labels[String(width)] || labels.auto;
    }

    function syncHtmlPreviewWidth() {
        const allowed = new Set(['auto', '1200', '768', '375']);
        const width = allowed.has(String(state.htmlPreviewWidth))
            ? String(state.htmlPreviewWidth)
            : 'auto';
        state.htmlPreviewWidth = width;
        if (htmlPreviewStage) htmlPreviewStage.dataset.previewWidth = width;
        htmlPreviewWidthButtons.forEach(button => {
            const active = String(button.dataset.previewWidth || '') === width;
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
    }

    function setHtmlPreviewWidth(width) {
        const allowed = new Set(['auto', '1200', '768', '375']);
        state.htmlPreviewWidth = allowed.has(String(width)) ? String(width) : 'auto';
        localStorage.setItem('tvi_html_preview_width', state.htmlPreviewWidth);
        syncHtmlPreviewWidth();
        updateHtmlPreview();
    }

    function syncHtmlPanelButtons() {
        const expandedPanel = String(htmlDesignerDialog?.dataset.expandedPanel || '');
        htmlDesignerDialog?.querySelectorAll('[data-action="toggle-html-panel"]').forEach(button => {
            const panel = String(button.dataset.htmlPanel || '');
            const expanded = expandedPanel === panel;
            const panelName = panel === 'js' ? 'JavaScript editor' : panel === 'preview' ? 'preview' : `${panel.toUpperCase()} editor`;
            setIconButton(button, expanded ? 'contract' : 'expand', expanded ? 'Return to editor layout' : `Expand ${panelName}`);
        });
        if (htmlPreviewExpand) {
            const expanded = expandedPanel === 'preview';
            setIconButton(htmlPreviewExpand, expanded ? 'contract' : 'expand', expanded ? 'Return to editors' : 'Expand preview');
        }
    }

    function toggleHtmlPanel(panel) {
        if (!htmlDesignerDialog || !['html', 'css', 'js', 'preview'].includes(String(panel))) return;
        const current = String(htmlDesignerDialog.dataset.expandedPanel || '');
        const next = current === panel ? '' : String(panel);

        if (!current && next) {
            state.htmlPanelPreviousJsCollapsed = htmlDesignerDialog.classList.contains('is-js-collapsed');
        }

        if (next === 'js') {
            htmlDesignerDialog.classList.remove('is-js-collapsed');
            syncHtmlJsToggle(false);
        }

        if (next) {
            htmlDesignerDialog.dataset.expandedPanel = next;
            htmlDesignerDialog.classList.add('is-panel-expanded');
        } else {
            delete htmlDesignerDialog.dataset.expandedPanel;
            htmlDesignerDialog.classList.remove('is-panel-expanded');
            htmlDesignerDialog.classList.toggle('is-js-collapsed', Boolean(state.htmlPanelPreviousJsCollapsed));
            syncHtmlJsToggle(Boolean(state.htmlPanelPreviousJsCollapsed));
        }

        syncHtmlPanelButtons();
        requestAnimationFrame(() => {
            Object.values(state.htmlEditors || {}).forEach(editor => editor.refresh());
            updateHtmlPreview();
        });
    }

    function toggleHtmlJsPanel() {
        if (!htmlDesignerDialog) return;
        const expandedPanel = String(htmlDesignerDialog.dataset.expandedPanel || '');
        if (expandedPanel === 'js') {
            delete htmlDesignerDialog.dataset.expandedPanel;
            htmlDesignerDialog.classList.remove('is-panel-expanded');
            htmlDesignerDialog.classList.add('is-js-collapsed');
            state.htmlPanelPreviousJsCollapsed = true;
            syncHtmlJsToggle(true);
            syncHtmlPanelButtons();
        } else {
            const collapsed = htmlDesignerDialog.classList.toggle('is-js-collapsed');
            state.htmlPanelPreviousJsCollapsed = collapsed;
            syncHtmlJsToggle(collapsed);
        }
        requestAnimationFrame(() => {
            state.htmlEditors?.js?.refresh();
            updateHtmlPreview();
        });
    }

    function toggleHtmlPreview() {
        toggleHtmlPanel('preview');
    }

    function scheduleHtmlPreview() {
        clearTimeout(state.htmlPreviewTimer);
        if (htmlPreviewStatus) htmlPreviewStatus.textContent = 'Updating…';
        state.htmlPreviewTimer = setTimeout(updateHtmlPreview, 160);
    }

    function previewGraphChildren(graph, nodeId, port = 'children') {
        const edges = (graph?.edges || [])
            .filter(edge => String(edge.from || '') === String(nodeId) && String(edge.from_port || '') === port)
            .sort((a, b) => {
                const nodeA = (graph.nodes || []).find(node => String(node.id) === String(a.to)) || {};
                const nodeB = (graph.nodes || []).find(node => String(node.id) === String(b.to)) || {};
                return (Number(nodeA.y || 0) - Number(nodeB.y || 0)) || (Number(nodeA.x || 0) - Number(nodeB.x || 0));
            });
        return edges
            .map(edge => (graph.nodes || []).find(node => String(node.id) === String(edge.to)))
            .filter(Boolean);
    }

    function previewComponentMap(graph, currentValues) {
        const childIds = new Set((graph?.edges || []).map(edge => String(edge.to || '')).filter(Boolean));
        const map = new Map();
        for (const node of graph?.nodes || []) {
            if (childIds.has(String(node.id || ''))) continue;
            const name = componentSlug(node.data?.component_name || '');
            if (!name) continue;
            map.set(name, node);
        }
        const currentNode = htmlDesignerNode();
        if (currentNode) {
            const name = componentSlug(currentValues.component_name || '');
            if (name) map.set(name, { ...currentNode, data: { ...(currentNode.data || {}), ...currentValues } });
        }
        return map;
    }

    function previewNodeAttributes(data = {}) {
        const classes = String(data.css_class || '').trim();
        const style = String(data.inline_style || '').trim();
        return `${classes ? ` class="${attr(classes)}"` : ''}${style ? ` style="${attr(style)}"` : ''}`;
    }

    function resolvePreviewComponentMarkers(source, componentMap, graph, assets, stack = []) {
        return String(source || '').replace(/<tvi-component\s+name=["']([^"']+)["']\s*\/?\s*>/gi, (match, rawName) => {
            const name = componentSlug(rawName || '');
            if (!name || !componentMap.has(name)) {
                return `<div class="tvi-preview-component-warning">Missing component: ${esc(name || rawName || 'unknown')}</div>`;
            }
            if (stack.includes(name) || stack.length >= 20) {
                return `<div class="tvi-preview-component-warning">Circular component reference: ${esc([...stack, name].join(' → '))}</div>`;
            }
            return renderPreviewComponentNode(componentMap.get(name), componentMap, graph, assets, [...stack, name]);
        });
    }

    function renderPreviewComponentNode(node, componentMap, graph, assets, stack = []) {
        if (!node) return '';
        const data = node.data || {};
        const type = String(node.type || '');
        const name = componentSlug(data.component_name || data.title || node.id || 'component') || 'component';

        if (type === 'view.html_boilerplate') {
            const children = previewGraphChildren(graph, node.id)
                .map(child => renderPreviewComponentNode(child, componentMap, graph, assets, stack))
                .join('\n');
            return `<div class="tvi-preview-document-shell"><div class="tvi-preview-component-placeholder">HTML5 document shell + social meta tags</div>${children}</div>`;
        }

        if (type === 'view.html_component') {
            const css = String(data.css_code || '').trim();
            const js = String(data.js_code || '').trim();
            if (css) assets.css.set(String(node.id || name), css);
            if (js) assets.js.set(String(node.id || name), { name, code: js });
            const html = resolvePreviewComponentMarkers(data.html_code || '', componentMap, graph, assets, stack);
            return `<div class="tvi-html-${attr(name)}" data-tvi-html-component="${attr(name)}">${html}</div>`;
        }

        if (type === 'view.component') {
            const tag = String(data.wrapper_tag || 'div') === 'none' ? '' : String(data.wrapper_tag || 'div').replace(/[^a-z0-9-]/gi, '') || 'div';
            const children = previewGraphChildren(graph, node.id).map(child => renderPreviewComponentNode(child, componentMap, graph, assets, stack)).join('\n');
            return tag ? `<${tag}${previewNodeAttributes(data)}>${children}</${tag}>` : children;
        }

        if (type === 'view.loop') {
            const children = previewGraphChildren(graph, node.id).map(child => renderPreviewComponentNode(child, componentMap, graph, assets, stack)).join('\n');
            const repeated = [1, 2, 3].map(() => children).join('\n');
            return `<div${previewNodeAttributes(data)} data-tvi-preview-loop>${repeated || '<div class="tvi-preview-component-placeholder">Loop items</div>'}</div>`;
        }

        if (['view.container', 'view.grid', 'view.form'].includes(type)) {
            const tag = type === 'view.form' ? 'form' : String(data.wrapper_tag || 'div').replace(/[^a-z0-9-]/gi, '') || 'div';
            const children = previewGraphChildren(graph, node.id).map(child => renderPreviewComponentNode(child, componentMap, graph, assets, stack)).join('\n');
            return `<${tag}${previewNodeAttributes(data)}>${children}</${tag}>`;
        }

        if (type === 'view.html') {
            return `<div${previewNodeAttributes(data)}>${resolvePreviewComponentMarkers(data.content || '', componentMap, graph, assets, stack)}</div>`;
        }

        if (type === 'view.text') {
            const tag = String(data.wrapper_tag || 'p') === 'none' ? '' : String(data.wrapper_tag || 'p').replace(/[^a-z0-9-]/gi, '') || 'p';
            const text = esc(data.text || data.title || 'Text');
            return tag ? `<${tag}${previewNodeAttributes(data)}>${text}</${tag}>` : text;
        }

        if (type === 'view.pagination' && typeof paginationPreviewMarkup === 'function') {
            const template = paginationTemplates.find(item => String(item.id) === String(data.template_id)) || paginationTemplates[0];
            return template ? paginationPreviewMarkup(node, template) : '<div class="tvi-preview-component-placeholder">Pagination</div>';
        }

        if (type === 'view.do_action' || type === 'view.action_hook') {
            const hookName = String(data.hook_name || 'custom_hook').trim() || 'custom_hook';
            return `<div class="tvi-preview-component-placeholder">Action hook: <code>${esc(hookName)}</code></div>`;
        }

        return `<div class="tvi-preview-component-placeholder">${esc(data.title || definitions[type]?.label || name)}</div>`;
    }

    function nestedPreviewAssets(assets) {
        const css = [...assets.css.values()].join('\n\n');
        const js = [...assets.js.values()].map(item => {
            const selector = JSON.stringify(`[data-tvi-html-component="${item.name}"]`);
            return `(function () {\n    document.querySelectorAll(${selector}).forEach(function (root) {\n        if (root.dataset.tviHtmlPreviewReady === '1') return;\n        root.dataset.tviHtmlPreviewReady = '1';\n        try {\n            (function (root) {\n${String(item.code).split('\n').map(line => `                ${line}`).join('\n')}\n            })(root);\n        } catch (error) { console.error(error); }\n    });\n})();`;
        }).join('\n\n');
        return { css, js };
    }

    function updateHtmlPreview() {
        if (!htmlDesignerPreview) return;
        const values = htmlDesignerValues();
        const view = viewForGraph(state.htmlDesigner?.graphId || state.activeGraphId);
        const themeState = viewThemeState(view);
        const variables = themeVariableDeclarations(themeState.colors);
        const graph = state.project.graphs?.[state.htmlDesigner?.graphId || state.activeGraphId] || { nodes: [], edges: [] };
        const componentMap = previewComponentMap(graph, values);
        const previewAssets = { css: new Map(), js: new Map() };
        const currentName = componentSlug(values.component_name || 'custom-component') || 'custom-component';
        const resolvedHtml = resolvePreviewComponentMarkers(values.html_code, componentMap, graph, previewAssets, [currentName]);
        const nestedAssets = nestedPreviewAssets(previewAssets);
        const safeScript = values.js_code.replace(/<\/script/gi, '<\\/script');
        const documentSource = `<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
* { box-sizing: border-box; }
html, body { min-height: 100%; }
body { margin: 0; padding: 24px; color: var(--thv-theme-text, #0f172a); background: var(--thv-theme-surface-alt, #f8fafc); font-family: Arial, Helvetica, sans-serif; }
[data-tvi-preview-root] { ${variables} width: min(1100px, 100%); margin: 0 auto; color: var(--thv-theme-text, #0f172a); }
.tvi-preview-component-warning { padding: 12px; color: #991b1b; background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px; }
.tvi-preview-component-placeholder { padding: 16px; color: var(--thv-theme-muted, #64748b); background: var(--thv-theme-surface, #fff); border: 1px dashed var(--thv-theme-border, #cbd5e1); border-radius: 8px; }
${values.css_code}
${nestedAssets.css}
</style>
</head>
<body>
<div data-tvi-preview-root>${resolvedHtml}</div>
<script>
${previewIframeLinkGuardScript()}
${nestedAssets.js.replace(/<\/script/gi, '<\\/script')}
(function () {
    const root = document.querySelector('[data-tvi-preview-root]');
    try {
        (function (root) {
${safeScript}
        })(root);
    } catch (error) {
        console.error(error);
        const message = document.createElement('pre');
        message.style.cssText = 'padding:12px;color:#991b1b;background:#fee2e2;border:1px solid #fecaca;border-radius:8px;white-space:pre-wrap';
        message.textContent = 'JavaScript error: ' + (error && error.message ? error.message : error);
        document.body.appendChild(message);
    }
})();
<\/script>
</body>
</html>`;
        htmlDesignerPreview.srcdoc = documentSource;
        if (htmlPreviewStatus) htmlPreviewStatus.textContent = `Live sandbox · ${values.component_name} · ${htmlPreviewWidthLabel()}`;
    }

    function htmlSnippetCategories() {
        return [
            'All',
            ...Array.from(new Set(
                htmlSnippets
                    .map(snippet => String(snippet.category || 'General').trim() || 'General')
            )).sort((a, b) => a.localeCompare(b))
        ];
    }

    function filteredHtmlSnippets() {
        const query = String(htmlSnippetSearch?.value || '').trim().toLowerCase();
        return htmlSnippets.filter(snippet => {
            const category = String(snippet.category || 'General');
            const text = `${snippet.name || ''} ${snippet.description || ''} ${category} ${(snippet.tags || []).join(' ')}`.toLowerCase();
            if (query) return text.includes(query); // Search always spans every folder.
            return state.htmlSnippetCategory === 'All' || category === state.htmlSnippetCategory;
        });
    }

    function selectedHtmlSnippet() {
        return htmlSnippets.find(item => String(item.id) === String(state.htmlSnippetSelectedId)) || null;
    }

    function renderHtmlSnippets() {
        if (!htmlSnippetList) return;

        const categories = htmlSnippetCategories();
        if (!categories.includes(state.htmlSnippetCategory)) state.htmlSnippetCategory = 'All';

        if (htmlSnippetTabs) {
            htmlSnippetTabs.innerHTML = categories.map(category => {
                const count = category === 'All'
                    ? htmlSnippets.length
                    : htmlSnippets.filter(snippet => String(snippet.category || 'General') === category).length;
                return `<option value="${attr(category)}" ${category === state.htmlSnippetCategory ? 'selected' : ''}>${esc(category)} (${count})</option>`;
            }).join('');
        }

        const filtered = filteredHtmlSnippets();
        const perPage = Math.max(1, Number(state.htmlSnippetPerPage) || 10);
        const pageCount = Math.max(1, Math.ceil(filtered.length / perPage));
        state.htmlSnippetPage = clamp(Number(state.htmlSnippetPage) || 1, 1, pageCount);
        const offset = (state.htmlSnippetPage - 1) * perPage;
        const pageItems = filtered.slice(offset, offset + perPage);

        if (!pageItems.some(snippet => String(snippet.id) === String(state.htmlSnippetSelectedId))) {
            state.htmlSnippetSelectedId = pageItems[0]?.id || null;
        }

        htmlSnippetList.innerHTML = pageItems.map(snippet => {
            const active = String(snippet.id) === String(state.htmlSnippetSelectedId);
            return `<button type="button" class="tvi-html-snippet-card tvi-html-snippet-card--selectable ${active ? 'is-active' : ''}" data-html-snippet-select="${attr(snippet.id)}"><span class="tvi-html-snippet-card__body"><strong>${esc(snippet.name || snippet.id)}</strong><span class="tvi-html-snippet-card__description">${esc(snippet.description || '')}</span><span class="tvi-html-snippet-card__meta"><span>${esc(snippet.category || 'General')}</span><code>${esc(snippet.package_path || snippet.id)}</code></span></span></button>`;
        }).join('') || '<p class="tvi-muted">No snippets match this search.</p>';

        if (htmlSnippetPageInfo) {
            htmlSnippetPageInfo.textContent = `Page ${state.htmlSnippetPage} of ${pageCount} · ${filtered.length} snippet${filtered.length === 1 ? '' : 's'}`;
        }
        if (htmlSnippetPrev) htmlSnippetPrev.disabled = state.htmlSnippetPage <= 1;
        if (htmlSnippetNext) htmlSnippetNext.disabled = state.htmlSnippetPage >= pageCount;

        renderHtmlSnippetPalette();
        renderHtmlSnippetPreview();
    }

    function htmlSnippetPaletteOptions(theme) {
        const themePalettes = (Array.isArray(theme?.palettes) ? theme.palettes : []).map(palette => ({
            value: `theme:${palette.id}`,
            id: String(palette.id || 'default'),
            name: String(palette.name || palette.id || 'Default'),
            source: 'theme',
            colors: formThemeColorMap(palette.colors)
        }));
        const presets = htmlSnippetPalettePresets.map(preset => ({
            value: `preset:${preset.id}`,
            id: preset.id,
            name: preset.name,
            source: 'preset',
            colors: formThemeColorMap(preset.colors)
        }));
        return [...themePalettes, ...presets];
    }

    function htmlSnippetPaletteOption(theme, value, fallbackPaletteId = 'default') {
        const options = htmlSnippetPaletteOptions(theme);
        return options.find(option => option.value === String(value || ''))
            || options.find(option => option.value === `theme:${fallbackPaletteId}`)
            || options[0]
            || null;
    }

    function renderHtmlSnippetPalette() {
        const editor = state.htmlSnippetPaletteEditor;
        if (!editor) return;
        const theme = formThemeById(editor.themeId);
        if (!theme) return;
        const option = htmlSnippetPaletteOption(theme, editor.paletteSelection, editor.paletteId);
        if (!option) return;
        editor.paletteSelection = option.value;
        if (option.source === 'theme') editor.paletteId = option.id;
        if (htmlSnippetThemeName) htmlSnippetThemeName.textContent = `${theme.name || theme.id} · ${option.name}`;
        if (htmlSnippetPalette) {
            const options = htmlSnippetPaletteOptions(theme);
            const themeOptions = options
                .filter(item => item.source === 'theme')
                .map(item => `<option value="${attr(item.value)}" ${item.value === option.value ? 'selected' : ''}>${esc(item.name)}</option>`)
                .join('');
            const presetOptions = options
                .filter(item => item.source === 'preset')
                .map(item => `<option value="${attr(item.value)}" ${item.value === option.value ? 'selected' : ''}>${esc(item.name)}</option>`)
                .join('');
            htmlSnippetPalette.innerHTML = `<optgroup label="Form theme palettes">${themeOptions}</optgroup><optgroup label="Snippet color presets">${presetOptions}</optgroup>`;
        }
        if (htmlSnippetColors) {
            htmlSnippetColors.innerHTML = Object.entries(editor.colors).map(([name, value]) => {
                const isHex = /^#[0-9a-f]{6}$/i.test(String(value));
                return `<div class="tvi-form-theme-color"><label><span>${esc(name.replace(/[_-]+/g, ' '))}</span><input type="text" value="${attr(value)}" data-html-snippet-color="${attr(name)}"></label><input type="color" value="${attr(isHex ? value : '#000000')}" data-html-snippet-color-picker="${attr(name)}" title="Choose ${attr(name)} color" aria-label="Choose ${attr(name)} color"></div>`;
            }).join('');
        }
    }

    function htmlSnippetPreviewDocument(snippet, colors) {
        if (!snippet) {
            return '<!doctype html><html><body style="font-family:system-ui;padding:24px">No snippet selected.</body></html>';
        }
        const variables = themeVariableDeclarations(colors);
        const safeScript = String(snippet.js || '').replace(/<\/script/gi, '<\\/script');
        return `<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
*{box-sizing:border-box}
html,body{min-height:100%}
body{margin:0;padding:28px;background:var(--thv-theme-surface-alt,#f8fafc);color:var(--thv-theme-text,#0f172a);font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
[data-tvi-snippet-preview]{${variables}width:min(1100px,100%);margin:auto;color:var(--thv-theme-text,#0f172a)}
${String(snippet.css || '')}
</style>
</head>
<body>
<div data-tvi-snippet-preview>${String(snippet.html || '')}</div>
<script>
${previewIframeLinkGuardScript()}
(function(){
const root=document.querySelector('[data-tvi-snippet-preview]');
try{(function(root){
${safeScript}
})(root);}catch(error){console.error(error);const message=document.createElement('pre');message.style.cssText='padding:12px;color:#991b1b;background:#fee2e2;border:1px solid #fecaca;border-radius:8px;white-space:pre-wrap';message.textContent='JavaScript error: '+(error&&error.message?error.message:error);document.body.appendChild(message);}
})();
<\/script>
</body>
</html>`;
    }

    function renderHtmlSnippetPreview() {
        const snippet = selectedHtmlSnippet();
        const editor = state.htmlSnippetPaletteEditor;
        if (htmlSnippetPreviewName) htmlSnippetPreviewName.textContent = snippet?.name || 'Snippet preview';
        if (htmlSnippetPreviewDescription) htmlSnippetPreviewDescription.textContent = snippet?.description || 'Select a snippet from the library.';
        if (htmlSnippetPreviewCategory) htmlSnippetPreviewCategory.textContent = snippet ? `${snippet.category || 'General'} · Live isolated iframe` : 'Live isolated iframe';
        if (htmlSnippetPreview) htmlSnippetPreview.srcdoc = htmlSnippetPreviewDocument(snippet, editor?.colors || {});
        htmlSnippetsModal?.querySelectorAll('[data-html-snippet-section]').forEach(button => {
            button.disabled = !snippet;
        });
    }

    function htmlSnippetsModalClick(event) {
        const selectButton = event.target.closest('[data-html-snippet-select]');
        if (selectButton) {
            state.htmlSnippetSelectedId = selectButton.dataset.htmlSnippetSelect || null;
            renderHtmlSnippets();
            return;
        }

        if (event.target.closest('#tvi-html-snippet-prev')) {
            state.htmlSnippetPage = Math.max(1, state.htmlSnippetPage - 1);
            renderHtmlSnippets();
            return;
        }

        if (event.target.closest('#tvi-html-snippet-next')) {
            state.htmlSnippetPage += 1;
            renderHtmlSnippets();
            return;
        }

        htmlSnippetClick(event);
    }

    function htmlSnippetClick(event) {
        const button = event.target.closest('[data-html-snippet-section]');
        if (!button) return;
        const snippet = selectedHtmlSnippet();
        if (!snippet) return;
        const section = button.dataset.htmlSnippetSection;
        if (section === 'all' || section === 'html') appendHtmlEditor('html', snippet.html, `<!-- ${snippet.name} -->`);
        if (section === 'all' || section === 'css') appendHtmlEditor('css', snippet.css, `/* ${snippet.name} */`);
        if (section === 'all' || section === 'js') appendHtmlEditor('js', snippet.js, `// ${snippet.name}`);
        scheduleHtmlPreview();
        toast(`${snippet.name} ${section === 'all' ? 'snippet' : section.toUpperCase()} added.`, 'success');
    }

    function htmlSnippetPaletteChange() {
        const editor = state.htmlSnippetPaletteEditor;
        if (!editor) return;
        const theme = formThemeById(editor.themeId);
        const option = htmlSnippetPaletteOption(theme, htmlSnippetPalette?.value, editor.paletteId);
        if (!option) return;
        editor.paletteSelection = option.value;
        if (option.source === 'theme') editor.paletteId = option.id;
        editor.colors = formThemeColorMap(option.colors);
        renderHtmlSnippetPalette();
        renderHtmlSnippetPreview();
    }

    function htmlSnippetColorInput(event) {
        const editor = state.htmlSnippetPaletteEditor;
        if (!editor) return;
        const textName = event.target.dataset.htmlSnippetColor;
        const pickerName = event.target.dataset.htmlSnippetColorPicker;
        const name = textName || pickerName;
        if (!name) return;
        editor.colors[name] = event.target.value;
        if (pickerName) {
            const text = htmlSnippetColors?.querySelector(`[data-html-snippet-color="${cssEscape(name)}"]`);
            if (text) text.value = event.target.value;
        } else if (/^#[0-9a-f]{6}$/i.test(event.target.value)) {
            const picker = htmlSnippetColors?.querySelector(`[data-html-snippet-color-picker="${cssEscape(name)}"]`);
            if (picker) picker.value = event.target.value;
        }
        renderHtmlSnippetPreview();
    }

    function resetHtmlSnippetColors() {
        const editor = state.htmlSnippetPaletteEditor;
        if (!editor) return;
        const theme = formThemeById(editor.themeId);
        const option = htmlSnippetPaletteOption(theme, editor.paletteSelection, editor.paletteId);
        if (!option) return;
        editor.colors = formThemeColorMap(option.colors);
        renderHtmlSnippetPalette();
        renderHtmlSnippetPreview();
    }

    function applyHtmlSnippetColors() {
        const editor = state.htmlSnippetPaletteEditor;
        if (!editor) return;
        const look = nodeById(editor.lookNodeId, 'main');
        if (!look || look.type !== 'looks.look') return;
        snapshot();
        look.data.form_theme_palette_id = editor.paletteId;
        look.data.form_theme_palette_selection = editor.paletteSelection || `theme:${editor.paletteId}`;
        look.data.form_theme_colors = formThemeColorMap(editor.colors);
        changed();
        renderAll();
        updateHtmlPreview();
        toast('Theme colors applied to the Look and all connected Views.', 'success');
    }

    function exportHtmlSnippetColors() {
        const editor = state.htmlSnippetPaletteEditor;
        if (!editor) return;
        const theme = formThemeById(editor.themeId);
        const payload = {
            format: 'thunder-visual-ide-form-palette',
            format_version: 1,
            theme_id: editor.themeId,
            theme_name: theme?.name || editor.themeId,
            palette_id: editor.paletteId,
            palette_selection: editor.paletteSelection,
            colors: formThemeColorMap(editor.colors)
        };
        downloadBlob(JSON.stringify(payload, null, 2), `${String(editor.themeId).replace(/[^a-z0-9_-]+/gi, '-')}-palette.json`, 'application/json');
    }

    function importHtmlSnippetColors(event) {
        const file = event.target.files?.[0];
        event.target.value = '';
        const editor = state.htmlSnippetPaletteEditor;
        if (!file || !editor) return;
        const reader = new FileReader();
        reader.onload = () => {
            try {
                const payload = JSON.parse(String(reader.result || '{}'));
                if (!payload || typeof payload !== 'object' || !payload.colors || typeof payload.colors !== 'object') {
                    throw new Error('The file does not contain a Look theme color palette.');
                }
                const theme = formThemeById(editor.themeId);
                const requestedSelection = payload.palette_selection
                    || `theme:${payload.palette_id || editor.paletteId}`;
                const option = htmlSnippetPaletteOption(theme, requestedSelection, editor.paletteId);
                if (!option) throw new Error('No compatible palette is available for these colors.');
                editor.paletteSelection = option.value;
                if (option.source === 'theme') editor.paletteId = option.id;
                editor.colors = {
                    ...formThemeColorMap(option.colors),
                    ...formThemeColorMap(payload.colors)
                };
                renderHtmlSnippetPalette();
                renderHtmlSnippetPreview();
                toast('Theme colors imported into the snippet preview.', 'success');
            } catch (error) {
                toast(error.message || 'Unable to import theme colors.', 'fail');
            }
        };
        reader.readAsText(file);
    }

    function appendHtmlEditor(section, content, heading = '') {
        const editor = state.htmlEditors?.[section];
        const value = String(content || '').trim();
        if (!editor || value === '') return;
        const existing = editor.getValue().replace(/\s+$/, '');
        editor.setValue(`${existing}${existing ? '\n\n' : ''}${heading ? heading + '\n' : ''}${value}\n`);
        editor.scrollIntoView({ line: editor.lineCount() - 1, ch: 0 });
    }

    function exportHtmlComponent() {
        const values = htmlDesignerValues();
        const payload = {
            format: 'thunder-html-component',
            version: 1,
            exported_at: new Date().toISOString(),
            data: values
        };
        downloadBlob(
            JSON.stringify(payload, null, 2),
            `${values.component_name || 'html-component'}.tvi-component.json`,
            'application/json'
        );
    }

    function importHtmlComponent() {
        const file = htmlComponentImportInput?.files?.[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = () => {
            try {
                const payload = JSON.parse(String(reader.result || ''));
                if (payload?.format !== 'thunder-html-component' || !payload.data || typeof payload.data !== 'object') {
                    throw new Error('This is not a Thunder Visual IDE HTML component export.');
                }
                const data = payload.data;
                htmlDesignerTitle.value = String(data.title || 'HTML Component');
                htmlDesignerName.value = componentSlug(data.component_name || 'custom-component') || 'custom-component';
                state.htmlEditors?.html.setValue(String(data.html_code || ''));
                state.htmlEditors?.css.setValue(String(data.css_code || ''));
                state.htmlEditors?.js.setValue(String(data.js_code || ''));
                refreshHtmlDesignerComponentOptions();
                updateHtmlPreview();
                toast(`${file.name} imported into the designer. Save to apply it to the node.`, 'success');
            } catch (error) {
                toast(error.message || 'Unable to import that component.', 'fail');
            }
        };
        reader.readAsText(file);
        htmlComponentImportInput.value = '';
    }

    function componentSlug(value) {
        return String(value || '')
            .trim()
            .toLowerCase()
            .replace(/[^a-z0-9_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    function openAbout(){ if (aboutModal) aboutModal.hidden=false; }
    function closeAbout(){ if (aboutModal) aboutModal.hidden=true; }

    function openSettings(){
        codeThemeSelect.value=state.codeTheme;
        if (codeWordWrapSelect) codeWordWrapSelect.value=state.codeWordWrap?'on':'off';
        settingsModal.hidden=false;
        previewSelectedSettings();
    }
    function closeSettings(){
        settingsModal.hidden=true;
        applyCodeTheme(state.codeTheme);
        applyCodeWordWrap(state.codeWordWrap);
    }
    function previewSelectedSettings(){
        applyCodeTheme(codeThemeSelect.value||'thunder-dark');
        applyCodeWordWrap((codeWordWrapSelect?.value||'off')==='on');
    }
    function saveSettings(){
        state.codeTheme=codeThemeSelect.value||'thunder-dark';
        state.codeWordWrap=(codeWordWrapSelect?.value||'off')==='on';
        localStorage.setItem('tvi_code_theme',state.codeTheme);
        localStorage.setItem('tvi_code_word_wrap',state.codeWordWrap?'1':'0');
        applyCodeTheme(state.codeTheme);
        applyCodeWordWrap(state.codeWordWrap);
        settingsModal.hidden=true;
        toast('Editor settings saved.','success');
    }
    function applyCodeTheme(theme){state.previewEditor?.setOption('theme',theme);state.fullCodeEditor?.setOption('theme',theme);Object.values(state.htmlEditors||{}).forEach(editor=>editor.setOption('theme',theme));}
    function applyCodeWordWrap(enabled){state.previewEditor?.setOption('lineWrapping',Boolean(enabled));state.fullCodeEditor?.setOption('lineWrapping',Boolean(enabled));Object.values(state.htmlEditors||{}).forEach(editor=>editor.setOption('lineWrapping',Boolean(enabled)));}

    function suppressNativeCanvasContextMenu(e){
        const path = typeof e.composedPath === 'function' ? e.composedPath() : [];
        const insideViewport = path.includes(viewport) || (e.target instanceof Element && !!e.target.closest('#tvi-viewport'));
        if (insideViewport) e.preventDefault();
    }

    function showContextMenu(items,x,y,target=null,point=null){
        state.contextTarget=target;state.contextPoint=point;
        contextMenu.innerHTML=items.map(item=>item.separator?'<hr>':`<button type="button" data-context-action="${attr(item.action)}" ${item.disabled?'disabled':''}>${esc(item.label)}</button>`).join('');
        contextMenu.hidden=false;
        const maxX=window.innerWidth-contextMenu.offsetWidth-8,maxY=window.innerHeight-contextMenu.offsetHeight-8;
        contextMenu.style.left=`${Math.max(8,Math.min(x,maxX))}px`;contextMenu.style.top=`${Math.max(8,Math.min(y,maxY))}px`;
    }
    function hideContextMenu(){contextMenu.hidden=true;state.contextTarget=null;}
    function nodeContextMenu(e){
        const el=e.target.closest('.tvi-node');if(!el)return;e.preventDefault();e.stopPropagation();
        const id=el.dataset.nodeId;if(!state.selectedNodeIds.includes(id))selectNodes([id]);state.selectedEdgeId=null;
        renderSelectionClasses();renderInspector();renderMeta();renderEdges();
        const node=nodeById(id),def=defFor(node);const items=[];
        if(node?.type==='view.html_component')items.push({action:'open-html-designer',label:'Open HTML Component Designer'},{separator:true});
        else if(['lifecycle.view','lifecycle.reusable_view'].includes(node?.type))items.push({action:'open',label:`Open ${def.nested_graph_label||'View Builder'}`},{action:'open-javascript-flow',label:'Open JavaScript Flow'},{separator:true});
        else if(def?.nested_graph)items.push({action:'open',label:`Open ${def.nested_graph_label||'Graph'}`},{separator:true});
        else if(['models.model','custom.class_definition'].includes(node.type))items.push({action:'manage-methods',label:'Manage Methods'},{separator:true});
        const chosen = selectedNodes();
        const allProtected=chosen.every(item=>isProtectedNode(item));
        const muteCandidates = chosen.filter(item=>canMuteNode(item));
        const unmute = muteCandidates.length > 0 && muteCandidates.every(nodeIsMuted);
        items.push(
            {action:'fit-selected',label:'Fit Selection to View'},
            {action:'isolate-selected',label:'Focus on Selection'},
            {action:'show-all',label:'Show All Nodes'},
            {separator:true},
            {action:'toggle-mute',label:unmute?(muteCandidates.length>1?'Unmute Selected':'Unmute Node'):(muteCandidates.length>1?'Mute Selected':'Mute Node'),disabled:muteCandidates.length===0},
            {action:'group-selected',label:'Group Selected',disabled:state.selectedNodeIds.length<2},
            {separator:true},
            {action:'copy',label:state.selectedNodeIds.length>1?`Copy ${state.selectedNodeIds.length} Nodes`:'Copy Node',disabled:allProtected},
            {action:'duplicate',label:'Duplicate',disabled:allProtected},
            {action:'save-preset',label:'Save Selection as Preset',disabled:allProtected},
            {action:'manage-presets',label:'Manage Presets…'},
            {action:'export-node-file',label:state.selectedNodeIds.length>1?`Export ${state.selectedNodeIds.length} Nodes to File…`:'Export Node to File…',disabled:allProtected},
            {separator:true},
            {action:'delete',label:state.selectedNodeIds.length>1?`Delete ${state.selectedNodeIds.length} Nodes`:'Delete Node',disabled:allProtected}
        );
        showContextMenu(items,e.clientX,e.clientY,{type:'node',id},screenToWorld(e.clientX,e.clientY));
    }
    function edgeContextMenu(e){const el=e.target.closest('[data-edge-id]');if(!el)return;e.preventDefault();e.stopPropagation();selectNodes([]);state.selectedEdgeId=el.dataset.edgeId;renderAll();showContextMenu([{action:'delete-edge',label:'Delete Connection'}],e.clientX,e.clientY,{type:'edge',id:el.dataset.edgeId});}
    function canvasContextMenu(e){if(e.target.closest('.tvi-node,[data-edge-id],.tvi-edge-delete'))return;e.preventDefault();const point=screenToWorld(e.clientX,e.clientY);showContextMenu([{action:'add-node',label:'Add Node Here…'},{action:'paste',label:'Paste Here',disabled:!canPasteIntoActiveGraph()},{action:'import-node-file',label:'Import Nodes from File…'},{action:'select-all',label:'Select All Nodes'},{action:'group-selected',label:'Group Selected',disabled:state.selectedNodeIds.length<2},{action:'manage-presets',label:'Manage Presets…'},{separator:true},{action:'fit-all',label:'Fit All Nodes'},{action:'show-all',label:'Show All Nodes'},{action:'reset-zoom',label:'Reset View'}],e.clientX,e.clientY,{type:'canvas'},point);}
    function contextMenuClick(e){const b=e.target.closest('[data-context-action]');if(!b)return;const action=b.dataset.contextAction,target=state.contextTarget,point=state.contextPoint;hideContextMenu();
        if(action==='open'&&target?.type==='node'){const n=nodeById(target.id),d=defFor(n);if(d?.nested_graph){if(!n.data.graph_id)createNestedGraph(n,true);openGraph(n.data.graph_id);}}
        else if(action==='open-javascript-flow'&&target?.type==='node'){const n=nodeById(target.id);if(n){snapshot();const id=createJavaScriptGraph(n,true);changed();openGraph(id);}}
        else if(action==='open-html-designer'&&target?.type==='node'){selectNodes([target.id]);renderInspector();openHtmlDesigner(target.id);}
        else if(action==='manage-methods'&&target?.type==='node'){selectNodes([target.id]);renderInspector();openMethods('methods_json');}
        else if(action==='copy')copySelectedNodes();else if(action==='paste')pasteCopiedNodes(point);else if(action==='duplicate')duplicateSelected();else if(action==='toggle-mute')toggleMuteSelected();else if(action==='group-selected')groupSelected();else if(action==='save-preset')savePreset();else if(action==='manage-presets')openPresets();else if(action==='export-node-file')openNodeBundleExport();else if(action==='import-node-file'){state.nodeBundleInsertPoint=point||null;nodeBundleImportInput?.click();}else if(action==='delete')deleteSelected();
        else if(action==='delete-edge'&&target?.id)removeEdge(target.id);else if(action==='add-node')openLibrary(point);
        else if(action==='select-all'){selectNodes(activeGraph().nodes.filter(n=>!hiddenNodeIds(activeGraph()).has(n.id)).map(n=>n.id));renderAll();}
        else if(action==='fit-selected')fitSelectedNodes();else if(action==='isolate-selected')hideAllExceptSelected();else if(action==='fit-all')fitAllNodes();else if(action==='show-all')showAllNodes();else if(action==='reset-zoom')resetZoom();
    }

    function openLibrary(point=null){state.paletteInsertCount=0;state.libraryInsertPoint=point;librarySearch.value=search.value;renderLibraryModal();libraryModal.hidden=false;librarySearch.focus();}
    function closeLibrary(){libraryModal.hidden=true;state.libraryInsertPoint=null;}
    function addNodeFromLibrary(type){const r=viewport.getBoundingClientRect(),p=state.libraryInsertPoint||screenToWorld(r.left+r.width/2,r.top+r.height/2);const offset=(state.paletteInsertCount++%8)*28;addNode(type,p.x-110+offset,p.y-50+offset);renderLibraryModal();}

    function openGraph(id){
        if(!state.project.graphs[id])return;
        if(state.project.graphs[state.activeGraphId]){
            state.graphSelections[state.activeGraphId]=[...state.selectedNodeIds];
        }
        state.activeGraphId=id;
        state.project.active_graph_id=id;
        const validIds=new Set(state.project.graphs[id].nodes.map(node=>node.id));
        selectNodes((state.graphSelections[id]||[]).filter(nodeId=>validIds.has(nodeId)));
        state.selectedEdgeId=null;
        state.connecting=null;
        renderAll();
    }
    function toggleFolder(id){const f=nodeById(id);if(!f)return;snapshot();f.data.collapsed=!f.data.collapsed;changed();renderAll();}
    function folderDescendants(folder, seen=new Set()){
        const result=[];
        for(const id of folder?.data?.member_ids||[]){if(seen.has(id))continue;seen.add(id);result.push(id);const child=nodeById(id);if(child?.type==='visual.folder')result.push(...folderDescendants(child,seen));}
        return result;
    }
    function hiddenNodeIds(graph){const hidden=new Set(graph.hidden_node_ids||[]);for(const f of graph.nodes.filter(n=>n.type==='visual.folder'&&n.data.collapsed))for(const id of folderDescendants(f))hidden.add(id);return hidden;}
    function groupSelected(){if(state.selectedNodeIds.length<2){toast('Select at least two nodes.','fail');return;}snapshot();const nodes=selectedNodes(),x=Math.min(...nodes.map(n=>n.x))-25,y=Math.min(...nodes.map(n=>n.y))-25;const folder={id:uid('folder'),type:'visual.folder',x,y,data:{title:'Node Group',collapsed:true,member_ids:[...state.selectedNodeIds]}};activeGraph().nodes.push(folder);selectNodes([folder.id]);changed();renderAll();}


    function loadNodeClipboard() {
        try {
            const value = JSON.parse(localStorage.getItem('tvi_node_clipboard_v1') || 'null');
            return value && Array.isArray(value.nodes) ? value : null;
        } catch {
            return null;
        }
    }

    function canPasteIntoActiveGraph() {
        return Boolean(state.nodeClipboard && state.nodeClipboard.graph_kind === activeGraph().kind && state.nodeClipboard.nodes?.length);
    }

    function copySelectedNodes() {
        const excludedTypes = new Set(['flow.start', 'flow.filter_return', 'migration.start']);
        const nodes = selectedNodes().filter(node => !excludedTypes.has(node.type));
        if (!nodes.length) {
            toast('Select one or more non-required nodes to copy.', 'warn');
            return;
        }
        const ids = new Set(nodes.map(node => node.id));
        const minX = Math.min(...nodes.map(node => Number(node.x) || 0));
        const minY = Math.min(...nodes.map(node => Number(node.y) || 0));
        const clipboard = {
            version: 1,
            graph_kind: activeGraph().kind,
            copied_at: new Date().toISOString(),
            nodes: nodes.map(node => ({ ...clone(node), x: (Number(node.x) || 0) - minX, y: (Number(node.y) || 0) - minY })),
            edges: activeGraph().edges.filter(edge => ids.has(edge.from) && ids.has(edge.to)).map(clone),
            nested_graphs: {}
        };
        for (const node of nodes) capturePresetGraph(node, clipboard.nested_graphs);
        state.nodeClipboard = clipboard;
        state.clipboardPasteCount = 0;
        localStorage.setItem('tvi_node_clipboard_v1', JSON.stringify(clipboard));
        toast(`${nodes.length} node${nodes.length === 1 ? '' : 's'} copied.`, 'success');
    }

    function pasteCopiedNodes(point = null) {
        const clipboard = state.nodeClipboard || loadNodeClipboard();
        if (!clipboard?.nodes?.length) {
            toast('There are no copied nodes to paste.', 'warn');
            return;
        }
        if (clipboard.graph_kind !== activeGraph().kind) {
            toast(`Copied ${clipboard.graph_kind} nodes cannot be pasted into a ${activeGraph().kind} graph.`, 'fail');
            return;
        }
        state.nodeClipboard = clipboard;
        snapshot();
        const rect = viewport.getBoundingClientRect();
        const base = point || screenToWorld(rect.left + rect.width / 2, rect.top + rect.height / 2);
        const offset = point ? 0 : (state.clipboardPasteCount++ % 8) * 24;
        const nodeMap = {};
        const graphMap = {};
        for (const oldId of Object.keys(clipboard.nested_graphs || {})) graphMap[oldId] = uid('graph');
        const copies = clipboard.nodes.map(original => {
            const node = clone(original);
            nodeMap[original.id] = uid(String(node.type || 'node').replace(/\W/g, '-'));
            node.id = nodeMap[original.id];
            node.x = base.x + (Number(original.x) || 0) + offset;
            node.y = base.y + (Number(original.y) || 0) + offset;
            return node;
        });
        for (const node of copies) remapNodeReferences(node, nodeMap, graphMap);
        for (const [oldGraphId, oldGraph] of Object.entries(clipboard.nested_graphs || {})) {
            const graph = clone(oldGraph);
            const newGraphId = graphMap[oldGraphId];
            const localMap = {};
            for (const child of graph.nodes || []) localMap[child.id] = uid(String(child.type || 'node').replace(/\W/g, '-'));
            graph.id = newGraphId;
            graph.owner_node_id = nodeMap[graph.owner_node_id] || graph.owner_node_id;
            for (const child of graph.nodes || []) {
                child.id = localMap[child.id];
                remapNodeReferences(child, { ...nodeMap, ...localMap }, graphMap);
            }
            for (const edge of graph.edges || []) {
                edge.id = uid('edge');
                edge.from = localMap[edge.from] || nodeMap[edge.from] || edge.from;
                edge.to = localMap[edge.to] || nodeMap[edge.to] || edge.to;
            }
            state.project.graphs[newGraphId] = graph;
        }
        activeGraph().nodes.push(...copies);
        for (const edge of clipboard.edges || []) {
            activeGraph().edges.push({ ...clone(edge), id: uid('edge'), from: nodeMap[edge.from], to: nodeMap[edge.to] });
        }
        selectNodes(copies.map(node => node.id));
        synchronizeAllLifecyclePriorities(false);
        changed();
        renderAll();
        toast(`${copies.length} copied node${copies.length === 1 ? '' : 's'} pasted.`, 'success');
    }

    function saveSelectedViewTreePreset(){
        const root=selectedNodes()[0];if(!root||activeGraph().kind!=='view')return;
        const ids=new Set([root.id]);const queue=[root.id];
        while(queue.length){const parent=queue.shift();for(const edge of activeGraph().edges.filter(e=>e.from===parent&&e.port_type==='view-child')){if(!ids.has(edge.to)){ids.add(edge.to);queue.push(edge.to);}}}
        selectNodes([...ids]);renderAll();savePreset();
    }

    function loadPresets(){try{const value=JSON.parse(localStorage.getItem(cfg.presetStorageKey)||'[]');return Array.isArray(value)?value:[];}catch{return [];}}
    function storePresets(items){localStorage.setItem(cfg.presetStorageKey,JSON.stringify(items));}
    function buildNodeBundle(nodes, name = ''){
        const ids=new Set(nodes.map(n=>n.id));
        const minX=Math.min(...nodes.map(n=>n.x)),minY=Math.min(...nodes.map(n=>n.y));
        const bundle={
            format:'thunder-visual-ide-node-bundle',
            format_version:1,
            schema_version:4,
            generated_by:'Thunder Visual IDE 0.12.97',
            id:uid('bundle'),
            name:String(name||'').trim(),
            graph_kind:activeGraph().kind,
            created_at:new Date().toISOString(),
            nodes:nodes.map(n=>({...clone(n),x:n.x-minX,y:n.y-minY})),
            edges:activeGraph().edges.filter(e=>ids.has(e.from)&&ids.has(e.to)).map(clone),
            nested_graphs:{}
        };
        for(const node of nodes)capturePresetGraph(node,bundle.nested_graphs);
        return bundle;
    }
    function defaultNodeBundleName(nodes){
        if(nodes.length===1)return `${nodeTitle(nodes[0])} node`;
        return `${nodes.length} ${activeGraph().kind} nodes`;
    }
    function safeDownloadName(value,fallback='thunder-nodes'){
        const clean=String(value||'').trim().toLowerCase().replace(/[^a-z0-9_-]+/g,'-').replace(/^-+|-+$/g,'');
        return clean||fallback;
    }
    function savePreset(){
        const nodes=selectedNodes().filter(node=>!isProtectedNode(node));if(!nodes.length){toast('Select one or more non-required nodes to save as a preset.','fail');return;}
        const name=prompt('Preset name:',nodes.length===1?nodeTitle(nodes[0]):`${nodes.length} node workflow`);if(!name)return;
        const preset=buildNodeBundle(nodes,name.trim());
        preset.id=uid('preset');
        delete preset.format;
        delete preset.format_version;
        const items=loadPresets();items.unshift(preset);storePresets(items);toast(`Preset “${preset.name}” saved.`,'success');
    }
    function openNodeBundleExport(){
        const nodes=selectedNodes().filter(node=>!isProtectedNode(node));
        if(!nodes.length){toast('Select one or more non-required nodes to export.','fail');return;}
        const defaultName=defaultNodeBundleName(nodes);
        state.pendingNodeBundle=buildNodeBundle(nodes,defaultName);
        nodeExportName.value=defaultName;
        nodeExportSummary.textContent=`Export ${nodes.length} selected node${nodes.length===1?'':'s'}, their internal connections, and nested graphs.`;
        nodeExportModal.hidden=false;
        requestAnimationFrame(()=>{nodeExportName.focus();nodeExportName.select();});
    }
    function closeNodeBundleExport(){
        nodeExportModal.hidden=true;
        state.pendingNodeBundle=null;
    }
    function confirmNodeBundleExport(){
        const bundle=state.pendingNodeBundle;
        if(!bundle)return;
        const name=String(nodeExportName.value||'').trim()||defaultNodeBundleName(bundle.nodes||[]);
        bundle.name=name;
        downloadBlob(JSON.stringify(bundle,null,2),`${safeDownloadName(name)}.thunder-nodes.json`,'application/json');
        closeNodeBundleExport();
        toast(`${bundle.nodes.length} node${bundle.nodes.length===1?'':'s'} exported to file.`,'success');
    }
    function capturePresetGraph(node,target){
        for(const graphId of nestedGraphIds(node)){
            if(target[graphId]||!state.project.graphs[graphId])continue;
            const g=clone(state.project.graphs[graphId]);target[graphId]=g;
            for(const child of g.nodes||[])capturePresetGraph(child,target);
        }
    }
    function openPresets(){renderPresets();presetsModal.hidden=false;}
    function closePresets(){presetsModal.hidden=true;}
    function presetRow(preset,index,source){
        const category=preset.category?` · ${esc(preset.category)}`:'';
        const description=preset.description?`<p>${esc(preset.description)}</p>`:'';
        const deleteButton=source==='user'?`<button type="button" class="tvi-danger" data-preset-action="delete" data-preset-source="user" data-preset-index="${index}">Delete</button>`:'';
        return `<article class="tvi-preset-row"><div><strong>${esc(preset.name||'Preset')}</strong><small>${esc(preset.graph_kind||'architecture')}${category} · ${(preset.nodes||[]).length} nodes</small>${description}</div><div><button type="button" data-preset-action="insert" data-preset-source="${source}" data-preset-index="${index}" ${(preset.graph_kind!==activeGraph().kind)?'disabled title="Open a compatible graph to insert this preset"':''}>Insert</button>${deleteButton}</div></article>`;
    }
    function presetsLatestFirst(items){
        return items.map((preset,index)=>({preset,index})).sort((left,right)=>{
            const leftTime=Date.parse(String(left.preset?.created_at||left.preset?.modified_at||''))||0;
            const rightTime=Date.parse(String(right.preset?.created_at||right.preset?.modified_at||''))||0;
            return (rightTime-leftTime)||(right.index-left.index);
        });
    }
    function renderPresets(){
        const items=loadPresets();
        bundledPresetsList.innerHTML=presetsLatestFirst(bundledPresets).map(({preset,index})=>presetRow(preset,index,'bundled')).join('')||'<p class="tvi-muted">No bundled presets are installed.</p>';
        presetsList.innerHTML=presetsLatestFirst(items).map(({preset,index})=>presetRow(preset,index,'user')).join('')||'<p class="tvi-muted">No user presets saved yet. Select nodes and click Save Preset.</p>';
    }
    function presetModalClick(e){const b=e.target.closest('[data-preset-action]');if(!b)return;const source=b.dataset.presetSource||'user';const items=source==='bundled'?bundledPresets:loadPresets(),i=Number(b.dataset.presetIndex),preset=items[i];if(!preset)return;if(b.dataset.presetAction==='delete'&&source==='user'){if(!confirm(`Delete preset ${preset.name}?`))return;items.splice(i,1);storePresets(items);renderPresets();return;}if(b.dataset.presetAction==='insert')insertNodeBundle(preset,null,`${source==='bundled'?'Bundled preset':'Preset'} “${preset.name}”`);}
    function insertPreset(preset){insertNodeBundle(preset,null,`Preset “${preset.name||'Preset'}”`);}
    function insertNodeBundle(bundle,point=null,label='Node bundle'){
        if(!bundle||!Array.isArray(bundle.nodes)||!Array.isArray(bundle.edges)){
            toast('The node file is invalid.','fail');return;
        }
        if(bundle.graph_kind!==activeGraph().kind){toast(`This node file belongs to a ${bundle.graph_kind||'different'} graph, not the current ${activeGraph().kind} graph.`,'fail');return;}
        snapshot();
        const r=viewport.getBoundingClientRect(),center=point||screenToWorld(r.left+r.width/2,r.top+r.height/2),nodeMap={},graphMap={};
        for(const oldId of Object.keys(bundle.nested_graphs||{}))graphMap[oldId]=uid('graph');
        const copies=(bundle.nodes||[]).map(original=>{const n=clone(original);nodeMap[original.id]=uid(n.type.replace(/\W/g,'-'));n.id=nodeMap[original.id];n.x=center.x+(Number(original.x)||0)-100;n.y=center.y+(Number(original.y)||0)-60;return n;});
        for(const n of copies)remapNodeReferences(n,nodeMap,graphMap);
        for(const [oldGraphId,oldGraph] of Object.entries(bundle.nested_graphs||{})){const g=clone(oldGraph),newGraphId=graphMap[oldGraphId],localMap={};for(const cn of g.nodes||[])localMap[cn.id]=uid(cn.type.replace(/\W/g,'-'));g.id=newGraphId;g.owner_node_id=nodeMap[g.owner_node_id]||g.owner_node_id;for(const cn of g.nodes||[]){cn.id=localMap[cn.id];remapNodeReferences(cn,{...nodeMap,...localMap},graphMap);}for(const ce of g.edges||[]){ce.id=uid('edge');ce.from=localMap[ce.from]||nodeMap[ce.from]||ce.from;ce.to=localMap[ce.to]||nodeMap[ce.to]||ce.to;}state.project.graphs[newGraphId]=g;}
        activeGraph().nodes.push(...copies);for(const e of bundle.edges||[])activeGraph().edges.push({...clone(e),id:uid('edge'),from:nodeMap[e.from],to:nodeMap[e.to]});selectNodes(copies.map(n=>n.id));synchronizeAllLifecyclePriorities(false);changed();renderAll();toast(`${label} inserted: ${copies.length} node${copies.length===1?'':'s'}.`,'success');
    }
    function importNodeBundleFile(){
        const file=nodeBundleImportInput?.files?.[0];if(!file)return;
        const insertPoint=state.nodeBundleInsertPoint;state.nodeBundleInsertPoint=null;
        const reader=new FileReader();
        reader.onload=()=>{try{const bundle=JSON.parse(String(reader.result));if(bundle?.format&&bundle.format!=='thunder-visual-ide-node-bundle')throw new Error('This is not a Thunder Visual IDE node bundle.');insertNodeBundle(bundle,insertPoint,`Node file “${bundle.name||file.name}”`);}catch(err){toast(err.message||'Could not import the node file.','fail');}};
        reader.readAsText(file);nodeBundleImportInput.value='';
    }
    function remapNodeReferences(node,nodeMap,graphMap){if(!node.data)return;for(const key of ['model_node_id','function_node_id','class_node_id'])if(nodeMap[node.data[key]])node.data[key]=nodeMap[node.data[key]];if(Array.isArray(node.data.member_ids))node.data.member_ids=node.data.member_ids.map(id=>nodeMap[id]).filter(Boolean);remapNestedGraphReferences(node,graphMap);}
    function exportPresets(){downloadBlob(JSON.stringify(loadPresets(),null,2),'thunder-node-presets.json','application/json');}
    function importPresets(){const file=presetImportInput.files?.[0];if(!file)return;const reader=new FileReader();reader.onload=()=>{try{const incoming=JSON.parse(String(reader.result));if(!Array.isArray(incoming))throw new Error('Preset file must contain an array.');const items=loadPresets();items.push(...incoming.filter(p=>p&&Array.isArray(p.nodes)));storePresets(items);renderPresets();toast('Presets imported.','success');}catch(err){toast(err.message,'fail');}};reader.readAsText(file);presetImportInput.value='';}

    function selectNodes(ids){state.selectedNodeIds=[...new Set(ids)];state.selectedEdgeId=null;}
    function toggleSelection(id){state.selectedNodeIds=state.selectedNodeIds.includes(id)?state.selectedNodeIds.filter(x=>x!==id):[...state.selectedNodeIds,id];}
    function renderSelectionClasses(){nodesLayer.querySelectorAll('.tvi-node').forEach(el=>el.classList.toggle('is-selected',state.selectedNodeIds.includes(el.dataset.nodeId)));}
    function deleteSelected(){
        if(!state.selectedNodeIds.length)return;
        const chosen=selectedNodes();
        const deletable=chosen.filter(node=>!isProtectedNode(node));
        const protectedCount=chosen.length-deletable.length;
        if(!deletable.length){toast('Required Start and Return Filter Data nodes cannot be deleted.','warn');return;}
        snapshot();const ids=new Set(deletable.map(node=>node.id));
        for(const node of deletable)for(const graphId of nestedGraphIds(node))delete state.project.graphs[graphId];
        activeGraph().nodes=activeGraph().nodes.filter(n=>!ids.has(n.id));
        activeGraph().edges=activeGraph().edges.filter(e=>!ids.has(e.from)&&!ids.has(e.to));
        for(const folder of activeGraph().nodes.filter(n=>n.type==='visual.folder'))folder.data.member_ids=(folder.data.member_ids||[]).filter(id=>!ids.has(id));
        selectNodes(protectedCount?chosen.filter(node=>isProtectedNode(node)).map(node=>node.id):[]);synchronizeAllLifecyclePriorities(false);changed();renderAll();
        if(protectedCount)toast(`${protectedCount} required filter node${protectedCount===1?' was':'s were'} kept.`, 'warn');
    }
    function duplicateSelected(){
        const originals=selectedNodes().filter(node=>!isProtectedNode(node));if(!originals.length){toast('Required graph nodes cannot be duplicated.','warn');return;}
        snapshot();const nodeMap={};
        for(const original of originals)nodeMap[original.id]=uid(original.type.replace(/\W/g,'-'));
        const copies=[];
        for(const original of originals){
            const copy=clone(original);copy.id=nodeMap[original.id];copy.x+=35;copy.y+=35;
            cloneNestedGraphsForNode(original,copy,nodeMap);
            copies.push(copy);activeGraph().nodes.push(copy);
        }
        for(const edge of activeGraph().edges.filter(edge=>nodeMap[edge.from]&&nodeMap[edge.to]))activeGraph().edges.push({...clone(edge),id:uid('edge'),from:nodeMap[edge.from],to:nodeMap[edge.to]});
        selectNodes(copies.map(node=>node.id));synchronizeAllLifecyclePriorities(false);changed();renderAll();
    }
    function cloneNestedGraphsForNode(original,copy,mainNodeMap){
        const graphMap={};for(const oldId of nestedGraphIds(original))graphMap[oldId]=uid('graph');
        for(const [oldGraphId,newGraphId] of Object.entries(graphMap)){
            const oldGraph=state.project.graphs[oldGraphId];if(!oldGraph)continue;
            const graph=clone(oldGraph),localMap={};for(const child of graph.nodes||[])localMap[child.id]=uid(child.type.replace(/\W/g,'-'));
            graph.id=newGraphId;graph.owner_node_id=copy.id;graph.name=String(graph.name||'Execution Flow').replace(nodeTitle(original),nodeTitle(copy));
            for(const child of graph.nodes||[]){child.id=localMap[child.id];remapNodeReferences(child,{...mainNodeMap,...localMap},graphMap);}
            for(const edge of graph.edges||[]){edge.id=uid('edge');edge.from=localMap[edge.from]||edge.from;edge.to=localMap[edge.to]||edge.to;}
            state.project.graphs[newGraphId]=graph;
        }
        remapNodeReferences(copy,mainNodeMap,graphMap);
    }


    function newProject(){if(!confirm('Replace the current project with a blank schema v4 project?'))return;resetOverwriteApprovals();state.project=blankProject();state.activeGraphId='main';state.graphSelections={};resetHistory();changed();renderAll();}
    function sampleProjects(){return Array.isArray(cfg.sampleProjects)?cfg.sampleProjects:[];}
    function defaultSample(){const samples=sampleProjects();return samples.find(sample=>sample.id===cfg.defaultSampleId)||samples[0]||null;}
    function openSamples(){renderSamples();samplesModal.hidden=false;}
    function closeSamples(){samplesModal.hidden=true;}
    function renderSamples(){
        const samples=sampleProjects();
        samplesList.innerHTML=samples.map(sample=>{
            const focus=(sample.focus||[]).map(item=>`<span>${esc(item)}</span>`).join('');
            return `<article class="tvi-sample-card"><div class="tvi-sample-card__head"><strong>${esc(sample.name||sample.id)}</strong><span>${esc(sample.level||'Beginner')}</span></div><p>${esc(sample.description||'')}</p><div class="tvi-sample-tags">${focus}</div><button type="button" class="tvi-button-primary" data-sample-id="${attr(sample.id)}">Load Sample</button></article>`;
        }).join('')||'<p class="tvi-muted">No sample projects were found.</p>';
    }
    function sampleModalClick(event){const button=event.target.closest('[data-sample-id]');if(!button)return;loadSample(button.dataset.sampleId);}
    function loadSample(sampleId){
        const sample=sampleProjects().find(item=>item.id===sampleId);
        if(!sample||!sample.project){toast('That sample project could not be loaded.','fail');return;}
        resetOverwriteApprovals();state.project=clone(sample.project);normalizeProject();state.activeGraphId='main';state.graphSelections={};state.selectedNodeIds=[];state.selectedEdgeId=null;resetHistory();changed();renderAll();closeSamples();setStatus(`${sample.name} loaded`);toast(`${sample.name} is ready to explore.`,'success');
    }
    function saveBrowserRecovery(){localStorage.setItem(cfg.storageKey,JSON.stringify(state.project));setStatus('Browser recovery copy saved');toast('Recovery copy saved in this browser.','success');}
    function loadSaved(){try{const v=localStorage.getItem(cfg.storageKey);if(!v)return null;const p=JSON.parse(v);return p?.schema_version===4?p:null;}catch{return null;}}
    function workspace(){return state.project.workspace&&typeof state.project.workspace==='object'?state.project.workspace:{};}
    async function saveNamedProject(){
        const ws=workspace();if(!ws.project_id){openProjects('save');return;}
        await persistProject(String(ws.project_name||projectId()),String(ws.project_id));
    }
    async function persistProject(name,id=''){
        setStatus('Saving project…');
        try{const r=await fetch(`${cfg.apiBase}/project-save`,{method:'POST',body:apiForm({project_name:name,project_id:id})});const data=await r.json();if(!r.ok||!data.ok){showIssues(data);return false;}state.project=data.project;normalizeProject();state.dirty=false;localStorage.setItem(cfg.storageKey,JSON.stringify(state.project));renderAll();setStatus(`Saved: ${data.record.name}`);toast(`Project “${data.record.name}” saved.`,'success');return true;}catch(err){toast(err.message,'fail');setStatus('Project save failed');return false;}
    }
    async function openProjects(mode='load'){state.projectDialogMode=mode;const ws=workspace();projectNameInput.value=mode==='save'?(ws.project_name||projectId()):'';projectsModal.hidden=false;await refreshProjects();if(mode==='save')projectNameInput.focus();}
    function closeProjects(){projectsModal.hidden=true;}
    async function refreshProjects(){
        projectsList.innerHTML='<p class="tvi-muted">Loading projects…</p>';
        try{const r=await fetch(`${cfg.apiBase}/project-list`,{method:'POST',body:apiForm()});const data=await r.json();if(!r.ok||!data.ok){showIssues(data);return;}renderProjects(data.projects||[]);}catch(err){projectsList.innerHTML=`<p class="tvi-muted">${esc(err.message)}</p>`;}
    }
    function renderProjects(items){projectsCount.textContent=`${items.length} saved`;projectsList.innerHTML=items.map(item=>`<article class="tvi-project-row"><div><strong>${esc(item.name)}</strong><small>Updated ${esc(formatDate(item.modified_at))}</small><code>${esc(item.id)}</code></div><div><button type="button" data-project-action="load" data-project-id="${attr(item.id)}">Load</button><button type="button" data-project-action="overwrite" data-project-id="${attr(item.id)}" data-project-name="${attr(item.name)}">Save Over</button><button type="button" class="tvi-danger" data-project-action="delete" data-project-id="${attr(item.id)}" data-project-name="${attr(item.name)}">Delete</button></div></article>`).join('')||'<p class="tvi-muted">No server-saved projects yet.</p>';}
    async function saveProjectAs(){const name=projectNameInput.value.trim();if(!name){toast('Enter a project name.','fail');projectNameInput.focus();return;}if(await persistProject(name,'')){closeProjects();}}
    async function projectModalClick(e){const b=e.target.closest('[data-project-action]');if(!b)return;const id=b.dataset.projectId,name=b.dataset.projectName||'';if(b.dataset.projectAction==='load'){await loadServerProject(id);}else if(b.dataset.projectAction==='overwrite'){if(!confirm(`Replace the saved project “${name}” with the current project?`))return;if(await persistProject(name,id))await refreshProjects();}else if(b.dataset.projectAction==='delete'){if(!confirm(`Delete saved project “${name}”?`))return;const r=await fetch(`${cfg.apiBase}/project-delete`,{method:'POST',body:apiForm({project_id:id})});const data=await r.json();if(!r.ok||!data.ok){showIssues(data);return;}if(workspace().project_id===id){delete state.project.workspace;state.dirty=true;renderMeta();}await refreshProjects();toast('Saved project deleted.','success');}}
    async function loadServerProject(id){setStatus('Loading project…');try{const r=await fetch(`${cfg.apiBase}/project-load`,{method:'POST',body:apiForm({project_id:id})});const data=await r.json();if(!r.ok||!data.ok){showIssues(data);return;}resetOverwriteApprovals();state.project=data.project;normalizeProject();state.activeGraphId=state.project.active_graph_id&&state.project.graphs[state.project.active_graph_id]?state.project.active_graph_id:'main';state.graphSelections={};resetHistory();state.dirty=false;localStorage.setItem(cfg.storageKey,JSON.stringify(state.project));closeProjects();renderAll();setStatus(`Loaded: ${data.record.name}`);toast(`Project “${data.record.name}” loaded.`,'success');}catch(err){toast(err.message,'fail');}}
    function formatDate(value){try{return new Date(value).toLocaleString();}catch{return value||'';}}
    function exportProject(){downloadBlob(JSON.stringify(state.project,null,2),`${projectId()}.thunder.json`,'application/json');}
    function importProject(){const file=importInput.files?.[0];if(!file)return;const reader=new FileReader();reader.onload=()=>{try{const p=JSON.parse(String(reader.result));if(p.schema_version!==4)throw new Error('Only schema version 4 projects are supported.');resetOverwriteApprovals();state.project=p;normalizeProject();state.activeGraphId='main';state.graphSelections={};resetHistory();changed();renderAll();toast('Project imported.','success');}catch(err){toast(err.message,'fail');}};reader.readAsText(file);importInput.value='';}

    function openProjectPackageExport(){
        packageIncludeAssets.checked=state.packageIncludeAssets;
        packageIncludeLibraries.checked=state.packageIncludeLibraries;
        packageExportModal.hidden=false;
    }
    function closeProjectPackageExport(){packageExportModal.hidden=true;}
    async function exportProjectPackage(){
        state.packageIncludeAssets=packageIncludeAssets.checked;
        state.packageIncludeLibraries=packageIncludeLibraries.checked;
        localStorage.setItem('tvi_package_include_assets',state.packageIncludeAssets?'1':'0');
        localStorage.setItem('tvi_package_include_libraries',state.packageIncludeLibraries?'1':'0');
        setStatus('Building portable project package…');
        try{
            const response=await fetch(`${cfg.apiBase}/project-package-export`,{
                method:'POST',
                body:apiForm({
                    include_assets:state.packageIncludeAssets?1:0,
                    include_libraries:state.packageIncludeLibraries?1:0
                })
            });
            if(!response.ok){
                const data=await response.json();
                showIssues(data);
                return;
            }
            const blob=await response.blob();
            downloadBlob(blob,`${projectId()}.thunder-project.zip`,'application/zip');
            closeProjectPackageExport();
            setStatus('Project package exported');
            toast('Portable project package exported.','success');
        }catch(error){toast(error.message,'fail');setStatus('Project package export failed');}
    }
    async function importProjectPackage(){
        const file=packageImportInput.files?.[0];
        if(!file)return;
        setStatus('Importing project package…');
        try{
            const form=apiForm();
            form.append('project_package',file,file.name);
            const response=await fetch(`${cfg.apiBase}/project-package-import`,{method:'POST',body:form});
            const data=await response.json();
            if(!response.ok||!data.ok){showIssues(data);return;}
            resetOverwriteApprovals();
            state.project=data.project;
            normalizeProject();
            state.activeGraphId='main';
            state.graphSelections={};
            state.selectedNodeIds=[];
            resetHistory();
            changed();
            renderAll();
            const warnings=Array.isArray(data.warnings)?data.warnings:[];
            toast(`Project package imported. Restored ${data.restored_assets||0} assets and ${data.restored_libraries||0} libraries.${warnings.length?'\n'+warnings.join('\n'):''}`,warnings.length?'warn':'success');
            setStatus('Project package imported');
        }catch(error){toast(error.message,'fail');setStatus('Project package import failed');}
        finally{packageImportInput.value='';}
    }

    async function exportAllIdeData(){
        setStatus('Exporting all IDE data…');
        try{
            const form=apiForm({
                presets:JSON.stringify(loadPresets()),
                recovery_project:localStorage.getItem(cfg.storageKey)||''
            });
            const response=await fetch(`${cfg.apiBase}/ide-data-export`,{method:'POST',body:form});
            if(!response.ok){const data=await response.json();showIssues(data);return;}
            const blob=await response.blob();
            downloadBlob(blob,`thunder-visual-ide-data-${new Date().toISOString().slice(0,10)}.zip`,'application/zip');
            setStatus('All IDE data exported');
            toast('Saved projects, libraries, custom themes, custom snippets, presets, recovery data, and the current project were exported.','success');
        }catch(error){toast(error.message,'fail');setStatus('IDE data export failed');}
    }

    async function importAllIdeData(){
        const file=ideDataImportInput.files?.[0];
        if(!file)return;
        if(!confirm('Importing this backup will replace all server-saved projects, uploaded libraries, custom marketplace assets, presets, and the browser recovery copy. Continue?')){
            ideDataImportInput.value='';
            return;
        }
        setStatus('Importing all IDE data…');
        try{
            const form=new FormData();
            form.append('ide_data_archive',file,file.name);
            const response=await fetch(`${cfg.apiBase}/ide-data-import`,{method:'POST',body:form});
            const data=await response.json();
            if(!response.ok||!data.ok){showIssues(data);return;}
            storePresets(Array.isArray(data.presets)?data.presets:[]);
            const restored=data.current_project||data.recovery_project;
            if(restored){
                localStorage.setItem(cfg.storageKey,JSON.stringify(restored));
                resetOverwriteApprovals();
                state.project=restored;
                normalizeProject();
                state.activeGraphId=state.project.active_graph_id&&state.project.graphs[state.project.active_graph_id]?state.project.active_graph_id:'main';
            }else{
                localStorage.removeItem(cfg.storageKey);
                resetOverwriteApprovals();
                state.project=blankProject();
                state.activeGraphId='main';
            }
            state.graphSelections={};
            state.selectedNodeIds=[];
            state.selectedEdgeId=null;
            resetHistory();
            state.dirty=false;
            await refreshMarketplaceRuntimeAssets();
            renderAll();
            setStatus('All IDE data restored');
            toast(`Restored ${data.projects||0} saved projects, ${data.libraries||0} libraries, ${data.custom_form_themes||0} custom themes, ${data.custom_html_snippets||0} custom snippets, and ${(data.presets||[]).length} presets.`,'success');
        }catch(error){toast(error.message,'fail');setStatus('IDE data import failed');}
        finally{ideDataImportInput.value='';}
    }

    async function clearAllIdeData(){
        if(!confirm('Clear every saved project, uploaded library archive, custom theme, custom snippet, preset, browser recovery copy, and the current workspace? Built-in assets will not be removed.'))return;
        setStatus('Clearing all user-created IDE data…');
        try{
            const response=await fetch(`${cfg.apiBase}/ide-data-clear`,{method:'POST',body:new FormData()});
            const data=await response.json();
            if(!response.ok||!data.ok){showIssues(data);return;}
            localStorage.removeItem(cfg.storageKey);
            localStorage.removeItem(cfg.presetStorageKey);
            resetOverwriteApprovals();
            state.project=blankProject();
            state.activeGraphId='main';
            state.graphSelections={};
            state.selectedNodeIds=[];
            state.selectedEdgeId=null;
            resetHistory();
            state.dirty=false;
            await refreshMarketplaceRuntimeAssets();
            renderAll();
            setStatus('User-created IDE data cleared');
            toast(`Cleared ${data.deleted_projects||0} saved projects, ${data.deleted_libraries||0} uploaded libraries, ${data.deleted_custom_form_themes||0} custom themes, and ${data.deleted_custom_html_snippets||0} custom snippets. Presets and recovery data were also cleared.`,'success');
        }catch(error){toast(error.message,'fail');setStatus('Unable to clear IDE data');}
    }

    async function apiAction(action){if(action==='build'){await buildZip();return;}const previewContext=action==='preview'?capturePreviewContext():null;setStatus(`${action}…`);try{const r=await fetch(`${cfg.apiBase}/${action}`,{method:'POST',body:apiForm()});const data=await r.json();if(!r.ok||!data.ok){showIssues(data);return;}if(action==='preview')openPreview(data,previewContext);else{showIssues(data);toast('Project is valid.','success');}}catch(err){toast(err.message,'fail');setStatus('Request failed');}}
    function apiForm(extra={}){const f=new FormData();f.append('project',JSON.stringify(state.project));for(const [key,value] of Object.entries(extra))f.append(key,String(value));return f;}
    async function buildZip(){setStatus('Building ZIP…');try{const r=await fetch(`${cfg.apiBase}/build`,{method:'POST',body:apiForm()});if(!r.ok){const d=await r.json();showIssues(d);return;}downloadBlob(await r.blob(),`${projectId()}.zip`,'application/zip');setStatus('ZIP built');}catch(err){toast(err.message,'fail');}}
    function selectedTestRoute(){
        if(state.activeGraphId!=='main')return null;
        const routes=selectedNodes().filter(node=>node.type==='routing.route');
        return routes.length===1?routes[0].data:null;
    }
    function overwriteApprovalKey(){
        return projectId();
    }
    function hasOverwriteApproval(){
        return state.overwriteApprovedProjects.has(overwriteApprovalKey());
    }
    function resetOverwriteApprovals(){
        state.overwriteApprovedProjects.clear();
        state.pendingOverwriteAction=null;
        state.pendingOverwriteProjectKey=null;
        overwriteModal.hidden=true;
    }
    function requestOverwriteApproval(action,data={}){
        state.pendingOverwriteAction=action;
        state.pendingOverwriteProjectKey=String(data.plugin_id||overwriteApprovalKey());
        overwriteMessage.textContent=data.error||`A plugin folder named ${state.pendingOverwriteProjectKey} is already there.`;
        overwriteModal.hidden=false;
        setStatus('Ready to replace existing plugin');
    }

    async function testPlugin(override=false, openAfter=true){
        setStatus(openAfter ? 'Deploying test plugin…' : 'Updating test plugin files…');
        try{
            const route=selectedTestRoute();
            const extra=(override||hasOverwriteApproval())?{override_existing:1}:{};
            if(route){
                extra.test_route_name=route.route_name||'';
                extra.test_route_path=route.path||'';
                extra.test_route_method=route.method||'GET';
            }
            const r=await fetch(`${cfg.apiBase}/test`,{method:'POST',body:apiForm(extra)});
            const data=await r.json();
            if(!r.ok||!data.ok){
                if(data.requires_override){
                    requestOverwriteApproval(openAfter?'test-open':'test-update',data);
                    return;
                }
                showIssues(data);
                return;
            }
            overwriteModal.hidden=true;
            if(!openAfter){
                toast(`Test plugin files updated in ${data.folder}.`,'success');
                setStatus('Test plugin files updated');
                return;
            }
            toast(`Test build deployed to ${data.folder}`,'success');
            setStatus('Test plugin deployed');
            const opened=window.open(data.url,'_blank');
            if(!opened){
                toast('The test plugin was deployed, but the browser blocked the new tab. Opening it in this tab.','warn');
                window.location.href=data.url;
            }
        }catch(err){
            toast(err.message,'fail');
            setStatus(openAfter?'Test deployment failed':'Test plugin update failed');
        }
    }
    function cancelTestOverwrite(){
        overwriteModal.hidden=true;
        state.pendingOverwriteAction=null;
        state.pendingOverwriteProjectKey=null;
        setStatus('Replacement cancelled');
    }
    function confirmTestOverwrite(){
        overwriteModal.hidden=true;
        const pending=state.pendingOverwriteAction;
        const projectKey=state.pendingOverwriteProjectKey||overwriteApprovalKey();
        state.pendingOverwriteAction=null;
        state.pendingOverwriteProjectKey=null;
        state.overwriteApprovedProjects.add(projectKey);
        if(pending&&pending.startsWith('migration-')){migrationAction(pending,true);return;}
        if(pending==='test-update'){testPlugin(true,false);return;}
        testPlugin(true,true);
    }
    async function migrationAction(action,override=false){
        const labels={'migration-run':'Running migrations','migration-rollback':'Rolling back migrations','migration-status':'Checking migration status'};
        setStatus(labels[action]||'Migration operation…');
        try{
            const r=await fetch(`${cfg.apiBase}/${action}`,{method:'POST',body:apiForm((override||hasOverwriteApproval())?{override_existing:1}:{})});
            const data=await r.json();
            if(!r.ok||!data.ok){
                if(data.requires_override){requestOverwriteApproval(action,data);return;}
                if(data.output){openMigrationOutput(action,data);toast('Migration command reported an error.','fail');setStatus('Migration command failed');return;}
                showIssues(data);return;
            }
            openMigrationOutput(action,data);
            setStatus(data.ok?'Migration command completed':'Migration command failed');
        }catch(err){toast(err.message,'fail');setStatus('Migration request failed');}
    }
    function openMigrationOutput(action,data){const titles={'migration-run':'Run Migrations','migration-rollback':'Roll Back Migrations','migration-status':'Migration Status'};migrationOutputTitle.textContent=titles[action]||'Migration Output';migrationOutputSummary.textContent=`Plugin: ${data.plugin_id||projectId()}`;migrationOutput.textContent=String(data.output||'No output returned.');migrationOutputModal.hidden=false;}
    function closeMigrationOutput(){migrationOutputModal.hidden=true;}
    function showIssues(data){const errors=data.errors||[data.error].filter(Boolean),warnings=data.warnings||[];if(errors.length)toast(errors.join('\n'),'fail');else if(warnings.length)toast(warnings.join('\n'),'warn');setStatus(errors.length?'Validation failed':warnings.length?'Valid with warnings':'Valid');}
    function normalizePreviewPath(path){return String(path||'').replace(/\\/g,'/').replace(/^\/+|\/+$/g,'').replace(/\/{2,}/g,'/');}
    function previewDirectoryForPath(path){const normalized=normalizePreviewPath(path);const index=normalized.lastIndexOf('/');return index<0?'':normalized.slice(0,index);}
    function previewBasename(path){const normalized=normalizePreviewPath(path);return normalized.split('/').pop()||normalized;}
    function previewParentDirectory(path){const normalized=normalizePreviewPath(path);if(!normalized)return'';const index=normalized.lastIndexOf('/');return index<0?'':normalized.slice(0,index);}
    function projectNodeByType(type){return mainGraph().nodes.find(node=>node.type===type)||null;}
    function connectedArchitectureNode(node,type){if(!node)return null;const graph=mainGraph();for(const edge of graph.edges||[]){if(edge.from!==node.id&&edge.to!==node.id)continue;const other=graph.nodes.find(candidate=>candidate.id===(edge.from===node.id?edge.to:edge.from));if(other?.type===type)return other;}return null;}
    function cleanPreviewClassName(value,fallback='GeneratedClass'){const cleaned=String(value||'').replace(/[^A-Za-z0-9_]/g,'');return cleaned||fallback;}
    function cleanMigrationSlug(value){return String(value||'migration').replace(/[^A-Za-z0-9_]+/g,'_').replace(/^_+|_+$/g,'').toLowerCase()||'migration';}
    function previewPathForArchitectureNode(node){
        if(!node)return'';
        const data=node.data||{};
        if(node.type==='project.plugin')return'config.json';
        if(['routing.route','routing.route_scope','authorization.all_permissions','authorization.all_roles'].includes(node.type))return'config.json';
        if(['lifecycle.controller','authorization.set_permissions','authorization.set_roles'].includes(node.type))return'plugin.php';
        if(node.type==='custom.function_definition')return'functions.php';
        if(node.type==='models.model')return`models/${cleanPreviewClassName(data.class_name,'Model')}.php`;
        if(node.type==='custom.class_definition')return`classes/${cleanPreviewClassName(data.class_name,'CustomClass')}.php`;
        if(node.type==='migration.definition'){
            const timestamp=String(data.timestamp||'').replace(/[^0-9_-]/g,'');
            const prefix=timestamp||'';
            const suffix=cleanMigrationSlug(data.filename_slug||'migration');
            return prefix?`migrations/${prefix}_${suffix}.php`:`migrations/${suffix}`;
        }
        if(node.type==='looks.look')return`looks/${String(data.folder||'main').replace(/^\/+|\/+$/g,'')||'main'}/look.json`;
        if(['looks.asset','looks.text_asset'].includes(node.type)){
            const look=connectedArchitectureNode(node,'looks.look');
            const folder=String(look?.data?.folder||'main').replace(/^\/+|\/+$/g,'')||'main';
            return`looks/${folder}/${normalizePreviewPath(data.filename||'assets/file.txt')}`;
        }
        if(['lifecycle.view','lifecycle.reusable_view'].includes(node.type)){
            const look=connectedArchitectureNode(node,'looks.look');
            const folder=String(look?.data?.folder||projectNodeByType('project.plugin')?.data?.look||'main').replace(/^\/+|\/+$/g,'')||'main';
            const fallback=node.type==='lifecycle.reusable_view'?'frontend/includes/reusable-view.php':'frontend/page.php';
            return`looks/${folder}/${normalizePreviewPath(data.filename||fallback)}`;
        }
        return'';
    }
    function previewJavaScriptPath(owner){
        if(!owner||!['lifecycle.view','lifecycle.reusable_view'].includes(owner.type))return'';
        const data=owner.data||{};const look=connectedArchitectureNode(owner,'looks.look');
        const folder=String(look?.data?.folder||projectNodeByType('project.plugin')?.data?.look||'main').replace(/^\/+|\/+$/g,'')||'main';
        const viewPath=normalizePreviewPath(data.filename||(owner.type==='lifecycle.reusable_view'?'frontend/includes/reusable-view.php':'frontend/page.php'));
        const filename=(viewPath.split('/').pop()||'view').replace(/\.[^.]+$/,'');
        const suffix=String(owner.id||'view').replace(/[^A-Za-z0-9_-]+/g,'-').slice(-8);
        const slug=`${filename}-${suffix}`.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'')||'view';
        return`looks/${folder}/assets/js/thunder-view-${slug}.js`;
    }
    function previewOwnerForGraph(graph){return graph?.owner_node_id?projectNodeById(graph.owner_node_id):null;}
    function capturePreviewContext(){
        const graph=activeGraph();
        const selected=selectedNodes()[0]||null;
        const owner=previewOwnerForGraph(graph);
        let preferred='';
        if(graph.kind==='flow'){
            if(owner?.type==='lifecycle.controller'||['authorization.set_permissions','authorization.set_roles'].includes(owner?.type))preferred='plugin.php';
            else if(owner?.type==='custom.function_definition')preferred='functions.php';
            else preferred=previewPathForArchitectureNode(owner);
        }else if(graph.kind==='javascript')preferred=previewJavaScriptPath(owner);
        else if(graph.kind==='view'||graph.kind==='migration')preferred=previewPathForArchitectureNode(owner);
        else preferred=previewPathForArchitectureNode(selected);
        if(!preferred&&graph.kind==='architecture')preferred=previewPathForArchitectureNode(selected)||'config.json';
        if(!preferred)preferred=graph.kind==='flow'?'plugin.php':graph.kind==='javascript'?previewPathForArchitectureNode(owner):'config.json';
        return{graphId:state.activeGraphId,graphKind:graph.kind,selectedNodeId:selected?.id||'',ownerNodeId:owner?.id||'',preferredPath:normalizePreviewPath(preferred)};
    }
    function closestPreviewFileIndex(preferredPath){
        const wanted=normalizePreviewPath(preferredPath);
        if(!state.preview.length)return-1;
        if(wanted){
            let index=state.preview.findIndex(file=>normalizePreviewPath(file.path)===wanted);
            if(index>=0)return index;
            const wantedBase=previewBasename(wanted);
            index=state.preview.findIndex(file=>previewBasename(file.path)===wantedBase);
            if(index>=0)return index;
            const directory=previewDirectoryForPath(wanted);
            if(directory){
                const stem=wantedBase.replace(/\.[^.]+$/,'');
                index=state.preview.findIndex(file=>previewDirectoryForPath(file.path)===directory&&previewBasename(file.path).includes(stem));
                if(index>=0)return index;
                index=state.preview.findIndex(file=>previewDirectoryForPath(file.path)===directory);
                if(index>=0)return index;
            }else if(!wanted.includes('.')){
                index=state.preview.findIndex(file=>normalizePreviewPath(file.path).startsWith(`${wanted}/`));
                if(index>=0)return index;
            }
        }
        const defaults=state.previewContext?.graphKind==='flow'?['plugin.php','functions.php']:['config.json','plugin.php'];
        for(const path of defaults){const index=state.preview.findIndex(file=>normalizePreviewPath(file.path)===path);if(index>=0)return index;}
        return 0;
    }
    function previewFolderEntries(directory){
        const current=normalizePreviewPath(directory);
        const prefix=current?`${current}/`:'';
        const folders=new Set();
        const files=[];
        state.preview.forEach((file,index)=>{
            const path=normalizePreviewPath(file.path);
            if(prefix&&!path.startsWith(prefix))return;
            const remainder=prefix?path.slice(prefix.length):path;
            if(!remainder||remainder.includes('/')){const folder=remainder.split('/')[0];if(folder)folders.add(folder);return;}
            files.push({index,file,name:remainder});
        });
        return{
            folders:[...folders].sort((a,b)=>a.localeCompare(b,undefined,{numeric:true,sensitivity:'base'})),
            files:files.sort((a,b)=>a.name.localeCompare(b.name,undefined,{numeric:true,sensitivity:'base'}))
        };
    }
    function previewFileMeta(file){
        const content=String(file?.content||'');
        if(content==='[binary asset]')return'Binary asset';
        const lines=content===''?0:content.split(/\r?\n/).length;
        const bytes=new Blob([content]).size;
        return`${lines} line${lines===1?'':'s'} · ${bytes<1024?bytes+' B':(bytes/1024).toFixed(bytes<10240?1:0)+' KB'}`;
    }
    function renderPreviewBreadcrumbs(){
        if(!previewBreadcrumbs)return;
        const parts=normalizePreviewPath(state.previewDirectory).split('/').filter(Boolean);
        const crumbs=[{label:'Plugin root',path:''}];
        let path='';
        parts.forEach(part=>{path=path?`${path}/${part}`:part;crumbs.push({label:part,path});});
        previewBreadcrumbs.innerHTML=crumbs.map((crumb,index)=>`${index?'<span aria-hidden="true">›</span>':''}<button type="button" data-preview-directory="${attr(crumb.path)}" ${index===crumbs.length-1?'aria-current="page"':''}>${esc(crumb.label)}</button>`).join('');
        if(previewUp)previewUp.disabled=!state.previewDirectory;
    }
    function renderPreviewBrowser(){
        renderPreviewBreadcrumbs();
        const entries=previewFolderEntries(state.previewDirectory);
        const folderHtml=entries.folders.map(folder=>{
            const path=state.previewDirectory?`${state.previewDirectory}/${folder}`:folder;
            const count=state.preview.filter(file=>normalizePreviewPath(file.path).startsWith(`${path}/`)).length;
            return`<button type="button" class="tvi-preview-entry tvi-preview-entry--folder" data-preview-directory="${attr(path)}"><span class="tvi-preview-entry__icon" aria-hidden="true">▰</span><span><strong>${esc(folder)}</strong><small>${count} generated item${count===1?'':'s'}</small></span><span class="tvi-preview-entry__chevron" aria-hidden="true">›</span></button>`;
        }).join('');
        const fileHtml=entries.files.map(({index,file,name})=>`<button type="button" class="tvi-preview-entry tvi-preview-entry--file ${index===state.previewFileIndex?'is-active':''}" data-preview-index="${index}"><span class="tvi-preview-entry__icon" aria-hidden="true">${esc(previewFileIcon(name))}</span><span><strong>${esc(name)}</strong><small>${esc(previewFileMeta(file))}</small></span></button>`).join('');
        previewFiles.innerHTML=folderHtml+fileHtml||'<div class="tvi-preview-browser__empty">This folder has no generated files.</div>';
    }
    function previewFileIcon(path){const ext=String(path).split('.').pop().toLowerCase();return({php:'PHP',json:'{}',js:'JS',css:'CSS',html:'<>',htm:'<>',svg:'SVG',md:'MD'}[ext]||'FILE');}
    function navigatePreviewDirectory(directory){state.previewDirectory=normalizePreviewPath(directory);renderPreviewBrowser();}
    function previewBrowserClick(event){
        const directoryButton=event.target.closest('[data-preview-directory]');
        if(directoryButton){navigatePreviewDirectory(directoryButton.dataset.previewDirectory||'');return;}
        const fileButton=event.target.closest('[data-preview-index]');
        if(fileButton)showPreviewFile(Number(fileButton.dataset.previewIndex));
    }
    function openPreview(data,context=null){
        state.preview=(data.files||[]).map(file=>({...file,path:normalizePreviewPath(file.path)})).sort((a,b)=>a.path.localeCompare(b.path,undefined,{numeric:true,sensitivity:'base'}));
        state.previewContext=context||capturePreviewContext();
        state.previewFileIndex=closestPreviewFileIndex(state.previewContext?.preferredPath||'');
        const selected=state.preview[state.previewFileIndex];
        state.previewDirectory=selected?previewDirectoryForPath(selected.path):'';
        previewSummary.textContent=`${data.summary?.files||0} files · ${data.summary?.nodes||0} nodes${selected?' · opened '+selected.path:''}`;
        previewModal.hidden=false;
        renderPreviewBrowser();
        requestAnimationFrame(()=>state.previewEditor?.refresh());
        if(state.previewFileIndex>=0)showPreviewFile(state.previewFileIndex,false);
    }
    function showPreviewFile(i,changeDirectory=true){
        const f=state.preview[i];if(!f)return;
        state.previewFileIndex=i;
        if(changeDirectory)state.previewDirectory=previewDirectoryForPath(f.path);
        previewPath.innerHTML=`<span>${esc(f.path)}</span><small>${esc(previewFileMeta(f))}</small>`;
        if(state.previewEditor){state.previewEditor.setOption('mode',previewLanguage(f.path));state.previewEditor.setValue(String(f.content||''));state.previewEditor.clearHistory();requestAnimationFrame(()=>{state.previewEditor.refresh();state.previewEditor.scrollTo(0,0);});}else{previewCode.value=String(f.content||'');}
        renderPreviewBrowser();
    }
    function previewLanguage(path){const ext=String(path).split('.').pop().toLowerCase();if(['php','phtml'].includes(ext))return'application/x-httpd-php';if(ext==='json')return{name:'javascript',json:true};return({js:'javascript',css:'css',html:'htmlmixed',htm:'htmlmixed',xml:'xml',svg:'xml',md:'markdown'}[ext]||'text/plain');}
    function closePreview(){previewModal.hidden=true;state.previewDirectory='';state.previewFileIndex=-1;state.previewContext=null;}

    function closeTopModal(){for(const [modal,close] of [[htmlSnippetsModal,closeHtmlSnippets],[formThemeModal,closeFormTheme],[nodeExportModal,closeNodeBundleExport],[htmlDesignerModal,()=>closeHtmlDesigner()],[samplesModal,closeSamples],[paginationTemplatesModal,closePaginationTemplates],[migrationOutputModal,closeMigrationOutput],[settingsModal,closeSettings],[overwriteModal,cancelTestOverwrite],[projectsModal,closeProjects],[codeModal,closeCodeEditor],[methodBrowserModal,closeMethodBrowser],[managerEditorModal,closeManagerEditor],[methodsModal,closeMethods],[libraryModal,closeLibrary],[presetsModal,closePresets],[previewModal,closePreview]]){if(modal&&!modal.hidden){close();return true;}}return false;}

    function resetHistory(){state.history=[clone(state.project)];state.historyIndex=0;state.changeBase=null;clearTimeout(state.historyTimer);}
    function snapshot(){if(!state.changeBase)state.changeBase=clone(state.project);}
    function snapshotDebounced(){if(!state.changeBase)state.changeBase=clone(state.project);clearTimeout(state.historyTimer);state.historyTimer=setTimeout(()=>{pushHistory(clone(state.project));state.changeBase=null;},350);}
    function pushHistory(project){const serialized=JSON.stringify(project);const current=state.history[state.historyIndex];if(current&&JSON.stringify(current)===serialized)return;state.history=state.history.slice(0,state.historyIndex+1);state.history.push(project);state.historyIndex=state.history.length-1;if(state.history.length>80){state.history.shift();state.historyIndex--;}}
    function undo(){if(state.historyIndex<=0)return;state.historyIndex--;state.project=clone(state.history[state.historyIndex]);normalizeProject();if(!state.project.graphs[state.activeGraphId])state.activeGraphId='main';renderAll();}
    function redo(){if(state.historyIndex>=state.history.length-1)return;state.historyIndex++;state.project=clone(state.history[state.historyIndex]);normalizeProject();renderAll();}
    function changed(mark=true){if(mark){state.dirty=true;if(state.changeBase){pushHistory(clone(state.project));state.changeBase=null;}}state.project.active_graph_id=state.activeGraphId;renderMeta();}

    function setStatus(text){document.getElementById('tvi-status').textContent=text;}
    function toast(text,type='success'){const el=document.createElement('div');el.className=`tvi-toast is-${type}`;el.textContent=text;document.getElementById('tvi-toast-region').appendChild(el);setTimeout(()=>el.remove(),5000);}
    function projectId(){const p=mainGraph().nodes.find(n=>n.type==='project.plugin');return String(p?.data?.id||'thunder-plugin').replace(/[^a-z0-9_-]+/gi,'-');}
    function downloadBlob(content,name,type){const blob=content instanceof Blob?content:new Blob([content],{type});const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download=name;a.click();setTimeout(()=>URL.revokeObjectURL(a.href),1000);}
    function findNodeInProject(project,id,graphId){return project.graphs[graphId]?.nodes.find(n=>n.id===id);}
    function parseJson(v,fallback){try{const x=typeof v==='string'?JSON.parse(v):v;return Array.isArray(fallback)?(Array.isArray(x)?x:fallback):(x&&typeof x==='object'?x:fallback);}catch{return fallback;}}
    function sanitize(v){return String(v).replace(/[^A-Za-z0-9_]/g,'');}
    function sanitizeMethodName(v){return sanitize(v);}
    function clamp(v,min,max){return Math.min(max,Math.max(min,v));}
    function esc(v){return String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));}
    function attr(v){return esc(v);}
    function cssEscape(v){return window.CSS?.escape?CSS.escape(String(v)):String(v).replace(/["\\]/g,'\\$&');}

    init();
})();
