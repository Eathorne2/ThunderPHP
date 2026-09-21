# Thunder Blog

Version: **1.12.0**

Thunder Blog is a standalone, block-based publishing plugin for ThunderPHP, covering posts, standalone pages, reusable headers and footers, structured menus, routed search, dynamic shortcodes, images and palettes.







## Version 1.12.0

- Widened the single-post `.thunder-blog__article-content` wrapper to `1120px` across all six looks.
- Added 30 original `Architecture & Building` blocks for post and page designers.
- The block library now contains 320 blocks total and reports `Architecture & Building (30)`.
- No database migration changes are required.

## Version 1.11.0

- Improves the general **Font size (px)** element control so its unsaved input value is seeded from the selected element’s computed font size reported directly by the editor iframe. This prevents an untouched/blank number input from jumping to its minimum when the user begins adjusting it.
- The computed value is display-only until the user edits the input; selecting an element does not create or save a font-size override. Clearing an existing override still returns the element to the block CSS size.
- Adds 30 original **Sports** blocks covering sports clubs, teams, fitness training, running, yoga, boxing, football, basketball, coaches, facilities, swimming, tennis, martial arts, schedules, fixtures, statistics, memberships, scoreboards, athletes, galleries, cycling, golf, nutrition, sportswear and tournament registration.
- The block library now contains 290 blocks total and the category browser reports `Sports (30)` automatically.
- No database migration is required.

## Version 1.10.0

- Adds a general **Font size (px)** control to every selected registered element in the block Inspector. When no override exists, the input reads the element’s computed font size from the editor iframe; editing the value stores a per-element override, and clearing it returns the element to the block CSS size. Linked clones share the same font-size appearance state.
- Font-size overrides are compiled into public content alongside element colour/background overrides.
- Adds 30 original **Medicine & Science** blocks covering hospitals, clinics, doctors, dentistry, therapy, pharmacies, diagnostics, laboratories, research, biotechnology, clinical trials, cardiology, pediatrics, women’s health, telemedicine and scientific publications.
- The block library now contains 260 blocks total and the category browser reports `Medicine & Science (30)` automatically.
- No database migration is required.

## Version 1.9.0

- Fixes the editor Inspector by using a full-height grid column with a nested sticky panel, so the Inspector remains pinned beneath the sticky editor topbar while its active tab scrolls independently.
- Adds 30 original **Cars & Transportation** blocks for vehicle, dealership, rental, EV, repair, detailing, taxi, logistics, trucking, parking, motorcycle, bus and roadside-assistance sites.
- The block library now contains 230 blocks total and the block browser reports `Cars & Transportation (30)` automatically.
- No database migration is required.

## Version 1.8.1

- Fixed `position: sticky` throughout the post/page/header/footer editor when rendered inside Thunder Admin.
- Thunder Admin's `.ta-main` (`overflow-x: hidden`) and `.ta-page-content` (`overflow-x: auto`) were creating non-scrolling overflow ancestors that captured sticky positioning. The Blog editor now locally switches those wrappers to `overflow-x: clip` and `overflow-y: visible` on desktop, preserving horizontal clipping without creating a scroll container.
- This restores sticky behavior for the editor top bar, Inspector, and other sticky editor UI without changing Thunder Admin globally.

## Version 1.8.0

- Added 30 original **Food & Restaurant** blocks for post and page design, covering restaurant heroes, chef features, signature dishes, menu lists, pizzerias, burgers, bakeries, coffee shops, fine dining, reservations, locations, galleries, reviews, catering, healthy food, nutrition, farm-to-table stories, desserts, ice cream, seafood, grill menus, recipes and online ordering.
- The block library now contains 200 blocks total and the category browser reports `Food & Restaurant (30)`.
- Fixed the block-browser preview height listener so preview iframe messages are handled before the main-canvas source guard.
- Every selected block preview now receives a render token; height reports from an older selection are ignored, while the current preview resizes its iframe and shell after render, image loads, DOM changes and responsive reflow.
- No database migration changes are required.

## Version 1.7.0

- Added 30 original **Education** blocks for post and page design, inspired by the breadth of current education website patterns: schools, universities, online learning, language programs, courses, certificates, teachers, libraries, campus life, children’s learning, research, alumni, quizzes, study travel, pricing and enrollment.
- The block library now contains 170 blocks total and the existing category counter reports `Education (30)`.
- Block-browser selected previews now report their rendered height to the parent editor and automatically resize the preview iframe/shell, removing the iframe scrollbar for tall blocks.
- Fixed desktop Inspector sticky behavior by allowing the non-fullscreen `.tb-builder` ancestor to use visible overflow; this removes the overflow containing block that prevented `position: sticky` from following the page scroll.
- The Inspector keeps its own settings scrollbar while sticky, and the existing fullscreen behavior is preserved.
- No database migration changes are required.

