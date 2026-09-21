<?php

declare(strict_types=1);

namespace ThunderVisualIde;

$asset_version = rawurlencode((string) ($plugin_info['version'] ?? '0.12.97'));
?>
<link rel="icon" type="image/jpeg" href="<?= current_look_http('assets/images/plugin.jpg') ?>">
<link rel="stylesheet" href="<?= current_look_http('assets/css/editor.css') ?>?v=<?= htmlspecialchars($asset_version, ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= current_look_http('assets/vendor/codemirror/lib/codemirror.css') ?>">
<link rel="stylesheet" href="<?= current_look_http('assets/vendor/codemirror/theme/monokai.css') ?>">
<link rel="stylesheet" href="<?= current_look_http('assets/vendor/codemirror/theme/eclipse.css') ?>">
<link rel="stylesheet" href="<?= current_look_http('assets/vendor/codemirror/theme/neo.css') ?>">
<link rel="stylesheet" href="<?= current_look_http('assets/vendor/codemirror/theme/material-darker.css') ?>">
<link rel="stylesheet" href="<?= current_look_http('assets/vendor/codemirror/theme/darcula.css') ?>">
<link rel="stylesheet" href="<?= current_look_http('assets/vendor/codemirror/theme/nord.css') ?>">

<svg class="tvi-icon-sprite" aria-hidden="true" focusable="false">
    <symbol id="tvi-icon-back" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/><path d="M9 12h10"/></symbol>
    <symbol id="tvi-icon-zoom-out" viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M7.5 10.5h6"/><path d="M15.5 15.5L21 21"/></symbol>
    <symbol id="tvi-icon-zoom-in" viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M7.5 10.5h6M10.5 7.5v6"/><path d="M15.5 15.5L21 21"/></symbol>
    <symbol id="tvi-icon-fit" viewBox="0 0 24 24"><path d="M8 3H3v5M16 3h5v5M8 21H3v-5M16 21h5v-5"/><path d="M8 8h8v8H8z"/></symbol>
    <symbol id="tvi-icon-eye" viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/><circle cx="12" cy="12" r="2.5"/></symbol>
    <symbol id="tvi-icon-expand" viewBox="0 0 24 24"><path d="M8 3H3v5M16 3h5v5M8 21H3v-5M16 21h5v-5"/></symbol>
    <symbol id="tvi-icon-contract" viewBox="0 0 24 24"><path d="M9 9H4V4M15 9h5V4M9 15H4v5M15 15h5v5"/></symbol>
    <symbol id="tvi-icon-save" viewBox="0 0 24 24"><path d="M4 3h13l3 3v15H4z"/><path d="M7 3v6h9V3M8 21v-7h8v7"/></symbol>
    <symbol id="tvi-icon-download" viewBox="0 0 24 24"><path d="M12 3v12M7 10l5 5 5-5"/><path d="M4 19h16"/></symbol>
    <symbol id="tvi-icon-upload" viewBox="0 0 24 24"><path d="M12 21V9M7 14l5-5 5 5"/><path d="M4 5h16"/></symbol>
    <symbol id="tvi-icon-snippets" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></symbol>
    <symbol id="tvi-icon-trash" viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3M7 7l1 14h8l1-14"/><path d="M10 11v6M14 11v6"/></symbol>
    <symbol id="tvi-icon-code" viewBox="0 0 24 24"><path d="M8 7l-5 5 5 5M16 7l5 5-5 5M14 4l-4 16"/></symbol>
    <symbol id="tvi-icon-preset" viewBox="0 0 24 24"><path d="M4 4h16v16H4z"/><path d="M8 4v6h8V4M8 20v-6h8v6"/></symbol>
    <symbol id="tvi-icon-left" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></symbol>
    <symbol id="tvi-icon-right" viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></symbol>
    <symbol id="tvi-icon-settings" viewBox="0 0 24 24"><path d="M4 7h10M18 7h2M4 17h2M10 17h10M14 4v6M6 14v6"/></symbol>
    <symbol id="tvi-icon-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
    <symbol id="tvi-icon-close" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></symbol>
    <symbol id="tvi-icon-pan" viewBox="0 0 24 24"><path d="M12 2v20M2 12h20"/><path d="M8 6l4-4 4 4M8 18l4 4 4-4M6 8l-4 4 4 4M18 8l4 4-4 4"/></symbol>
