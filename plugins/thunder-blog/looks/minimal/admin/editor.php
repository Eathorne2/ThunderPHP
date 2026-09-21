<?php

namespace ThunderBlog;

$postPalette = json_decode((string) $post->palette_json, true);
$postPalette = is_array($postPalette) ? blog_validate_palette($postPalette) : $global_palette;
$initialBlocks = trim((string) $post->blocks_json) !== '' ? (string) $post->blocks_json : '[]';
$publishedValue = !empty($post->published_at) ? date('Y-m-d\TH:i', strtotime((string) $post->published_at)) : (!$is_edit && in_array($post_type, ['post', 'page'], true) ? date('Y-m-d\TH:i') : '');
?>
<link rel="stylesheet" href="<?= esc(blog_look_http('assets/css/admin.css')) ?>">
<link rel="stylesheet" href="<?= esc(blog_look_http('assets/css/builder.css')) ?>">
<form class="tb-editor" method="post" enctype="multipart/form-data" id="tb-post-form" data-tb-editor-form>
    <?= csrf() ?>
    <input type="hidden" name="post_type" value="<?= esc($post_type) ?>">
    <input type="hidden" name="blocks_json" id="tb-blocks-json" value="<?= esc($initialBlocks) ?>">
    <input type="hidden" name="palette_json" id="tb-palette-json" value="<?= esc(json_encode($postPalette, JSON_UNESCAPED_SLASHES)) ?>">

    <header class="tb-editor__topbar">
        <div class="tb-editor__topbar-left">
            <a class="tb-editor__back" href="<?= esc($back_url) ?>" title="Back to <?= esc(strtolower(blog_content_type_label($post_type, true))) ?>"><i class="fa-solid fa-arrow-left"></i></a>
            <div class="tb-editor__title-fields">
                <input class="tb-editor__title-input" type="text" name="title" value="<?= esc($post->title) ?>" placeholder="<?= esc($content_label) ?> title" required data-tb-post-title>
                <div class="tb-editor__slug-row"><span class="tb-editor__slug-prefix"><?= esc($slug_prefix) ?></span><input class="tb-editor__slug-input" type="text" name="slug" value="<?= esc($post->slug) ?>" placeholder="generated-<?= esc($post_type) ?>-slug" data-tb-post-slug></div>
            </div>
        </div>
        <div class="tb-editor__topbar-actions">
            <select class="tb-editor__status" name="status">
                <option value="draft" <?= $post->status === 'draft' ? 'selected' : '' ?>>Draft</option>
                <?php if ($can_publish): ?><option value="published" <?= $post->status === 'published' ? 'selected' : '' ?>>Published</option><option value="scheduled" <?= $post->status === 'scheduled' ? 'selected' : '' ?>>Scheduled</option><?php endif; ?>
                <option value="archived" <?= $post->status === 'archived' ? 'selected' : '' ?>>Archived</option>
            </select>
            <?php if ($is_edit && in_array($post_type, ['post', 'page'], true)): ?><a class="tb-editor__preview-link" href="<?= esc(blog_post_url($post)) ?>" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> View</a><?php endif; ?>
            <button class="tb-editor__save" type="submit"><i class="fa-solid fa-floppy-disk"></i> <?= $is_edit ? 'Update ' . esc($content_label_lower) : 'Create ' . esc($content_label_lower) ?></button>
        </div>
    </header>

    <?php if ($error): ?><div class="tb-editor__error"><?= esc($error) ?></div><?php endif; ?>

    <div class="tb-builder" id="tb-builder"
         data-api-list="<?= esc(ROOT . '/admin/blog/api/blocks') ?>"
         data-api-block="<?= esc(ROOT . '/admin/blog/api/blocks/__SLUG__') ?>"
         data-api-images="<?= esc(ROOT . '/admin/blog/api/images') ?>"
         data-initial-blocks="<?= esc($initialBlocks) ?>"
         data-initial-palette="<?= esc(json_encode($postPalette, JSON_UNESCAPED_SLASHES)) ?>"
         data-palettes="<?= esc(json_encode($palette_presets, JSON_UNESCAPED_SLASHES)) ?>"
         data-menus="<?= esc(json_encode($menus, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) ?>"
         data-context="<?= esc($builder_context) ?>"
         data-content-label="<?= esc($content_label) ?>"
         data-app-name="<?= esc(defined('APP_NAME') ? APP_NAME : 'Website') ?>"
         data-app-description="<?= esc(defined('APP_DESCRIPTION') ? APP_DESCRIPTION : '') ?>"
         data-root="<?= esc(ROOT) ?>"
         data-app-logo="<?= esc(defined('APP_LOGO') && APP_LOGO ? get_image(APP_LOGO) : '') ?>"
         data-year="<?= date('Y') ?>">
        <div class="tb-builder__toolbar">
            <div class="tb-builder__toolbar-group">
                <button class="tb-builder__open-library" type="button" data-tb-open-library><i class="fa-solid fa-table-cells-large"></i><span>Browse blocks</span></button>
                <span class="tb-builder__toolbar-divider" aria-hidden="true"></span>
                <button class="tb-builder__tool-button" type="button" data-tb-breakpoint="390" title="Mobile"><i class="fa-solid fa-mobile-screen"></i></button>
                <button class="tb-builder__tool-button" type="button" data-tb-breakpoint="768" title="Tablet"><i class="fa-solid fa-tablet-screen-button"></i></button>
                <button class="tb-builder__tool-button" type="button" data-tb-breakpoint="1200" title="Desktop"><i class="fa-solid fa-desktop"></i></button>
                <button class="tb-builder__tool-button tb-builder__tool-button--active" type="button" data-tb-breakpoint="full" title="Full width"><i class="fa-solid fa-arrows-left-right-to-line"></i></button>
            </div>
            <div class="tb-builder__toolbar-center"><span class="tb-builder__document-label"><?= esc($content_label) ?> canvas</span><span class="tb-builder__save-state" data-tb-save-state>Changes update the saved block document</span></div>
            <div class="tb-builder__toolbar-group">
                <label class="tb-builder__palette-label"><span class="tb-builder__palette-text">Palette</span><select class="tb-builder__palette-select" data-tb-palette><?php foreach ($palette_presets as $key => $preset): ?><option value="<?= esc($key) ?>"><?= esc($preset['name']) ?></option><?php endforeach; ?><option value="custom">Content palette</option></select></label>
                <button class="tb-builder__tool-button" type="button" data-tb-wide title="Expand canvas"><i class="fa-solid fa-maximize"></i></button>
                <button class="tb-builder__tool-button" type="button" data-tb-fullscreen title="Fullscreen editor"><i class="fa-solid fa-expand"></i></button>
            </div>
        </div>

        <div class="tb-builder__workspace">
            <main class="tb-builder__stage">
                <div class="tb-builder__stage-scroll">
                    <div class="tb-builder__frame-shell" data-tb-frame-shell>
                        <iframe class="tb-builder__canvas-frame" title="<?= esc($content_label) ?> canvas" sandbox="allow-scripts" scrolling="no" data-tb-canvas></iframe>
                    </div>
                </div>
            </main>

            <aside class="tb-builder__inspector">
                <div class="tb-builder__inspector-sticky">
                <div class="tb-builder__tabs" role="tablist">
                    <button class="tb-builder__tab tb-builder__tab--active" type="button" data-tb-tab="properties">Inspector</button>
                    <button class="tb-builder__tab" type="button" data-tb-tab="post"><?= esc($content_label) ?></button>
                </div>
                <div class="tb-builder__tab-panel tb-builder__tab-panel--active" data-tb-panel="properties">
                    <div class="tb-builder__inspector-empty" data-tb-inspector-empty><i class="fa-regular fa-hand-pointer tb-builder__inspector-empty-icon"></i><h3 class="tb-builder__inspector-empty-title">Select a block or element</h3><p class="tb-builder__inspector-empty-text">Click a block for block-wide controls, or click a registered element to edit that element independently.</p></div>
                    <div class="tb-builder__inspector-content" data-tb-inspector-content></div>
                </div>
                <div class="tb-builder__tab-panel" data-tb-panel="post">
                    <div class="tb-builder__post-fields">
                        <?php if (!$is_design): ?>
                        <label class="tb-builder__field"><span class="tb-builder__label">Excerpt</span><textarea class="tb-builder__textarea" name="excerpt" rows="5" placeholder="A short summary for listings and metadata."><?= esc($post->excerpt) ?></textarea></label>
                        <label class="tb-builder__field"><span class="tb-builder__label">Category</span><select class="tb-builder__select" name="category_id"><option value="0">Uncategorized</option><?php foreach ($categories as $category): ?><option value="<?= (int) $category->id ?>" <?= (int) $post->category_id === (int) $category->id ? 'selected' : '' ?>><?= esc($category->name) ?></option><?php endforeach; ?></select></label>
                        <label class="tb-builder__field"><span class="tb-builder__label">Tags</span><input class="tb-builder__input" type="text" name="tags" value="<?= esc($tags_text) ?>" placeholder="news, updates, community"><span class="tb-builder__hint">Separate tags with commas.</span></label>
                        <div class="tb-builder__field"><span class="tb-builder__label">Featured image URL</span><div class="tb-builder__input-action-row"><input class="tb-builder__input" type="url" name="featured_image" value="<?= esc($post->featured_image) ?>" placeholder="https://..." data-tb-featured-image><button class="tb-builder__browse-image-button" type="button" data-tb-featured-image-browse><i class="fa-regular fa-images"></i><span>Browse</span></button></div></div>
                        <label class="tb-builder__field"><span class="tb-builder__label">Or upload an image</span><input class="tb-builder__file" type="file" name="featured_image_file" accept="image/jpeg,image/png,image/webp,image/gif"></label>
                        <label class="tb-builder__field"><span class="tb-builder__label">Publish date</span><input class="tb-builder__input" type="datetime-local" name="published_at" value="<?= esc($publishedValue) ?>"></label>
                        <label class="tb-builder__field"><span class="tb-builder__label">SEO title</span><input class="tb-builder__input" type="text" name="seo_title" value="<?= esc($post->seo_title) ?>" maxlength="255"></label>
                        <label class="tb-builder__field"><span class="tb-builder__label">SEO description</span><textarea class="tb-builder__textarea" name="seo_description" rows="4" maxlength="500"><?= esc($post->seo_description) ?></textarea></label>
                        <label class="tb-builder__check"><input class="tb-builder__checkbox" type="checkbox" name="is_featured" <?= !empty($post->is_featured) ? 'checked' : '' ?>><span class="tb-builder__check-text">Featured <?= esc($content_label_lower) ?></span></label>
                        <?php else: ?>
                        <div class="tb-builder__design-note"><i class="fa-solid fa-circle-info"></i><span>This reusable <?= esc($content_label_lower) ?> can use only blocks registered for the <?= esc($post_type) ?> design context. Select it as active from Headers &amp; Footers after saving.</span></div>
                        <?php endif; ?>
                        <div class="tb-builder__custom-palette">
                            <div class="tb-builder__palette-heading"><span class="tb-builder__label">Content palette</span><div class="tb-builder__palette-file-actions"><button class="tb-builder__palette-file-button" type="button" data-tb-palette-import-button><i class="fa-solid fa-file-import"></i><span>Import</span></button><button class="tb-builder__palette-file-button" type="button" data-tb-palette-export><i class="fa-solid fa-file-export"></i><span>Export</span></button><input class="tb-builder__hidden-file" type="file" accept="application/json,.json" data-tb-palette-import></div></div>
                            <div class="tb-builder__palette-grid" data-tb-custom-palette><?php foreach ($postPalette as $key => $value): ?><label class="tb-builder__color-field"><span class="tb-builder__color-name"><?= esc(ucwords(str_replace('-', ' ', $key))) ?></span><input class="tb-builder__color" type="color" value="<?= esc($value) ?>" data-tb-palette-color="<?= esc($key) ?>"></label><?php endforeach; ?></div>
                            <span class="tb-builder__hint" data-tb-palette-file-status>Import or export this content palette as a JSON file.</span>
                        </div>
                    </div>
                </div>
                </div>
            </aside>
        </div>

        <section class="tb-block-browser" data-tb-block-browser hidden role="dialog" aria-modal="true" aria-labelledby="tb-block-browser-title">
            <header class="tb-block-browser__header">
                <div class="tb-block-browser__heading">
                    <span class="tb-block-browser__kicker">Thunder Blog</span>
                    <div class="tb-block-browser__title-row"><h2 class="tb-block-browser__title" id="tb-block-browser-title">Browse <?= esc($post_type) ?> blocks</h2><span class="tb-block-browser__result-count" data-tb-result-count></span></div>
                </div>
                <label class="tb-block-browser__search"><i class="fa-solid fa-magnifying-glass tb-block-browser__search-icon"></i><input class="tb-block-browser__search-input" type="search" placeholder="Search block names, descriptions or categories" data-tb-block-search></label>
                <label class="tb-block-browser__mobile-category"><span class="tb-block-browser__mobile-category-label">Category</span><select class="tb-block-browser__category-select" data-tb-block-category><option value="">All categories</option></select></label>
                <button class="tb-block-browser__close" type="button" data-tb-close-library title="Close block browser"><i class="fa-solid fa-xmark"></i><span>Close</span></button>
            </header>

            <div class="tb-block-browser__body">
                <aside class="tb-block-browser__categories">
                    <div class="tb-block-browser__section-label">Categories</div>
                    <nav class="tb-block-browser__category-list" aria-label="Block categories" data-tb-category-list></nav>
                </aside>

                <section class="tb-block-browser__results" aria-label="Block results">
                    <div class="tb-block-browser__result-list" data-tb-block-list></div>
                    <div class="tb-block-browser__pagination" data-tb-library-pagination></div>
                </section>

                <section class="tb-block-browser__preview" aria-label="Selected block preview">
                    <header class="tb-block-browser__preview-header">
                        <div class="tb-block-browser__preview-info">
                            <span class="tb-block-browser__preview-category" data-tb-browser-preview-category>Select a block</span>
                            <h3 class="tb-block-browser__preview-title" data-tb-browser-preview-title>Block preview</h3>
                            <p class="tb-block-browser__preview-description" data-tb-browser-preview-description>Choose a compatible block from the results to inspect its real HTML output.</p>
                        </div>
                        <button class="tb-block-browser__add-selected" type="button" data-tb-add-selected disabled><i class="fa-solid fa-plus"></i><span>Add to <?= esc($content_label_lower) ?></span></button>
                    </header>
                    <div class="tb-block-browser__preview-toolbar">
                        <span class="tb-block-browser__preview-label">Preview size</span>
                        <div class="tb-block-browser__breakpoints">
                            <button class="tb-block-browser__breakpoint tb-block-browser__breakpoint--active" type="button" data-tb-browser-breakpoint="390" title="Mobile preview"><i class="fa-solid fa-mobile-screen"></i></button>
                            <button class="tb-block-browser__breakpoint" type="button" data-tb-browser-breakpoint="768" title="Tablet preview"><i class="fa-solid fa-tablet-screen-button"></i></button>
                            <button class="tb-block-browser__breakpoint" type="button" data-tb-browser-breakpoint="1200" title="Desktop preview"><i class="fa-solid fa-desktop"></i></button>
                            <button class="tb-block-browser__breakpoint" type="button" data-tb-browser-breakpoint="full" title="Full-width preview"><i class="fa-solid fa-arrows-left-right-to-line"></i></button>
                        </div>
                    </div>
                    <div class="tb-block-browser__preview-stage">
                        <div class="tb-block-browser__preview-shell" data-tb-browser-preview-shell>
                            <iframe class="tb-block-browser__preview-frame" title="Selected block HTML preview" sandbox="allow-scripts" scrolling="auto" data-tb-browser-preview></iframe>
                        </div>
                    </div>
                </section>
            </div>
        </section>

        <section class="tb-image-browser" data-tb-image-browser hidden role="dialog" aria-modal="true" aria-labelledby="tb-image-browser-title">
            <header class="tb-image-browser__header">
                <div class="tb-image-browser__heading"><span class="tb-image-browser__kicker">Thunder Blog</span><div class="tb-image-browser__title-row"><h2 class="tb-image-browser__title" id="tb-image-browser-title">Choose an image</h2><span class="tb-image-browser__result-count" data-tb-image-result-count></span></div></div>
                <label class="tb-image-browser__search"><i class="fa-solid fa-magnifying-glass tb-image-browser__search-icon"></i><input class="tb-image-browser__search-input" type="search" placeholder="Search image names or categories" data-tb-image-search></label>
                <label class="tb-image-browser__mobile-category"><span class="tb-image-browser__mobile-category-label">Category</span><select class="tb-image-browser__category-select" data-tb-image-category><option value="">All categories</option></select></label>
                <button class="tb-image-browser__close" type="button" data-tb-close-image-browser title="Close image browser"><i class="fa-solid fa-xmark"></i><span>Close</span></button>
            </header>
            <div class="tb-image-browser__body">
                <aside class="tb-image-browser__categories"><div class="tb-image-browser__section-label">Folders</div><nav class="tb-image-browser__category-list" aria-label="Image categories" data-tb-image-category-list></nav></aside>
                <section class="tb-image-browser__results" aria-label="Image results"><div class="tb-image-browser__grid" data-tb-image-list></div><div class="tb-image-browser__pagination" data-tb-image-pagination></div></section>
                <section class="tb-image-browser__preview" aria-label="Selected image preview">
                    <div class="tb-image-browser__preview-stage"><div class="tb-image-browser__preview-empty" data-tb-image-preview-empty><i class="fa-regular fa-image"></i><strong>Select an image</strong><span>Images are loaded from category folders under assets/images/library.</span></div><img class="tb-image-browser__preview-image" alt="Selected library image" data-tb-image-preview hidden></div>
                    <footer class="tb-image-browser__preview-footer"><div class="tb-image-browser__preview-info"><span class="tb-image-browser__preview-category" data-tb-image-preview-category>Image library</span><strong class="tb-image-browser__preview-name" data-tb-image-preview-name>No image selected</strong><span class="tb-image-browser__preview-size" data-tb-image-preview-size></span></div><button class="tb-image-browser__use-button" type="button" data-tb-use-image disabled><i class="fa-solid fa-check"></i><span>Use image</span></button></footer>
                </section>
            </div>
        </section>
    </div>
</form>
<script src="<?= esc(blog_look_http('assets/js/builder.js')) ?>"></script>