## Version 1.6.0

- Added 30 original **Wedding** blocks inspired by common wedding-site patterns: save-the-date heroes, love stories, schedules, venues, RSVP/invitation sections, galleries, wedding-party profiles, registries, guest travel, bridal fashion, flowers, cakes, planner services, photography, testimonials, packages, checklists, palettes and guest information.
- Wedding blocks are available in both post and page designer contexts.
- The block browser automatically reports `Wedding (30)` through the existing category counter.
- No database migration changes are required.


## Version 1.5.0

- Added 30 original `Fashion & Beauty` blocks for post and page design.
- New layouts cover editorial fashion heroes, collections, trends, lookbooks, boutiques, accessories, runway schedules, designer stories, beauty treatments, skincare, makeup, barber services, spa, nails, salons, fragrance, testimonials and galleries.
- Fashion & Beauty blocks use editable element schemas, repeatable items where appropriate, semantic Thunder Blog palette colours, and stock photography URLs.
- Block-browser category counts automatically expose the new category as `Fashion & Beauty (30)`.
- Added the requested `.tb-block-browser__result-list { max-height: 80vh; overflow: auto; }` rule across all six looks.
- No database migration changes are required.

## Version 1.4.2

- Inspector stays pinned beside the editor on desktop and fills the available viewport height, with its own settings scrollbar.
- The block browser now scrolls as one full-screen document instead of scrolling its results and preview columns independently.
- `tb-block-browser__results` is no longer a grid container, preventing paginated block cards from being compressed.
- The main canvas stage no longer owns a scrollbar; normal page/fullscreen-builder scrolling handles long documents.

## Version 1.4.1

- Fixed full-screen block-browser overflow so the category pane, paginated block result list, and selected-block preview can scroll independently inside the browser viewport.
- The block preview iframe explicitly allows scrolling for tall block previews.
- On desktop editor layouts, the Inspector stays visible and its active tab scrolls independently from the canvas/page. This lets you adjust long settings panels while keeping the canvas at the same visual position.
- Fullscreen editor mode keeps both the canvas stage and Inspector as independent scroll regions.
- Narrow/mobile layouts retain normal document scrolling rather than forcing nested inspector scrolling.
- No database migration changes are required.

## Version 1.4.0

- Expanded the block library to 80 blocks total.
- Header designer now includes exactly 30 dedicated header blocks.
- Footer designer now includes exactly 30 dedicated footer blocks.
- Added original layouts inspired by modern multi-row, logo/menu, CTA, contact, search, social, newsletter, sitemap and publication patterns.
- Block-browser category labels now display context-aware counts, for example `Headers (30)` and `Footers (30)`.
- The mobile category dropdown displays the same counts.
- No database migration changes are required.


## Version 1.3.0

- Menu item edit fields now wrap automatically within the available admin panel width.
- Added four frontend looks: Newsprint, Studio Cards, Horizon Magazine, and Signal.
- Thunder Blog now ships with six selectable frontend looks in total.


## Version 1.2.2 changes

- Added separate editable eyebrow text for the blog index and blog search page.
- New posts and pages default the publish date field to the current date and time.
- `home` is no longer a reserved standalone-page slug, so `/home` can be created with the page designer when no earlier application route handles it.


## Version 1.2.1 changes

- Fullscreen builder mode now keeps `.tb-builder__stage-scroll` scrollable, so long canvases remain navigable without moving the surrounding editor chrome.
- Blog and search hero titles/subtitles are configurable from settings.
- View-count visibility is configurable alongside author/date/category metadata.
- Custom menu URLs accept `{{ROOT}}/...` and resolve it at render time.
- Menu-editor form panels have additional padding.
- Menu-aware blocks render temporary Home/About/Blog/Contact placeholder items inside the editor until a saved menu is selected; public output remains empty until a real menu is chosen.

## Features

- Public listing at `/blog`
- Public post pages at `/blog/{slug}`
- Routed blog search at `/blog/search?q=...`
- Data-only menu designer with post, page and custom links
- Menu-aware header, footer and standalone navigation blocks
- Standalone designed pages at `/{slug}`
- Reusable block-designed headers and footers
- Dynamic listing shortcode generator
- Category and tag archives
- Admin post CRUD
- Full-screen block browser opened from the editor toolbar
- File-based, paginated and categorized block library
- Lightweight paginated results that load a full manifest only for the selected block
- Large live HTML preview in a sandboxed iframe
- Focused full-width editor canvas and inspector
- Full-width canvas by default, with mobile, tablet and desktop preview modes
- Wide-canvas and browser fullscreen modes
- Independent mobile, tablet, desktop and full-width block-browser preview controls
- Global and per-post colour palettes
- Per-post palette import and export using portable JSON files
- Configurable frontend looks
- Schema-aware external user-table mapping with table and column dropdowns
- Thunder Authentication author filters when that plugin is installed
- Canonical server-side block compilation on save
- Two-level block and element selection in the canvas
- Per-element inspector fields with move, duplicate, linked clone, detach and delete actions
- Named-palette text and background colour selectors for compatible elements
- Direct text editing in the canvas by double-clicking editable text
- Plain-text paste handling that strips formatting during canvas editing
- Categorized, paginated image browser backed by plugin image folders
- Featured-image resizing to a maximum 1000px using `\Core\Image`
- Namespaced CSS and vanilla JavaScript