</svg>
<div class="tvi-app" id="tvi-app">
    <header class="tvi-toolbar">
        <div class="tvi-brand"><span class="tvi-brand__mark"><img src="<?= current_look_http('assets/images/plugin.jpg') ?>" alt=""></span><div><strong>Thunder Visual IDE</strong><small>Node-based visual programming</small></div></div>
        <nav class="tvi-menubar" aria-label="IDE menus">
            <div class="tvi-menu">
                <button type="button" class="tvi-menu__toggle" data-menu-toggle="file">File <span>▾</span></button>
                <div class="tvi-menu__panel" data-menu-panel="file" hidden>
                    <button type="button" data-action="new">New Project</button>
                    <button type="button" data-action="sample">Browse Samples…</button>
                    <button type="button" data-action="save">Save Project</button>
                    <button type="button" data-action="save-as">Save Project As…</button>
                    <button type="button" data-action="load-project">Load Saved Project…</button>
                    <hr>
                    <button type="button" data-action="save-browser">Save Browser Recovery Copy</button>
                    <button type="button" data-action="import">Import Project JSON</button>
                    <button type="button" data-action="export">Export Project JSON</button>
                    <hr>
                    <button type="button" data-action="import-project-package">Import Project Package ZIP</button>
                    <button type="button" data-action="open-project-package-export">Export Project Package ZIP…</button>
                    <hr>
                    <button type="button" data-action="export-all-ide-data">Export All IDE Data ZIP</button>
                    <button type="button" data-action="import-all-ide-data">Import All IDE Data ZIP</button>
                    <button type="button" class="tvi-danger" data-action="clear-all-ide-data">Clear All User Data…</button>
                </div>
            </div>
            <div class="tvi-menu">
                <button type="button" class="tvi-menu__toggle" data-menu-toggle="edit">Edit <span>▾</span></button>
                <div class="tvi-menu__panel" data-menu-panel="edit" hidden>
                    <button type="button" data-action="undo">Undo</button>
                    <button type="button" data-action="redo">Redo</button>
                    <hr>
                    <button type="button" data-action="copy-selected">Copy Selected <span class="tvi-shortcut">Ctrl/Cmd+C</span></button>
                    <button type="button" data-action="paste-nodes">Paste <span class="tvi-shortcut">Ctrl/Cmd+V</span></button>
                    <hr>
                    <button type="button" data-action="group-selected">Group Selected</button>
                    <button type="button" data-action="save-preset">Save Selection as Preset</button>
                    <button type="button" data-action="open-presets">Manage Presets</button>
                    <hr>
                    <button type="button" data-action="export-selected-node-file">Export Selected Nodes to File…</button>
                    <button type="button" data-action="import-node-file">Import Nodes from File…</button>
                </div>
            </div>
            <div class="tvi-menu">
                <button type="button" class="tvi-menu__toggle" data-menu-toggle="nodes">Nodes <span>▾</span></button>
                <div class="tvi-menu__panel" data-menu-panel="nodes" hidden>
                    <button type="button" data-action="open-library">Open Expanded Library</button>
                    <button type="button" data-action="open-pagination-templates">Pagination Designs…</button>
                    <button type="button" data-action="open-html-designer">HTML Component Designer…</button>
                    <button type="button" data-action="reload-definitions" title="Reload node.json and Compiler.php files">Reload Node Definitions</button>
                </div>
            </div>
            <div class="tvi-menu">
                <button type="button" class="tvi-menu__toggle" data-menu-toggle="marketplace">Marketplace <span>▾</span></button>
                <div class="tvi-menu__panel" data-menu-panel="marketplace" hidden>
                    <button type="button" data-action="open-marketplace-studio">Asset Studio…</button>
                    <small class="tvi-menu__note">Create, import and export custom form themes and HTML snippets.</small>
                </div>
            </div>
            <div class="tvi-menu">
                <button type="button" class="tvi-menu__toggle" data-menu-toggle="database">Database <span>▾</span></button>
                <div class="tvi-menu__panel" data-menu-panel="database" hidden>
                    <button type="button" data-action="migration-run">Run Migrations</button>
                    <button type="button" data-action="migration-rollback">Roll Back Migrations</button>
                    <hr>
                    <button type="button" data-action="migration-status">Migration Status</button>
                </div>
            </div>
            <div class="tvi-menu">
                <button type="button" class="tvi-menu__toggle" data-menu-toggle="settings">Settings <span>▾</span></button>
                <div class="tvi-menu__panel" data-menu-panel="settings" hidden>
                    <button type="button" data-action="open-settings">Editor Preferences…</button>
                </div>
            </div>
            <div class="tvi-menu">
                <button type="button" class="tvi-menu__toggle" data-menu-toggle="help">Help <span>▾</span></button>
                <div class="tvi-menu__panel tvi-menu__panel--right" data-menu-panel="help" hidden>
                    <a href="<?= htmlspecialchars(rtrim((string) ROOT, '/') . '/thunder-ide/tutorials', ENT_QUOTES, 'UTF-8') ?>">Tutorials</a>
                    <button type="button" data-action="open-about">About Thunder Visual IDE</button>
                </div>
            </div>
            <div class="tvi-menu">
                <button type="button" class="tvi-menu__toggle" data-menu-toggle="build">Build &amp; Export <span>▾</span></button>
                <div class="tvi-menu__panel tvi-menu__panel--right" data-menu-panel="build" hidden>
                    <button type="button" data-action="validate">Validate Project</button>
                    <button type="button" data-action="preview">Preview Generated Code</button>
                    <button type="button" data-action="test-plugin">Deploy Test Plugin</button>
                    <button type="button" data-action="update-test-plugin">Update Test Plugin Files</button>
                    <hr>
                    <button type="button" class="tvi-menu__primary" data-action="build">Build Plugin ZIP</button>
                </div>
            </div>
        </nav>
        <div class="tvi-toolbar__group tvi-toolbar__group--workspace">
            <button type="button" class="tvi-icon-button tvi-panel-toggle" data-action="toggle-node-library" id="tvi-toggle-node-library" title="Hide node library" aria-label="Hide node library" aria-pressed="true"><svg class="tvi-icon"><use href="#tvi-icon-snippets"></use></svg></button>
            <button type="button" class="tvi-icon-button tvi-panel-toggle" data-action="toggle-inspector" id="tvi-toggle-inspector" title="Hide inspector" aria-label="Hide inspector" aria-pressed="true"><svg class="tvi-icon"><use href="#tvi-icon-settings"></use></svg></button>
            <button type="button" class="tvi-icon-button" data-action="back-graph" id="tvi-back-graph" title="Back to main graph" aria-label="Back to main graph" hidden><svg class="tvi-icon"><use href="#tvi-icon-back"></use></svg></button>
            <button type="button" class="tvi-icon-button" data-action="zoom-out" title="Zoom out" aria-label="Zoom out"><svg class="tvi-icon"><use href="#tvi-icon-zoom-out"></use></svg></button>
            <button type="button" data-action="zoom-reset" id="tvi-zoom-label" title="Reset zoom">100%</button>
            <button type="button" class="tvi-icon-button" data-action="zoom-in" title="Zoom in" aria-label="Zoom in"><svg class="tvi-icon"><use href="#tvi-icon-zoom-in"></use></svg></button>
        </div>
    </header>

    <div class="tvi-main" id="tvi-main">
        <aside class="tvi-palette" id="tvi-node-library-panel">
            <div class="tvi-panel-title"><span>Node Library</span><span class="tvi-panel-title__actions"><button type="button" class="tvi-panel-expand tvi-icon-button" data-action="open-library" title="Open expanded node library" aria-label="Open expanded node library"><svg class="tvi-icon"><use href="#tvi-icon-expand"></use></svg></button><button type="button" class="tvi-icon-button" data-action="toggle-node-library" title="Hide node library" aria-label="Hide node library"><svg class="tvi-icon"><use href="#tvi-icon-left"></use></svg></button></span></div>
            <label class="tvi-search"><span>⌕</span><input id="tvi-node-search" type="search" placeholder="Search nodes"></label>
            <div id="tvi-palette-list"></div>
            <div class="tvi-palette-help">Click an output socket, then a compatible input socket. Double-click Controllers, Functions, Models, Classes and Views to open their nested graphs. Right-click nodes, connections or empty canvas for quick actions. Drag empty canvas to multi-select. Hold Space and drag to pan.</div>
        </aside>

        <section class="tvi-workspace">
            <div class="tvi-canvas-toolbar">
                <div><strong id="tvi-graph-title">Plugin Architecture</strong><span id="tvi-graph-kind">Architecture</span></div>
                <div class="tvi-canvas-toolbar__actions">
                    <span id="tvi-selection-hint">Select a node to edit it</span>
                    <div class="tvi-pan-joystick" id="tvi-pan-joystick" role="button" tabindex="0" title="Drag in any direction to pan the graph" aria-label="Drag in any direction to pan the graph"><svg class="tvi-icon"><use href="#tvi-icon-pan"></use></svg><span class="tvi-pan-joystick__thumb" aria-hidden="true"></span></div>
                    <button type="button" class="tvi-icon-button" data-action="fit-all" title="Fit all visible nodes into the viewport" aria-label="Fit all visible nodes into the viewport"><svg class="tvi-icon"><use href="#tvi-icon-fit"></use></svg></button>
                    <button type="button" class="tvi-icon-button" data-action="show-all" title="Show all nodes" aria-label="Show all nodes"><svg class="tvi-icon"><use href="#tvi-icon-eye"></use></svg></button>
                </div>
            </div>
            <div class="tvi-viewport" id="tvi-viewport" tabindex="0" oncontextmenu="return false;">
                <div class="tvi-graph-context-banner" id="tvi-graph-context-banner" hidden>
                    <span id="tvi-graph-context-type">Editing graph</span>
                    <strong id="tvi-graph-context-name"></strong>
                </div>
                <div class="tvi-world" id="tvi-world"><svg class="tvi-connections" id="tvi-connections"></svg><div class="tvi-nodes" id="tvi-nodes"></div></div>
                <div class="tvi-empty-state" id="tvi-empty-state"><strong>Build with ThunderPHP concepts</strong><span>Drag a node here or choose a guided sample project.</span><button type="button" data-action="sample">Browse Samples…</button></div>
            </div>
        </section>

        <aside class="tvi-inspector" id="tvi-inspector-panel">
            <div class="tvi-panel-title"><span>Inspector</span><button type="button" class="tvi-icon-button" data-action="toggle-inspector" title="Hide inspector" aria-label="Hide inspector"><svg class="tvi-icon"><use href="#tvi-icon-right"></use></svg></button></div>
            <div id="tvi-inspector-content" class="tvi-inspector-content"></div>
        </aside>
    </div>

    <footer class="tvi-statusbar"><div id="tvi-status">Ready</div><div class="tvi-statusbar__right"><span id="tvi-node-count">0 nodes</span><span id="tvi-edge-count">0 connections</span><span id="tvi-save-state">Not saved</span></div></footer>
</div>

<input type="file" id="tvi-import-input" accept="application/json,.json" hidden>
<input type="file" id="tvi-package-import-input" accept="application/zip,.zip" hidden>
<input type="file" id="tvi-data-import-input" accept="application/zip,.zip" hidden>
<input type="file" id="tvi-preset-import-input" accept="application/json,.json" hidden>
<input type="file" id="tvi-node-bundle-import-input" accept="application/json,.json,.thunder-nodes.json" hidden>
<input type="file" id="tvi-html-component-import-input" accept="application/json,.json" hidden>
<input type="file" id="tvi-marketplace-package-import" accept="application/zip,.zip" hidden>


<div class="tvi-modal" id="tvi-samples-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-samples"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--samples">
        <header class="tvi-modal__head"><div><strong>Sample Projects</strong><small>Choose a focused project to inspect, modify, compile, and use as a learning reference.</small></div><button type="button" data-action="close-samples">×</button></header>
        <div class="tvi-samples-grid" id="tvi-samples-list"></div>
    </div>
</div>

<div class="tvi-modal" id="tvi-preview-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-preview"></div>
    <div class="tvi-modal__dialog">
        <header class="tvi-modal__head"><div><strong>Generated Plugin Preview</strong><small id="tvi-preview-summary"></small></div><button type="button" data-action="close-preview">×</button></header>
        <div class="tvi-preview">
            <aside class="tvi-preview-browser">
                <div class="tvi-preview-browser__head">
                    <div><strong>Generated files</strong><small>Browse one folder at a time</small></div>
                    <button type="button" id="tvi-preview-up" title="Open parent folder" aria-label="Open parent folder">↑</button>
                </div>
                <nav class="tvi-preview-breadcrumbs" id="tvi-preview-breadcrumbs" aria-label="Generated file path"></nav>
                <div class="tvi-preview-files" id="tvi-preview-files"></div>
            </aside>
            <section><div class="tvi-preview__path" id="tvi-preview-path">No file selected</div><textarea id="tvi-preview-code" spellcheck="false"></textarea></section>
        </div>
    </div>
</div>

<div class="tvi-modal" id="tvi-code-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-code-editor"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--editor">
        <header class="tvi-modal__head"><div><strong id="tvi-code-modal-title">Code Editor</strong><small>Syntax highlighting · Ctrl/Cmd+Shift+F selects the next occurrence · Ctrl/Cmd+D duplicates lines · Alt-click adds cursors · Ctrl/Cmd+Alt+↑/↓ adds cursors vertically.</small></div><div class="tvi-modal__actions"><button type="button" data-action="save-code-editor">Save</button><button type="button" data-action="close-code-editor">×</button></div></header>
        <textarea id="tvi-code-modal-textarea" class="tvi-full-code-editor" spellcheck="false"></textarea>
    </div>
</div>

<div class="tvi-modal" id="tvi-methods-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-methods"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--methods">
        <header class="tvi-modal__head"><div><strong id="tvi-methods-title">Methods</strong><small id="tvi-methods-summary">Manage method signatures without editing JSON.</small></div><button type="button" data-action="close-methods">×</button></header>
        <div class="tvi-methods-layout">
            <section class="tvi-methods-list"><div class="tvi-subhead"><strong>Methods</strong><div class="tvi-modal__actions"><button type="button" id="tvi-add-model-defaults" data-action="add-model-defaults" hidden>Add Query Builder Defaults</button><button type="button" data-action="new-method">+ New</button></div></div><div id="tvi-methods-list"></div></section>
            <section class="tvi-method-form">
                <div class="tvi-method-form-grid">
                    <label class="tvi-field"><span>Method name</span><input id="tvi-method-name" type="text" placeholder="findByEmail"></label>
                    <label class="tvi-field"><span>Return type <em>optional</em></span><input id="tvi-method-return-type" type="text" placeholder="array, ?User, bool"></label>
                    <label class="tvi-field"><span>Implementation</span><select id="tvi-method-implementation"><option value="flow">Execution graph</option><option value="body">PHP body</option></select></label>
                    <label class="tvi-field tvi-class-method-option" id="tvi-method-visibility-field" hidden><span>Visibility</span><select id="tvi-method-visibility"><option value="public">public</option><option value="protected">protected</option><option value="private">private</option></select></label>
                    <label class="tvi-field tvi-field--checkbox tvi-class-method-option" id="tvi-method-static-field" hidden><span>Static method</span><input id="tvi-method-static" type="checkbox"></label>
                    <label class="tvi-field tvi-field--checkbox" id="tvi-method-chainable-field"><span>Can continue a method chain</span><input id="tvi-method-chainable" type="checkbox"><small>Use for methods that return the current object, usually <code>self</code>, <code>static</code>, or the current class.</small></label>
                </div>
                <div class="tvi-method-parameters">
                    <div class="tvi-subhead"><strong>Parameters</strong><button type="button" data-action="add-method-param">+ Parameter</button></div>
                    <div id="tvi-method-params-list"></div>
                    <small>Parameter names are required. Types and default PHP values are optional. Use values such as <code>null</code>, <code>[]</code>, <code>false</code>, <code>10</code>, or <code>'draft'</code>.</small>
                </div>
                <label class="tvi-field tvi-method-help-field"><span>Help information <em>Markdown supported</em></span><textarea id="tvi-method-help" rows="7" spellcheck="false" placeholder="Explain when to use this method, important behavior, and an example."></textarea><small>This information appears in the searchable Method Browser when the method is used by a call node.</small></label>
                <label class="tvi-field tvi-field--grow" id="tvi-method-body-field"><span>Method body</span><textarea id="tvi-method-body" class="tvi-full-code-editor" spellcheck="false" placeholder="return $this->where('email', $email)->first();"></textarea></label>
                <div class="tvi-method-flow-panel" id="tvi-method-flow-field"><strong>Execution graph</strong><p>Build this method from programming nodes. Unsaved method details are saved automatically before the graph opens.</p><button type="button" data-action="open-method-flow">Save &amp; Open Method Flow</button></div>
                <div class="tvi-modal__actions"><button type="button" data-action="save-method">Save Method</button><button type="button" data-action="new-method">Clear</button></div>
            </section>
        </div>
    </div>
</div>

<div class="tvi-modal" id="tvi-method-browser-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-method-browser"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--method-browser">
        <header class="tvi-modal__head"><div><strong id="tvi-method-browser-title">Method Browser</strong><small id="tvi-method-browser-summary">Browse methods or assemble a complete linear chain before creating normal graph nodes.</small></div><button type="button" data-action="close-method-browser">×</button></header>
        <div class="tvi-method-browser-layout">
            <aside class="tvi-method-browser-list-panel">
                <label class="tvi-search tvi-search--large"><span>⌕</span><input id="tvi-method-browser-search" type="search" placeholder="Search methods, parameters or help"></label>
                <div id="tvi-method-browser-list" class="tvi-method-browser-list"></div>
            </aside>
            <section class="tvi-method-chain-panel">
                <div class="tvi-method-chain-head"><div><strong>Chain Builder</strong><small id="tvi-method-chain-source">Drag methods here or double-click a method to append it.</small></div><button type="button" data-action="clear-method-chain">Clear</button></div>
                <div id="tvi-method-chain-list" class="tvi-method-chain-list" aria-live="polite"></div>
                <div class="tvi-method-chain-preview-wrap"><div class="tvi-method-chain-preview-head"><strong>Equivalent PHP preview</strong><span id="tvi-method-chain-status"></span></div><pre id="tvi-method-chain-preview"><code>// Add methods to preview the chain.</code></pre></div>
            </section>
            <section id="tvi-method-browser-detail" class="tvi-method-browser-detail"></section>
        </div>
        <footer class="tvi-modal__actions tvi-modal__actions--footer"><button type="button" data-action="close-method-browser">Cancel</button><button type="button" id="tvi-method-browser-use" data-action="confirm-method-browser">Use Selected Method</button><button type="button" id="tvi-method-chain-create" class="tvi-button-primary" data-action="create-method-chain">Create Chain in Graph</button></footer>
    </div>