## Version 1.2.0

- Added a menu designer at `/admin/blog/menus`. Menus store names, item titles, destinations, parent relationships, targets and order without storing presentation markup.
- Menu items can source a blog post, standalone page or custom URL. Post and page destinations are resolved from their current slugs when rendered.
- Added a `menu` block-setting field and `menu_sources` manifest section. One saved menu can be rendered through different block-owned HTML and CSS designs.
- Menu-enabled documents recompile their menu sources at render time, so editing one saved menu immediately updates every header, footer, page and post that uses it.
- Converted the original brand, centered, simple-footer and column-footer blocks to saved-menu sources.
- Added Horizontal Menu, Vertical Menu and Pill Menu blocks for posts, pages, headers and footers.
- Added Logo, Menu and CTA Header; Top Bar, Menu and Search Header; Centered Menu Footer; and Newsletter and Menu Footer blocks.
- Added `/blog/search` with paginated title, excerpt and slug matching.
- Updated every public search input supplied by Thunder Blog to submit naturally to `/blog/search`.
- Added database tables `thunder_blog_menus` and `thunder_blog_menu_items`.

## Version 1.1.0

- Fixed author resolution by using an actual `\Core\Session` instance, saving the mapped current-user key, querying the complete external user row, and passing that row through Thunder Authentication author filters.
- Replaced free-text author schema settings with live database-table and column selectors plus a current-user mapping preview.
- Added standalone pages created with the existing block designer and published at `/{slug}`. Posts remain at `/blog/{slug}`.
- Added reusable header and footer content types, context-specific block libraries, active-design selectors, and automatic frontend rendering.
- Added trusted HTML and ThunderPHP shortcode blocks.
- Added a shortcode generator for small, medium, large, grid and horizontal post listings with latest, popular, most-viewed, featured, popular-this-week and oldest ordering.
- Added page-layout blocks with main/sidebar, three-column and feature/feed regions designed for dynamic shortcodes.
- Added header blocks for brand navigation and centered editorial mastheads, plus simple and multi-column footer blocks.
- Added `post_type` and `is_featured` publishing fields and changed slug uniqueness to be scoped by content type.

## Version 1.0.4

- Added move, independent duplicate, linked clone, detach and delete controls to every included editable element definition.
- Independent duplicates inherit the current values but no longer share future changes with the source.
- Linked clones share field values and element palette choices; editing any clone updates all members of its clone group.
- Duplicate blocks deliberately detach all element clone groups, keeping copied blocks independent.
- Added manifest-controlled element text-colour and background-colour selectors.
- Colour selectors expose only semantic names from the active post palette, never arbitrary raw colours.
- Element colour choices remain semantic, so switching or importing the post palette updates rendered colours without rewriting block data.
- Added server-side clone and palette-token normalization before compiled HTML is saved.

## Version 1.0.3

- The post canvas now opens in full-width preview mode.
- The iframe and frame shell automatically follow the rendered document height, removing the canvas scrollbar and leaving normal page scrolling.
- Replaced the flat block property document with a native block-and-element document schema.
- Clicking a block exposes block-wide settings and block movement, duplication, and deletion controls.
- Clicking a registered element exposes only that element's inspector fields.
- Repeatable element groups can independently allow movement, duplication, and deletion, with manifest-defined minimum and maximum item counts.
- Added a repeatable Image Gallery starter block with editable images, captions, column count, gap, and aspect ratio.
- FAQ items, statistic cards, and text columns are now repeatable element groups.
- Clicking blank canvas space clears both block and element selection for an unobstructed preview.
- This version intentionally uses the new element document format and does not convert block JSON saved by earlier releases.


## Version 1.0.2

- Added JSON file import and export for each post's colour palette.
- Featured-image uploads are resized proportionally so their longest side is no more than 1000px.
- Replaced generated image placeholders in image-led starter blocks with remote Unsplash stock photographs.
- Added direct canvas text editing: double-click supported text, edit it in place, then click away to save it back into the block document.
- Pasted canvas text is inserted as plain text so copied formatting is discarded.
- Added a full-screen image browser opened from image inspector fields, the featured-image field, or by double-clicking an image in the canvas.
- Added paginated image search and folder-based categories under `assets/images/library`.
- Added starter image folders for `business`, `general`, `nature`, and `people`.