</div>

<div class="tvi-modal" id="tvi-library-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-library"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--library">
        <header class="tvi-modal__head"><div><strong>Node Library</strong><small>Double-click several nodes to add them. This window stays open until you close it.</small></div><button type="button" data-action="close-library">×</button></header>
        <div class="tvi-library-modal-body"><label class="tvi-search tvi-search--large"><span>⌕</span><input id="tvi-library-search" type="search" placeholder="Search all available nodes"></label><div id="tvi-library-list" class="tvi-library-grid"></div></div>
    </div>
</div>


<div class="tvi-modal" id="tvi-pagination-templates-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-pagination-templates"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--pagination-templates">
        <header class="tvi-modal__head"><div><strong>Pagination Designs</strong><small>Add one or more folder-backed Pager components to the current View Builder. This window stays open while you add designs.</small></div><button type="button" data-action="close-pagination-templates">×</button></header>
        <div class="tvi-pagination-template-body">
            <div class="tvi-pagination-template-note">Design definitions live under <code>pagination-templates/&lt;design&gt;/</code>. Copy a folder and reload the IDE to add another design.</div>
            <div id="tvi-pagination-templates-list" class="tvi-pagination-template-grid"></div>
        </div>
    </div>
</div>

<div class="tvi-modal" id="tvi-html-designer-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-html-designer"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--html-designer">
        <header class="tvi-modal__head">
            <div><strong>HTML Component Designer</strong><small>HTML, isolated CSS, JavaScript and a sandboxed live iframe preview.</small></div>
            <div class="tvi-modal__actions">
                <button type="button" class="tvi-icon-button" data-action="save-html-component-preset" title="Save component as preset" aria-label="Save component as preset"><svg class="tvi-icon"><use href="#tvi-icon-preset"></use></svg></button>
                <button type="button" class="tvi-icon-button" data-action="export-html-component" title="Export component" aria-label="Export component"><svg class="tvi-icon"><use href="#tvi-icon-download"></use></svg></button>
                <button type="button" class="tvi-icon-button" data-action="import-html-component" title="Import component" aria-label="Import component"><svg class="tvi-icon"><use href="#tvi-icon-upload"></use></svg></button>
                <button type="button" class="tvi-button-primary tvi-icon-button" data-action="save-html-designer" title="Save component" aria-label="Save component"><svg class="tvi-icon"><use href="#tvi-icon-save"></use></svg></button>
                <button type="button" data-action="close-html-designer">×</button>
            </div>
        </header>
        <div class="tvi-html-designer-toolbar">
            <label class="tvi-field"><span>Node title</span><input id="tvi-html-designer-title" type="text" placeholder="Feature Card"></label>
            <label class="tvi-field"><span>Component name</span><input id="tvi-html-designer-name" type="text" placeholder="feature-card"></label>
            <label class="tvi-field tvi-html-component-insert-field"><span>Insert component</span><select id="tvi-html-designer-component" aria-label="Component to insert"></select></label>
            <button type="button" class="tvi-icon-button" data-action="insert-html-component" title="Insert selected component at the HTML cursor" aria-label="Insert selected component at the HTML cursor"><svg class="tvi-icon"><use href="#tvi-icon-plus"></use></svg></button>
            <button type="button" class="tvi-icon-button" data-action="open-html-snippets" title="Browse snippets" aria-label="Browse snippets"><svg class="tvi-icon"><use href="#tvi-icon-snippets"></use></svg></button>
            <button type="button" class="tvi-danger tvi-icon-button" data-action="clear-html-designer" title="Clear HTML, CSS and JavaScript" aria-label="Clear HTML, CSS and JavaScript"><svg class="tvi-icon"><use href="#tvi-icon-trash"></use></svg></button>
            <button type="button" class="tvi-icon-button" id="tvi-html-preview-expand" data-action="toggle-html-preview" title="Expand preview" aria-label="Expand preview"><svg class="tvi-icon"><use href="#tvi-icon-expand"></use></svg></button>
        </div>
        <div class="tvi-html-designer-body">
            <main class="tvi-html-designer-grid">
                <section class="tvi-html-editor-panel tvi-html-editor-panel--html" data-html-panel="html">
                    <header>
                        <div><strong>HTML</strong><small>Supports View variables and template tags.</small></div>
                        <button type="button" class="tvi-icon-button" data-action="toggle-html-panel" data-html-panel="html" title="Expand HTML editor" aria-label="Expand HTML editor"><svg class="tvi-icon"><use href="#tvi-icon-expand"></use></svg></button>
                    </header>
                    <textarea id="tvi-html-designer-html" spellcheck="false"></textarea>
                </section>
                <section class="tvi-html-editor-panel tvi-html-editor-panel--css" data-html-panel="css">
                    <header>
                        <div><strong>CSS</strong><small>Automatically scoped to this component.</small></div>
                        <button type="button" class="tvi-icon-button" data-action="toggle-html-panel" data-html-panel="css" title="Expand CSS editor" aria-label="Expand CSS editor"><svg class="tvi-icon"><use href="#tvi-icon-expand"></use></svg></button>
                    </header>
                    <textarea id="tvi-html-designer-css" spellcheck="false"></textarea>
                </section>
                <section class="tvi-html-editor-panel tvi-html-editor-panel--js" data-html-panel="js">
                    <header>
                        <div><strong>JavaScript</strong><small>Use <code>root.querySelector()</code> to stay inside the component.</small></div>
                        <div class="tvi-html-panel-actions">
                            <button type="button" class="tvi-icon-button" data-action="toggle-html-panel" data-html-panel="js" title="Expand JavaScript editor" aria-label="Expand JavaScript editor"><svg class="tvi-icon"><use href="#tvi-icon-expand"></use></svg></button>
                            <button type="button" class="tvi-icon-button" id="tvi-html-js-toggle" data-action="toggle-html-js" title="Expand JavaScript" aria-label="Expand JavaScript"><svg class="tvi-icon"><use href="#tvi-icon-code"></use></svg></button>
                        </div>
                    </header>
                    <textarea id="tvi-html-designer-js" spellcheck="false"></textarea>
                </section>
                <section class="tvi-html-editor-panel tvi-html-editor-panel--preview" data-html-panel="preview">
                    <header>
                        <div><strong>Preview</strong><small id="tvi-html-preview-status">Live sandbox</small></div>
                        <div class="tvi-html-panel-actions">
                            <div class="tvi-html-preview-widths" role="group" aria-label="Responsive preview width">
                                <button type="button" data-action="set-html-preview-width" data-preview-width="auto" title="Fluid preview" aria-label="Fluid preview">Fluid</button>
                                <button type="button" data-action="set-html-preview-width" data-preview-width="1200" title="Desktop preview, 1200 pixels" aria-label="Desktop preview, 1200 pixels">Desktop</button>
                                <button type="button" data-action="set-html-preview-width" data-preview-width="768" title="Tablet preview, 768 pixels" aria-label="Tablet preview, 768 pixels">Tablet</button>
                                <button type="button" data-action="set-html-preview-width" data-preview-width="375" title="Mobile preview, 375 pixels" aria-label="Mobile preview, 375 pixels">Mobile</button>
                            </div>
                            <button type="button" class="tvi-icon-button" data-action="toggle-html-panel" data-html-panel="preview" title="Expand preview" aria-label="Expand preview"><svg class="tvi-icon"><use href="#tvi-icon-expand"></use></svg></button>
                        </div>
                    </header>
                    <div id="tvi-html-preview-stage" class="tvi-html-preview-stage" data-preview-width="auto">
                        <iframe id="tvi-html-designer-preview" sandbox="allow-scripts" title="HTML component preview"></iframe>
                    </div>
                </section>
            </main>
        </div>
    </div>
</div>

<div class="tvi-modal" id="tvi-html-snippets-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-html-snippets"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--html-snippets">
        <header class="tvi-modal__head">
            <div><strong>HTML Snippet Library</strong><small>Select one snippet, preview it with the current View palette, then add the code you need.</small></div>
            <button type="button" data-action="close-html-snippets">×</button>
        </header>
        <div class="tvi-html-snippet-browser-layout">
            <aside class="tvi-html-snippet-browser-sidebar">
                <div class="tvi-html-snippets-toolbar">
                    <label class="tvi-search tvi-search--large"><span>⌕</span><input id="tvi-html-snippet-search" type="search" placeholder="Search every snippet folder…"></label>
                    <label class="tvi-field tvi-html-snippet-category-field"><span>Category</span><select id="tvi-html-snippet-tabs" aria-label="Snippet category"></select></label>
                </div>
                <div id="tvi-html-snippet-list" class="tvi-html-snippet-list"></div>
                <footer class="tvi-html-snippet-pagination">
                    <button type="button" class="tvi-icon-button" id="tvi-html-snippet-prev" title="Previous snippet page" aria-label="Previous snippet page"><svg class="tvi-icon"><use href="#tvi-icon-left"></use></svg></button>
                    <span id="tvi-html-snippet-page-info">Page 1 of 1</span>
                    <button type="button" class="tvi-icon-button" id="tvi-html-snippet-next" title="Next snippet page" aria-label="Next snippet page"><svg class="tvi-icon"><use href="#tvi-icon-right"></use></svg></button>
                </footer>
            </aside>
            <section class="tvi-html-snippet-preview-panel">
                <div class="tvi-subhead">
                    <div><strong id="tvi-html-snippet-preview-name">Snippet preview</strong><small id="tvi-html-snippet-preview-description">Select a snippet from the library.</small></div>
                    <div class="tvi-html-snippet-preview-tools">
                        <span id="tvi-html-snippet-preview-category">Live isolated iframe</span>
                        <button type="button" class="tvi-icon-button" id="tvi-html-snippet-preview-fullscreen" data-action="toggle-html-snippet-preview-fullscreen" title="View preview full screen" aria-label="View preview full screen" aria-pressed="false">
                            <svg class="tvi-icon"><use href="#tvi-icon-expand"></use></svg>
                        </button>
                    </div>
                </div>
                <iframe id="tvi-html-snippet-preview" title="HTML snippet preview" sandbox="allow-scripts"></iframe>
                <div class="tvi-html-snippet-preview-actions" aria-label="Add selected snippet code">
                    <button type="button" class="tvi-button-primary" data-html-snippet-section="all" title="Add HTML, CSS and JavaScript">Add All</button>
                    <button type="button" data-html-snippet-section="html" title="Add only the HTML">HTML</button>
                    <button type="button" data-html-snippet-section="css" title="Add only the CSS">CSS</button>
                    <button type="button" data-html-snippet-section="js" title="Add only the JavaScript">JavaScript</button>
                </div>
            </section>
            <aside class="tvi-html-snippet-palette-panel" id="tvi-html-snippet-palette-panel">
                <header class="tvi-html-snippet-palette-head">
                    <strong>Colors</strong>
                    <button type="button" class="tvi-icon-button" id="tvi-html-snippet-palette-toggle" data-action="toggle-html-snippet-palette" title="Minimize color palette" aria-label="Minimize color palette" aria-expanded="true">
                        <svg class="tvi-icon"><use href="#tvi-icon-right"></use></svg>
                    </button>
                </header>
                <div class="tvi-html-snippet-palette-content">
                    <div class="tvi-html-snippet-theme-summary"><strong id="tvi-html-snippet-theme-name">Look theme</strong><small>These colors are shared by form inputs and HTML snippets across Views connected to this Look.</small></div>
                    <label class="tvi-field"><span>Palette preset</span><select id="tvi-html-snippet-palette"></select><small>Includes the current form theme palettes plus ten reusable snippet color presets.</small></label>
                    <div id="tvi-html-snippet-colors" class="tvi-form-theme-colors"></div>
                    <div class="tvi-form-theme-palette-actions">
                        <button type="button" data-action="reset-html-snippet-colors">Use Palette Defaults</button>
                        <button type="button" data-action="export-html-snippet-colors">Export Colors</button>
                        <button type="button" data-action="import-html-snippet-colors">Import Colors</button>
                        <input id="tvi-html-snippet-color-import" type="file" accept="application/json,.json" hidden>
                    </div>
                </div>
            </aside>
        </div>
        <footer class="tvi-modal__actions">
            <button type="button" data-action="close-html-snippets">Close</button>
            <button type="button" class="tvi-button-primary" data-action="apply-html-snippet-colors">Apply Colors to Look</button>
        </footer>
    </div>
</div>

<div class="tvi-modal" id="tvi-node-export-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-node-export"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--compact">
        <header class="tvi-modal__head">
            <div><strong>Export Selected Nodes</strong><small id="tvi-node-export-summary">Save the current selection as a portable node bundle.</small></div>
            <button type="button" data-action="close-node-export">×</button>
        </header>
        <div class="tvi-compact-modal-body">
            <label class="tvi-field"><span>File name</span><input type="text" id="tvi-node-export-name" autocomplete="off" spellcheck="false"><small>The <code>.thunder-nodes.json</code> extension is added automatically.</small></label>
        </div>
        <footer class="tvi-modal__actions tvi-modal__actions--footer">
            <button type="button" data-action="close-node-export">Cancel</button>
            <button type="button" class="tvi-button-primary" data-action="confirm-node-export">Export Nodes</button>
        </footer>
    </div>
</div>

<div class="tvi-modal" id="tvi-presets-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-presets"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--presets">
        <header class="tvi-modal__head"><div><strong>Node Presets</strong></div><button type="button" data-action="close-presets">×</button></header>
        <div class="tvi-presets-body">
            <section><div class="tvi-subhead"><strong>Bundled presets</strong></div><div id="tvi-bundled-presets-list"></div></section>
            <section><div class="tvi-subhead"><strong>User presets</strong><div class="tvi-modal__actions"><button type="button" data-action="export-presets">Export</button><button type="button" data-action="import-presets">Import</button></div></div><div id="tvi-presets-list"></div></section>
        </div>
    </div>
</div>

<div class="tvi-modal" id="tvi-manager-editor-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-manager-editor"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--manager-editor">
        <header class="tvi-modal__head"><div><strong id="tvi-manager-editor-title">Expanded Editor</strong><small id="tvi-manager-editor-summary">Edit the selected node with more workspace.</small></div><button type="button" data-action="close-manager-editor">×</button></header>
        <div class="tvi-manager-editor-body" id="tvi-manager-editor-body"></div>
        <footer class="tvi-modal__actions tvi-modal__actions--footer"><span class="tvi-muted">Changes are applied immediately to the node and Inspector.</span><button type="button" class="tvi-button-primary" data-action="close-manager-editor">Done</button></footer>
    </div>
</div>