## Version 1.0.1

- Replaced the permanently visible block sidebar with a toolbar-triggered full-screen browser.
- Added category navigation, search, result counts and numbered previous/next pagination.
- Added a dedicated large iframe that renders the selected block's actual HTML, CSS and JavaScript.
- Added mobile, tablet, desktop and full-width controls to the browser preview.
- Added quick-add buttons, double-click insertion and a primary Add to post action.
- Kept block-list responses lightweight for libraries containing thousands of blocks.

## Installation

Copy `thunder-blog` into the ThunderPHP plugins folder, then run:

```text
php thunder migrate thunder-blog
```

Activate the plugin and visit:

```text
/blog
/admin/blog
/admin/blog/pages
/admin/blog/menus
/admin/blog/designs
/admin/blog/shortcodes
/admin/blog/settings
```

The initial author mapping targets Thunder Authentication:

```text
Table: auth_users
Primary key: id
Display name: username
Email: email
Profile slug: username
```

No database foreign key is created against the external users table. The post stores the configured external primary-key value in `thunder_blog_posts.author_id`, which keeps the plugin portable across integer, UUID and string user keys.

## Adding blocks

1. Create `blocks/your-block/block.json`.
2. Add its lightweight metadata to `blocks/index.json`.
3. Keep every CSS selector under a `.tb-block...` class.
4. Use semantic palette variables such as `--tb-color-primary` and `--tb-color-surface`.
5. Put block-wide fields under `settings.schema`.
6. Register selectable content under `elements`, giving each element its own `schema`, `template`, defaults, and optional repeatable controls.
7. Insert each element group into the block HTML with `{{elements:group-key}}`.

The library API pages the lightweight block index without loading thousands of complete HTML/CSS/JavaScript manifests. Blocks declare `contexts` (`post`, `page`, `header`, or `footer`), so each designer sees only compatible blocks. A full manifest is fetched only when a block is selected for the iframe preview or added to the document.


## Creating and using menus

Open `/admin/blog/menus`, create a menu, then add blog posts, standalone pages or custom links. Every item supports a title, parent item, display order and link target. Choosing a parent creates hierarchy; the block decides whether that hierarchy appears as dropdowns, nested sidebar links, footer columns or another design.

A compatible block exposes a **Saved menu** selector under Block settings. Menu-enabled documents resolve their saved menus at render time. Changing a menu title, hierarchy, custom URL, linked post/page slug, or item order therefore updates every block that uses that menu without reopening those documents.

## Adding images to the image browser

Place images in immediate subfolders of:

```text
assets/images/library/
```

Every folder becomes a browser category. For example:

```text
assets/images/library/
├── business/
│   ├── office-team.jpg
│   └── laptop-desk.webp
├── nature/
│   └── forest-path.jpg
└── people/
    └── portrait.png
```

Supported formats are JPEG, PNG, WebP, GIF, and SVG. Descriptive filenames are converted into readable image titles. The image API loads only the requested page, so the library can grow without sending every image to the editor at once.

Within an element schema, a field may include a CSS `selector` relative to that element's template root. Use `:scope` when the template root itself is editable. Text, textarea, and rich-text fields with selectors can be edited by double-clicking the matching canvas content. Image fields with selectors open the image browser. Fields without selectors remain inspector-only controls.

## Block variables

Available shared palette variables:

```text
--tb-color-primary
--tb-color-primary-hover
--tb-color-secondary
--tb-color-accent
--tb-color-background
--tb-color-surface
--tb-color-text
--tb-color-muted
--tb-color-border
--tb-color-success
--tb-color-warning
--tb-color-danger
```

## Extension hooks

Actions:

```text
thunder_blog_before_index
thunder_blog_after_index
thunder_blog_before_search
thunder_blog_after_search
thunder_blog_before_post
thunder_blog_after_post
thunder_blog_post_saved
thunder_blog_post_deleted
thunder_blog_settings_saved
foundation_render_blog_page
```

Filters:

```text
thunder_blog_block_index
thunder_blog_author
thunder_blog_post_query
thunder_blog_render_page
thunder_blog_compiled_post
```

Thunder Authentication filters used when available:

```text
auth_user_display_name
auth_user_profile_url
auth_user_avatar
```

## Security model

- All admin POST actions use CSRF verification.
- Every admin route checks its permission.
- User-table identifiers are restricted to valid SQL identifiers.
- Block HTML/CSS/JS comes from trusted plugin manifests.
- Inspector values are compiled again on the server rather than trusting browser-generated HTML.
- Image uploads validate MIME type, extension and size, then use `\Core\Image::resize()` to constrain the longest side to 1000px.
- Preview iframes use sandboxed documents.