<div class="tvi-modal" id="tvi-projects-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-projects"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--projects">
        <header class="tvi-modal__head"><div><strong>Saved IDE Projects</strong><small>Projects are stored on the server inside this IDE plugin.</small></div><button type="button" data-action="close-projects">×</button></header>
        <div class="tvi-projects-body">
            <section class="tvi-project-save-panel">
                <label class="tvi-field"><span>Project name</span><input id="tvi-project-name" type="text" placeholder="Authentication Plugin"></label>
                <div class="tvi-modal__actions"><button type="button" class="tvi-button-primary" data-action="confirm-save-project-as">Save Current Project</button><button type="button" data-action="refresh-projects">Refresh List</button></div>
                <p>Saving an existing loaded project updates the same file. Use Save Project As to create another copy.</p>
            </section>
            <section class="tvi-saved-projects"><div class="tvi-subhead"><strong>Available projects</strong><span id="tvi-projects-count"></span></div><div id="tvi-projects-list"></div></section>
        </div>
    </div>
</div>

<div class="tvi-modal" id="tvi-migration-output-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-migration-output"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--migration-output">
        <header class="tvi-modal__head"><div><strong id="tvi-migration-output-title">Migration Output</strong><small id="tvi-migration-output-summary"></small></div><button type="button" data-action="close-migration-output">×</button></header>
        <pre id="tvi-migration-output" class="tvi-migration-output"></pre>
        <div class="tvi-modal__actions tvi-modal__actions--footer"><button type="button" data-action="close-migration-output">Close</button></div>
    </div>
</div>

<div class="tvi-modal" id="tvi-overwrite-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="cancel-test-overwrite"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--warning">
        <header class="tvi-modal__head"><div><strong>Replace Existing Plugin?</strong><small>You only need to approve this once for the current project session.</small></div><button type="button" data-action="cancel-test-overwrite">×</button></header>
        <div class="tvi-warning-body"><h2>Update the existing plugin folder?</h2><p id="tvi-overwrite-message"></p><div class="tvi-warning-box">Continuing replaces the files currently in that plugin folder with this test build. Make a backup first only if the folder contains changes you want to keep.</div><div class="tvi-modal__actions"><button type="button" data-action="cancel-test-overwrite">Not Now</button><button type="button" class="tvi-button-primary" data-action="confirm-test-overwrite">Replace and Continue</button></div></div>
    </div>
</div>

<div class="tvi-modal" id="tvi-package-export-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-project-package-export"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--settings">
        <header class="tvi-modal__head"><div><strong>Export Project Package</strong><small>Create a portable ZIP containing the project and selected external files.</small></div><button type="button" data-action="close-project-package-export">×</button></header>
        <div class="tvi-settings-body">
            <label class="tvi-package-option"><input id="tvi-package-include-assets" type="checkbox" checked><span><strong>Include uploaded asset files</strong><small>Packages binary files from Asset nodes. Editable CSS and JavaScript Code Assets remain in the project JSON.</small></span></label>
            <label class="tvi-package-option"><input id="tvi-package-include-libraries" type="checkbox" checked><span><strong>Include library ZIP archives</strong><small>Makes Library Package nodes portable to another ThunderPHP installation.</small></span></label>
            <div class="tvi-package-note">When an option is disabled, the node definitions remain in the project but their external file contents must be supplied again after import.</div>
            <div class="tvi-modal__actions"><button type="button" data-action="close-project-package-export">Cancel</button><button type="button" class="tvi-button-primary" data-action="export-project-package">Export Package ZIP</button></div>
        </div>
    </div>
</div>


<div class="tvi-modal" id="tvi-form-theme-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-form-theme"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--form-theme">
        <header class="tvi-modal__head">
            <div><strong>Look Form Input Theme</strong><small>Select one shared theme and palette for built-in Form, Input, Select, Textarea, Checkbox, and Button nodes across this Look.</small></div>
            <button type="button" data-action="close-form-theme">×</button>
        </header>
        <div class="tvi-form-theme-layout">
            <aside class="tvi-form-theme-list" id="tvi-form-theme-list"></aside>
            <section class="tvi-form-theme-preview-panel">
                <div class="tvi-subhead"><div><strong id="tvi-form-theme-preview-name">Theme preview</strong><small id="tvi-form-theme-preview-description"></small></div><span>Live isolated iframe</span></div>
                <iframe id="tvi-form-theme-preview" title="Form input theme preview" sandbox="allow-scripts"></iframe>
            </section>
            <aside class="tvi-form-theme-palette-panel">
                <label class="tvi-field"><span>Palette preset</span><select id="tvi-form-theme-palette"></select><small>Includes palettes bundled with the selected form design and the reusable snippet color presets.</small></label>
                <div id="tvi-form-theme-colors" class="tvi-form-theme-colors"></div>
                <div class="tvi-form-theme-palette-actions">
                    <button type="button" data-action="reset-form-theme-colors">Use Palette Defaults</button>
                    <button type="button" data-action="export-form-theme-colors">Export Colors</button>
                    <button type="button" data-action="import-form-theme-colors">Import Theme &amp; Colors</button>
                    <button type="button" data-action="import-form-theme-colors-only">Import Colors Only</button>
                    <input id="tvi-form-theme-color-import" type="file" accept="application/json,.json" hidden>
                </div>
            </aside>
        </div>
        <footer class="tvi-modal__actions">
            <button type="button" data-action="close-form-theme">Cancel</button>
            <button type="button" class="tvi-button-primary" data-action="apply-form-theme">Apply Theme</button>
        </footer>
    </div>
</div>


<div class="tvi-modal" id="tvi-about-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-about"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--about">
        <header class="tvi-modal__head"><div><strong>About Thunder Visual IDE</strong><small>Visual plugin development for ThunderPHP</small></div><button type="button" data-action="close-about">×</button></header>
        <div class="tvi-about-body">
            <div class="tvi-about-mark"><img src="<?= current_look_http('assets/images/plugin.jpg') ?>" alt="Thunder Visual IDE logo"></div>
            <div>
                <h2><?= htmlspecialchars((string) ($plugin_info['name'] ?? 'Thunder Visual IDE'), ENT_QUOTES, 'UTF-8') ?></h2>
                <p class="tvi-about-version">Version <?= htmlspecialchars((string) ($plugin_info['version'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                <p>Thunder Visual IDE is a node-based visual development environment for creating normal, editable ThunderPHP plugins. It provides architecture graphs, executable flows, View Builder components, migrations, models, reusable presets, theme systems and safe plugin compilation without requiring an authentication system or database.</p>
                <dl class="tvi-about-details">
                    <div><dt>Author</dt><dd><?= htmlspecialchars((string) ($plugin_info['author'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd></div>
                    <div><dt>Email</dt><dd><a href="mailto:<?= htmlspecialchars((string) ($plugin_info['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) ($plugin_info['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></a></dd></div>
                    <div><dt>Plugin ID</dt><dd><code><?= htmlspecialchars((string) ($plugin_info['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></code></dd></div>
                    <div><dt>Design model</dt><dd>Node-based plugin architecture with local project, preset, tutorial and library files</dd></div>
                </dl>
                <div class="tvi-about-actions">
                    <a href="<?= htmlspecialchars(rtrim((string) ROOT, '/') . '/thunder-ide/tutorials', ENT_QUOTES, 'UTF-8') ?>">Browse Tutorials</a>
                    <a href="<?= htmlspecialchars((string) ($plugin_info['website'] ?? 'https://thunderphp.com'), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">Visit thunderphp.com ↗</a>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="tvi-modal" id="tvi-marketplace-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-marketplace-studio"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--marketplace">
        <header class="tvi-modal__head">
            <div><strong>Marketplace Asset Studio</strong><small>Create distributable form-theme and HTML-snippet ZIP packages without modifying built-in assets.</small></div>
            <button type="button" data-action="close-marketplace-studio">×</button>
        </header>
        <div class="tvi-marketplace-tabs" role="tablist" aria-label="Marketplace asset type">
            <button type="button" class="is-active" data-marketplace-tab="theme">Form Themes</button>
            <button type="button" data-marketplace-tab="snippet">HTML Snippets</button>
        </div>
        <div class="tvi-marketplace-layout" id="tvi-marketplace-layout">
            <aside class="tvi-marketplace-sidebar" data-marketplace-column="sidebar">
                <div class="tvi-marketplace-column-head">
                    <strong>Assets</strong>
                    <button type="button" data-marketplace-focus="sidebar" title="Focus assets column" aria-label="Focus assets column">⛶</button>
                </div>
                <div class="tvi-marketplace-column-content">
                    <label class="tvi-search tvi-search--large"><span>⌕</span><input id="tvi-marketplace-search" type="search" placeholder="Search assets"></label>
                    <div class="tvi-marketplace-sidebar-actions">
                        <button type="button" class="tvi-button-primary" data-action="new-marketplace-asset">New Custom</button>
                        <button type="button" data-action="import-marketplace-package">Import ZIP</button>
                    </div>
                    <div id="tvi-marketplace-list" class="tvi-marketplace-list"></div>
                </div>
            </aside>
            <main class="tvi-marketplace-editor" data-marketplace-column="editor">
                <div class="tvi-marketplace-column-head">
                    <strong>Package editor</strong>
                    <button type="button" data-marketplace-focus="editor" title="Focus package editor column" aria-label="Focus package editor column">⛶</button>
                </div>
                <div class="tvi-marketplace-column-content">
                    <div id="tvi-marketplace-readonly-note" class="tvi-marketplace-readonly-note" hidden>Built-in assets are read-only. Use <strong>Create Custom Copy</strong> to use this asset as a starting point.</div>
                    <section class="tvi-marketplace-meta-grid">
                        <label class="tvi-field"><span>Package ID</span><input id="tvi-marketplace-id" type="text" placeholder="my-form-theme"><small>Permanent marketplace identifier. It cannot be changed after saving.</small></label>
                        <label class="tvi-field"><span>Name</span><input id="tvi-marketplace-name" type="text" placeholder="My Form Theme"></label>
                        <label class="tvi-field"><span>Version</span><input id="tvi-marketplace-version" type="text" value="1.0.0" placeholder="1.0.0"></label>
                        <label class="tvi-field"><span>Author</span><input id="tvi-marketplace-author" type="text" placeholder="Author or company"></label>
                        <label class="tvi-field"><span>Website</span><input id="tvi-marketplace-website" type="url" placeholder="https://example.com"></label>
                        <label class="tvi-field"><span>License</span><input id="tvi-marketplace-license" type="text" value="Commercial" placeholder="Commercial"></label>
                        <label class="tvi-field"><span>Category</span><input id="tvi-marketplace-category" type="text" placeholder="Forms or Cards"></label>
                        <label class="tvi-field"><span>Tags</span><input id="tvi-marketplace-tags" type="text" placeholder="admin, clean, responsive"></label>
                        <label class="tvi-field"><span>Order</span><input id="tvi-marketplace-order" type="number" value="100"></label>
                        <label class="tvi-field tvi-field--span-all"><span>Description</span><textarea id="tvi-marketplace-description" rows="3" placeholder="Explain what this package provides and where it works best."></textarea></label>
                    </section>
                    <section id="tvi-marketplace-theme-editor" class="tvi-marketplace-code-section">
                        <div class="tvi-subhead"><div><strong>Theme source</strong><small>Supply a complete preview form, isolated CSS, optional JavaScript and the templates used by View form nodes.</small></div></div>
                        <div class="tvi-marketplace-graph-note"><strong>How fields connect to the graph</strong><p>The preview HTML does not create input nodes. A form theme styles the existing Form, Text Input, Select, Checkbox and other View Builder nodes through the component templates below.</p></div>
                        <div class="tvi-marketplace-theme-tools">
                            <select id="tvi-marketplace-default-palette" hidden aria-hidden="true"></select>
                            <textarea id="tvi-marketplace-palettes" hidden aria-hidden="true"></textarea>
                            <button type="button" data-action="reset-marketplace-palette">Reset Starter Palettes</button>
                        </div>
                        <section class="tvi-marketplace-palette-manager" id="tvi-marketplace-palette-manager">
                            <div class="tvi-marketplace-palette-sidebar">
                                <div class="tvi-subhead"><div><strong>Color palettes</strong><small>Select a palette to edit and preview it immediately.</small></div></div>
                                <div id="tvi-marketplace-palette-list" class="tvi-marketplace-palette-list"></div>
                                <div class="tvi-marketplace-palette-actions">
                                    <button type="button" data-marketplace-palette-action="add">New</button>
                                    <button type="button" data-marketplace-palette-action="duplicate">Duplicate</button>
                                    <button type="button" data-marketplace-palette-action="delete" class="tvi-danger">Delete</button>
                                </div>
                            </div>
                            <div class="tvi-marketplace-palette-editor">
                                <div class="tvi-marketplace-palette-meta">
                                    <label class="tvi-field"><span>Palette name</span><input id="tvi-marketplace-palette-name" type="text"></label>
                                    <div class="tvi-marketplace-palette-id"><span>Palette ID</span><code id="tvi-marketplace-palette-id"></code></div>
                                    <button type="button" id="tvi-marketplace-palette-default" data-marketplace-palette-action="default">Set as default</button>
                                </div>
                                <div id="tvi-marketplace-palette-colors" class="tvi-marketplace-palette-colors"></div>
                                <button type="button" data-marketplace-palette-action="add-color">Add color token</button>
                            </div>
                        </section>
                        <div class="tvi-marketplace-editor-grid">
                            <div class="tvi-field tvi-marketplace-code-field"><div class="tvi-marketplace-code-head"><span>Preview form HTML</span><button type="button" data-marketplace-expand="tvi-marketplace-preview-html" data-marketplace-mode="htmlmixed" data-marketplace-title="Preview form HTML">Expand</button></div><textarea id="tvi-marketplace-preview-html" class="tvi-marketplace-code" rows="16" spellcheck="false"></textarea></div>
                            <div class="tvi-field tvi-marketplace-code-field"><div class="tvi-marketplace-code-head"><span>Theme CSS</span><button type="button" data-marketplace-expand="tvi-marketplace-theme-css" data-marketplace-mode="css" data-marketplace-title="Theme CSS">Expand</button></div><textarea id="tvi-marketplace-theme-css" class="tvi-marketplace-code" rows="16" spellcheck="false"></textarea><small>Every rule must be anchored through <code>{{scope}}</code>.</small></div>
                            <div class="tvi-field tvi-field--span-all tvi-marketplace-code-field"><div class="tvi-marketplace-code-head"><span>Theme JavaScript</span><button type="button" data-marketplace-expand="tvi-marketplace-theme-js" data-marketplace-mode="javascript" data-marketplace-title="Theme JavaScript">Expand</button></div><textarea id="tvi-marketplace-theme-js" class="tvi-marketplace-code" rows="8" spellcheck="false" placeholder="// Optional behaviour"></textarea></div>
                        </div>
                        <div class="tvi-marketplace-component-editor">
                            <div class="tvi-subhead"><div><strong>Component templates</strong><small>The required tokens are validated before a package is saved or imported.</small></div><label>Template <select id="tvi-marketplace-component-select"><option value="field">Text/email/password/number/date/file</option><option value="textarea">Textarea</option><option value="select">Select</option><option value="checkbox">Checkbox</option><option value="button">Button</option><option value="form">Form wrapper</option></select></label></div>
                            <div class="tvi-marketplace-code-head"><span>Selected template source</span><button type="button" data-marketplace-expand="tvi-marketplace-component-code" data-marketplace-mode="htmlmixed" data-marketplace-title="Component template">Expand</button></div>
                            <textarea id="tvi-marketplace-component-code" class="tvi-marketplace-code" rows="8" spellcheck="false"></textarea>
                            <div id="tvi-marketplace-component-tokens" class="tvi-marketplace-token-list"></div>
                        </div>
                    </section>
                    <section id="tvi-marketplace-snippet-editor" class="tvi-marketplace-code-section" hidden>
                        <div class="tvi-subhead"><div><strong>Snippet source</strong><small>Use fragment HTML, automatically scoped CSS and JavaScript that works from the supplied <code>root</code> component wrapper.</small></div></div>
                        <div class="tvi-marketplace-graph-note"><strong>How snippet inputs work</strong><p>A snippet is inserted as one HTML Design Component node. Input tags inside the snippet remain HTML; they are not extracted into separate graph nodes. Use normal View Builder Form/Input nodes when each field must be independently connected or configured.</p></div>
                        <div class="tvi-marketplace-editor-grid">
                            <div class="tvi-field tvi-marketplace-code-field"><div class="tvi-marketplace-code-head"><span>HTML fragment</span><button type="button" data-marketplace-expand="tvi-marketplace-snippet-html" data-marketplace-mode="htmlmixed" data-marketplace-title="Snippet HTML">Expand</button></div><textarea id="tvi-marketplace-snippet-html" class="tvi-marketplace-code" rows="18" spellcheck="false"></textarea></div>
                            <div class="tvi-field tvi-marketplace-code-field"><div class="tvi-marketplace-code-head"><span>CSS</span><button type="button" data-marketplace-expand="tvi-marketplace-snippet-css" data-marketplace-mode="css" data-marketplace-title="Snippet CSS">Expand</button></div><textarea id="tvi-marketplace-snippet-css" class="tvi-marketplace-code" rows="18" spellcheck="false"></textarea></div>
                            <div class="tvi-field tvi-field--span-all tvi-marketplace-code-field"><div class="tvi-marketplace-code-head"><span>JavaScript</span><button type="button" data-marketplace-expand="tvi-marketplace-snippet-js" data-marketplace-mode="javascript" data-marketplace-title="Snippet JavaScript">Expand</button></div><textarea id="tvi-marketplace-snippet-js" class="tvi-marketplace-code" rows="8" spellcheck="false" placeholder="const button = root.querySelector('[data-action]');"></textarea></div>
                        </div>
                    </section>
                </div>
            </main>
            <aside class="tvi-marketplace-preview-panel" data-marketplace-column="preview">
                <div class="tvi-marketplace-column-head">
                    <strong>Preview and checks</strong>
                    <button type="button" data-marketplace-focus="preview" title="Focus preview and checks column" aria-label="Focus preview and checks column">⛶</button>
                </div>
                <div class="tvi-marketplace-column-content">
                    <div class="tvi-subhead"><div><strong>Live preview</strong><small>Sandboxed preview using the current package source.</small></div><span id="tvi-marketplace-source-badge">Custom</span></div>
                    <iframe id="tvi-marketplace-preview" title="Marketplace asset preview" sandbox="allow-scripts"></iframe>
                    <section class="tvi-marketplace-rules">
                        <strong>Package checks</strong>
                        <div id="tvi-marketplace-validation" aria-live="polite"></div>
                    </section>
                </div>
            </aside>
        </div>
        <footer class="tvi-modal__actions tvi-modal__actions--footer">
            <button type="button" data-action="close-marketplace-studio">Close</button>
            <button type="button" id="tvi-marketplace-copy" data-action="copy-marketplace-asset">Create Custom Copy</button>
            <button type="button" id="tvi-marketplace-delete" class="tvi-danger" data-action="delete-marketplace-asset">Delete Custom</button>
            <button type="button" id="tvi-marketplace-export" data-action="export-marketplace-asset">Export ZIP</button>
            <button type="button" id="tvi-marketplace-save" class="tvi-button-primary" data-action="save-marketplace-asset">Save Custom Asset</button>
        </footer>
    </div>
</div>

<div class="tvi-modal" id="tvi-settings-modal" hidden>
    <div class="tvi-modal__backdrop" data-action="close-settings"></div>
    <div class="tvi-modal__dialog tvi-modal__dialog--settings">
        <header class="tvi-modal__head"><div><strong>Editor Settings</strong><small>Preferences apply to generated-code preview, source editing, and the HTML Component Designer.</small></div><button type="button" data-action="close-settings">×</button></header>
        <div class="tvi-settings-body">
            <div class="tvi-settings-grid">
                <label class="tvi-field"><span>Code editor theme</span><select id="tvi-code-theme">
                    <option value="thunder-dark">Thunder Dark</option>
                    <option value="material-darker">Material Darker</option>
                    <option value="darcula">Darcula</option>
                    <option value="monokai">Monokai</option>
                    <option value="nord">Nord</option>
                    <option value="eclipse">Eclipse Light</option>
                    <option value="neo">Neo Light</option>
                </select></label>
                <label class="tvi-field"><span>Word wrap</span><select id="tvi-code-word-wrap">
                    <option value="off">Off</option>
                    <option value="on">On</option>
                </select></label>
            </div>
            <p class="tvi-muted">CodeMirror themes are CSS files under <code>looks/main/assets/vendor/codemirror/theme/</code>. Word wrap is stored in this browser and applied to every CodeMirror editor.</p>
            <div class="tvi-modal__actions"><button type="button" data-action="close-settings">Cancel</button><button type="button" class="tvi-button-primary" data-action="save-settings">Save Settings</button></div>
        </div>
    </div>
</div>

<div class="tvi-context-menu" id="tvi-context-menu" hidden></div>

<div class="tvi-toast-region" id="tvi-toast-region"></div>
<script>
window.THUNDER_VISUAL_IDE = <?= json_encode([
    'apiBase' => $api_base,
    'storageKey' => 'thunder_visual_ide_project_v4',
    'presetStorageKey' => 'thunder_visual_ide_presets_v1',
    'nodeDefinitions' => $node_definitions,
    'paginationTemplates' => $pagination_templates,
    'htmlSnippets' => $html_snippets,
    'validationRules' => $validation_rules,
    'bundledPresets' => $bundled_presets,
    'formThemes' => $form_themes,
    'sampleProjects' => $sample_projects,
    'defaultSampleId' => 'auth-starter',
    'version' => (string) ($plugin_info['version'] ?? ''),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
<script src="<?= current_look_http('assets/vendor/codemirror/lib/codemirror.js') ?>"></script>
<script src="<?= current_look_http('assets/vendor/codemirror/mode/xml/xml.js') ?>"></script>
<script src="<?= current_look_http('assets/vendor/codemirror/mode/javascript/javascript.js') ?>"></script>
<script src="<?= current_look_http('assets/vendor/codemirror/mode/css/css.js') ?>"></script>
<script src="<?= current_look_http('assets/vendor/codemirror/mode/htmlmixed/htmlmixed.js') ?>"></script>
<script src="<?= current_look_http('assets/vendor/codemirror/mode/clike/clike.js') ?>"></script>
<script src="<?= current_look_http('assets/vendor/codemirror/mode/php/php.js') ?>"></script>
<script src="<?= current_look_http('assets/vendor/codemirror/mode/markdown/markdown.js') ?>"></script>
<script src="<?= current_look_http('assets/vendor/codemirror/addon/edit/matchbrackets.js') ?>"></script>
<script src="<?= current_look_http('assets/vendor/codemirror/addon/edit/closebrackets.js') ?>"></script>
<script src="<?= current_look_http('assets/vendor/codemirror/addon/selection/active-line.js') ?>"></script>
<script src="<?= current_look_http('assets/js/editor.js') ?>?v=<?= htmlspecialchars($asset_version, ENT_QUOTES, 'UTF-8') ?>"></script>
