## Changes in 0.12.97

- Fixed Run Action Hook and Run Filter Hook placement when their reusable component markers are inserted inside HTML Design Components connected through an HTML Boilerplate.
- The compiler now scans the complete document shell for `<tvi-component>` references before applying fallback placement.
- A named hook component renders only at its marker when that marker is used anywhere in the final View document.
- When no matching marker is present, the hook component retains the existing fallback behavior and renders at its normal View Graph position.
- The placement fix applies to nested component markers generally, while preserving duplicate protection and component dependency handling.

## Changes in 0.12.96

- Fixed generated HTML Component stylesheets when components are connected directly to an HTML Boilerplate node through **Page content**.
- Boilerplate compilation now reserves a document-level stylesheet insertion point, so both the shared View Builder CSS and the generated per-View HTML Component CSS load once before the first inserted component.
- Removed all default `do_action()` calls from the HTML Boilerplate template. New boilerplates now contain only the social metadata document shell and `<tvi-content />` inside the centered `<main>` element.

## Changes in 0.12.95

- Added a **Do Action Component** to the View Builder. It compiles to a positioned `do_action()` call and can be inserted inside an HTML Design Component through a normal `<tvi-component>` marker.
- The component provides editable hook-name, data-variable, and component-name fields. The data variable defaults to `$data` and safely falls back to an empty array without overwriting it.
- Custom action hook names declared by Do Action Component and Run Action Hook nodes are added automatically to the editable action-hook dropdowns on Controller Hook and View nodes.
- Compacted the HTML Component Designer toolbar from Node title through the Preview expand control so the fields and icon buttons stay on fewer rows.

## Changes in 0.12.94

- Replaced browser datalist hook suggestions with a proper editable dropdown on Controller Hook and View nodes.
- Standard hooks can be selected from the dropdown, while the same field still accepts any custom hook name.
- Controller Hook options continue to switch automatically between action and filter hooks.
- Added keyboard support: Arrow Up/Down opens and navigates the dropdown, Enter selects, and Escape closes it.

## Changes in 0.12.93

- Added Fluid, Desktop (1200px), Tablet (768px), and Mobile (375px) controls to the HTML Component Designer preview header.
- The responsive preview width is remembered in the browser and remains compatible with the expanded Preview panel.
- HTML Design Component CSS is now extracted into one generated, cacheable stylesheet per View or Reusable View instead of being emitted as inline `<style>` blocks inside component partials.
- Generated component styles remain automatically scoped, and their guarded stylesheet link is placed immediately before the first visual component insertion.
- Added editable hook-name suggestions to Controller Hook and View nodes. Controller suggestions change between the standard action and filter hooks according to the selected hook type.

## Changes in 0.12.92

- Added 25 original Image Galleries snippets, increasing the category from 5 to 30 entries.
- Added 15 multi-image layouts including editorial mosaics, filmstrips, filtered portfolios, proofing grids, panoramas, comparisons and archive presentations.
- Added 10 single-image detail/page layouts for photography, artwork, products, travel, architecture, museum objects, fashion campaigns, restoration comparisons and fullscreen viewing.
- Every new stylesheet uses named Look theme variables, and all new `preview.svg` files are intentionally empty.
- This release is based on the stable 0.12.90 code line; the discarded optional-wrapper changes from 0.12.91 are not included.

## Changes in 0.12.90

- Added a collapsible color palette panel to the HTML Snippet Library.
- The palette starts expanded for new users and can be minimized to a slim side rail while browsing snippets.
- The minimized preference is stored in the browser and the selected colors remain unchanged when the panel is collapsed.
- On narrower screens the minimized palette becomes a compact horizontal bar.

## Changes in 0.12.89

- HTML Block nodes now emit the entered HTML directly when both wrapper fields are empty.
- Loop and Render Reusable View nodes no longer create an automatic container unless wrapper classes or wrapper styles are supplied.
- Chart nodes retain only their required chart element by default; their optional responsive outer wrapper is created only when wrapper styling is configured.
- Responsive width classes are no longer enough by themselves to force wrappers on these content-rendering nodes.

## Changes in 0.12.88

- Existing-plugin replacement approval is remembered in memory for the current project session. Deploying or updating the same project again does not reopen the modal.
- Replacement approval resets automatically on page reload and whenever another project, sample, import, or blank workspace replaces the current project.
- The replacement modal now uses calmer wording and a normal primary action instead of destructive warning styling.

## Changes in 0.12.86

- A clean first launch now opens a blank project. Browser recovery data is still restored automatically when present; bundled samples remain available from the Samples browser.
- Test deployment and test-file updates now require explicit confirmation before replacing any existing plugin folder, including a previous TVI test build.
- Generated Views no longer declare a separate `$__tvi_read` closure. The compiler now calls the framework-level `get_nested_value()` helper for dot-notation data reads.

Add this helper to ThunderPHP's global functions file before compiling plugins with this version:

```php
function get_nested_value(mixed $scope, string $path, mixed $default = null): mixed
{
    $segments = array_values(array_filter(
        explode('.', $path),
        static fn (string $segment): bool => $segment !== ''
    ));

    if ($segments === []) {
        return $default;
    }

    $value = $scope;

    foreach ($segments as $segment) {
        if (is_array($value)) {
            if (!array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
            continue;
        }

        if ($value instanceof \ArrayAccess) {
            if (!$value->offsetExists($segment)) {
                return $default;
            }

            $value = $value[$segment];
            continue;
        }

        if (is_object($value)) {
            if (!isset($value->{$segment}) && !property_exists($value, $segment)) {
                return $default;
            }

            $value = $value->{$segment};
            continue;
        }

        return $default;
    }

    return $value;
}
```

# Thunder Visual IDE 0.12.97

## Changes in 0.12.85

- Added reusable JSON export and import for Pagination Component custom colors.
- Pagination design browsing now previews every design with the colors or theme mappings of the Pagination node that opened the browser.
- New pagination nodes added from that browser retain the source node's palette settings.
- Added all reusable HTML snippet color presets to the Look Form Input Theme palette selector.
- Reusable presets stay selected while switching form designs, making it easier to compare the same colors across different input styles.
- Look nodes now retain the selected reusable palette identity in addition to the resolved color map.
- Form-theme and snippet color exports preserve the reusable palette selection when applicable.

## Changes in 0.12.84

- Audited all built-in HTML snippet categories and identified every category containing fewer than five snippets.
- Added 71 original snippets across 28 existing categories, bringing every category to a minimum of five built-in options.
- Added practical designs for alerts, audio, cards, commerce, contact, content, dashboards, feedback, galleries, sliders, marketing, menus, navigation, products, progress, search, articles, social blocks, sticky sidebars and video.
- New layouts take broad inspiration from contemporary Nicepage block patterns such as split sections, layered images, grids, sliders, forms, tabs, galleries and product presentations while remaining original.
- Every new stylesheet uses named Look theme variables; no fixed hex, RGB, HSL, black or white palette values are included.
- All 71 new `preview.svg` files are intentionally empty because the snippet browser no longer uses preview images.
- The built-in HTML snippet library now contains 411 snippets across 47 categories, with a minimum of five snippets in every category.


## Changes in 0.12.83

- Restored `<tvi-content />` to the default HTML Boilerplate as an optional direct View Builder fallback alongside `html_content`.
- Added 30 Business & Law snippets: 20 general components and 10 coordinated header/footer sets.
- All new Business & Law styles use named Look theme variables with no fixed color palette overrides.

- Added three pagination color modes: template defaults, custom colors, and inherited Look theme colors.
- Added per-role theme mapping for pagination button backgrounds, text, borders, hover, active, disabled and summary colors.
- Updated every bundled pagination design to expose a reusable default palette and color tokens in its stylesheet.
- Added a terminal Refresh flow node that generates `redirect(current_url());` followed by `exit;`.
- Removed the instructional comment from the PHP Code node's default editor content.
- Updated the HTML Boilerplate default body to render `html_header`, `html_content`, and `html_footer` inside a centered 1200px `<main>` element.
- Hook-based boilerplates no longer append direct View content after the closing HTML document; `<tvi-content />` remains available for explicit direct insertion.

## Changes in 0.12.81

- Added a dedicated **Travel & Hotels** category with 30 original snippets.
- Added 20 practical components covering resort booking, destination search, destination grids, rooms, amenities, reservations, travel packages, itineraries, guest reviews, galleries, local experiences, spa, hotel dining, transfers, FAQs, memberships, offers, travel journals, destination guides and concierge contact.
- Added 10 coordinated responsive header and footer sets for coastal resorts, alpine lodges, city hotels, safari camps, desert retreats, island villas, heritage hotels, adventure tours, yacht charters and eco lodges.
- The layouts take broad inspiration from contemporary Nicepage travel and hotel template patterns while remaining original and editable.
- Every new stylesheet uses the connected Look's named semantic theme variables for backgrounds, surfaces, text, borders, accents, buttons, overlays and shadows.
- New Travel & Hotels `preview.svg` files are intentionally empty because preview images are no longer used by the snippet browser.
- The built-in HTML snippet library now contains 310 snippets, including exactly 30 entries in **Travel & Hotels**.

## Changes in 0.12.80

- Audited all 60 snippets in the **Wedding** and **Music & Entertainment** categories for palette compatibility.
- Replaced fixed header/footer palette variables with the connected Look's semantic theme variables.
- Replaced hard-coded colors in general snippets, including gradients, overlays, borders, shadows, buttons, muted text, panels and media controls.
- Updated the Wedding Dress Code Guide swatches to use theme variables instead of fixed colors.
- Removed hard-coded fallback colors from theme-variable declarations in these two categories so the active Look palette remains authoritative.

## Changes in 0.12.79

- Added a dedicated **Wedding** category with 30 original snippets.
- Added 20 practical sections covering save-the-date announcements, countdowns, couple stories, schedules, RSVP, venues, travel, wedding parties, galleries, registries, guest FAQs, dress codes, accommodation, menus, vendors, planning services, testimonials, pricing, invitations, seating lookup and post-wedding thanks.
- Added 10 coordinated responsive header and footer sets for garden, coastal, modern, black-tie, rustic, Italian destination and bohemian weddings, plus wedding planner, bridal boutique and wedding photography businesses.
- The layouts take broad inspiration from contemporary wedding-template patterns while remaining original and editable.
- All new HTML, CSS and JavaScript is expanded and human-readable, with isolated class prefixes, semantic Look theme variables and scoped vanilla JavaScript.
- New Wedding `preview.svg` files are intentionally empty because preview images are no longer used by the snippet browser.
- The built-in HTML snippet library now contains 280 snippets, including exactly 30 entries in **Wedding**.

## Changes in 0.12.78


- Added a dedicated **Music & Entertainment** category with 30 original snippets.
- Added 20 practical sections covering festivals, album releases, tour dates, playlists, tickets, bands, discographies, podcasts, radio, nightclubs, cinema, entertainment news, esports, talent agencies, venues, fan memberships, merchandise, galleries and artist newsletters.
- Added 10 coordinated responsive header and footer sets for festivals, jazz clubs, record labels, nightclubs, solo artists, cinemas, esports networks, radio stations, theatres and comedy clubs.
- The layouts take broad inspiration from modern music and entertainment template patterns while remaining original and editable.
- All new HTML, CSS and JavaScript is expanded and human-readable, with isolated class prefixes, semantic Look theme variables and scoped vanilla JavaScript.
- New Music & Entertainment `preview.svg` files are intentionally empty because preview images are no longer used by the snippet browser.
- The built-in HTML snippet library now contains 250 snippets, including exactly 30 entries in **Music & Entertainment**.

## Changes in 0.12.77

- Added a dedicated **Fashion & Beauty** category with 30 original snippets.
- Added 20 practical sections covering runway campaigns, lookbooks, category shopping, beauty services, skincare, fragrance, salon booking, creative profiles, new arrivals, sizing, style discovery, memberships, nail services, testimonials, journals, brand values, store locations and limited releases.
- Added 10 coordinated responsive header and footer sets for fashion ateliers, boutiques, skincare brands, beauty salons, editorial labels, spas, perfume houses, barber studios, makeup brands and jewelry studios.
- All new HTML, CSS and JavaScript is expanded and human-readable, with isolated class prefixes, semantic Look theme variables and scoped vanilla JavaScript.
- New Fashion & Beauty `preview.svg` files are intentionally empty because preview images are no longer used by the snippet browser.
- The built-in HTML snippet library now contains 220 snippets, including exactly 30 entries in **Fashion & Beauty**.

## Changes in 0.12.76

- Added 10 coordinated food-themed header and footer sets to the **Food & Restaurant** snippet category.
- Added fine-dining, bakery, seafood, sushi, farm-to-table, pizzeria, vegan café, coffee-roastery, street-food and event-catering designs.
- Every set includes responsive navigation, matching footer styling, readable HTML/CSS/JavaScript and isolated component class names.
- New food header/footer `preview.svg` files are intentionally empty because preview images are no longer used by the snippet browser.
- The built-in HTML snippet library now contains 190 snippets, including 30 entries in **Food & Restaurant**.

## Changes in 0.12.75

- Added 20 professional food and restaurant snippets in a dedicated **Food & Restaurant** category.
- Added restaurant, bakery, café, catering, recipe, delivery, seafood, private dining, wellness and organic-market layouts.
- Added interactive menu filters, reservation controls, serving adjustment, product configuration, sliders and location tabs.
- Food snippet source remains expanded and human-readable across HTML, CSS and JavaScript files.
- New food snippet `preview.svg` placeholders are intentionally empty because preview files are no longer used by the browser.

## Changes in 0.12.74

- Added 20 professional corporate HTML snippets inspired by modern business-template composition: split layouts, layered images, over-grid content, structured service blocks, executive profiles, client proof, pricing, contact and editorial insight sections.
- Added two Hero Sections: Executive Advisory Hero and Technology Partnership Hero.
- Added two coordinated Header and Footer Sets for global consulting and financial institutions.
- Added two Icon Grids, two Grid Layouts, two User Cards and two Testimonial Sliders for corporate services, industries, case studies, leadership and client results.
- Added Consultation Request and Global Offices contact forms, Enterprise Pricing, Annual Report and Strategy Session calls to action, a Corporate Trust Logo Strip, a Corporate Page Intro and Executive Insights article cards.
- Interactive snippets use readable, component-scoped vanilla JavaScript for navigation, filtering, sliders, office tabs, pricing toggles, profile expansion and form feedback.
- The built-in HTML snippet library now contains 160 snippets.

## Changes in 0.12.73

- Added ten modern editorial elements across existing snippet categories while preserving the magazine-inspired visual direction introduced in 0.12.72.
- Added two Authentication snippets: Editorial Members Login and Collectors Registration.
- Added two Product Cards: Limited Drop Product Card and Gallery Edition Product Card.
- Added two User Cards: Editorial Contributor Card and Creative Director User Card.
- Added two coordinated Header and Footer Sets: Culture Journal and Neo Gazette.
- Added Edition Launch Hero and Print Club Call to Action.
- Interactive additions use readable vanilla JavaScript for password visibility, image switching, save/follow states, mobile navigation, and form feedback.
- The built-in HTML snippet library now contains 140 snippets.

## Changes in 0.12.72

- Added ten modern magazine and editorial snippets with oversized typography, asymmetric grids, layered photography, issue numbering, cinematic layouts and responsive behavior.
- Added the **Magazine & Editorial** snippet category with cover stories, contents boards, story grids, photo essays, interview spreads, trend displays and a premium newsletter section.
- Added three reusable snippet color presets: Electric Lime, Ink & Paper, and Digital Sunset.
- The built-in HTML snippet library now contains 130 snippets, and the universal snippet palette collection now contains ten presets.
- All new HTML, CSS and JavaScript remains separated, human-readable and editable.

## Changes in 0.12.71

- Added seven reusable color palette presets to the HTML Snippet Library: Coastal Blue, Emerald Grove, Amber Studio, Ruby Editorial, Violet Signal, Graphite Dark, and Warm Sand.
- The palette selector now separates palettes supplied by the current form theme from the universal snippet color presets.
- Applying a snippet preset saves its semantic colors to the Look without changing the selected form design.
- Color export and import preserve the selected snippet preset when possible.

## Changes in 0.12.70

- Added six coordinated Header and Footer Sets to the built-in HTML snippet library.
- New options include Aurora Glass, Commerce, Creative Studio, Midnight SaaS, Local Service, and Boutique Hotel designs.
- Every set includes matching header and footer styling, responsive navigation, readable HTML/CSS/JavaScript, isolated class prefixes, and Look theme variables where appropriate.
- The built-in snippet library now contains 120 snippets, including nine complete Header and Footer Sets.

## Changes in 0.12.69

- Added a full-screen button to the HTML Snippet Library preview.
- The preview panel uses the browser Fullscreen API when available, allowing snippets to be inspected at the real screen width while retaining the snippet title and Add buttons.
- Added a fixed-position IDE fallback for browsers that do not support native element fullscreen.
- The button switches between expand and contract icons and updates its accessible label while full screen is active.

## Changes in 0.12.68

- Added 36 new built-in HTML snippets, with three readable options in each requested group.
- Added coordinated Header and Footer Sets so both page regions share one visual direction.
- Added new Profiles, User Cards, Hero Sections, Call to Action sections, Sticky Sidebars, Video Players, Audio Players, Testimonial Sliders, Social components, Grid Layouts, and Icon Grids.
- Every new snippet keeps HTML, CSS, and JavaScript in separate human-readable files with unique class prefixes and semantic Look theme variables.
- Interactive snippets use readable vanilla JavaScript scoped through the component `root`.

## Changes in 0.12.67

- Node presets are displayed newest first.
- Execution-flow Start nodes can no longer be deleted, duplicated, copied into presets, or exported as ordinary nodes.
- The model/class method add/edit panel now scrolls independently so long help text and method bodies remain accessible.
- Added **Array Push**, which appends one connected value to a connected array and returns the updated array.
- Form-theme palette files now provide separate **Import Theme & Colors** and **Import Colors Only** actions, allowing colors to be reused with a different form design.
- Create Table migration columns now support `ENUM` with editable comma-separated values.
- The Variable node now supports global, namespaced, and class constants.

## Changes in 0.12.66

- Added an **HTML Boilerplate** root node to the View Builder.
- The full HTML5 document is editable in the normal code editor and includes responsive, canonical, robots, Open Graph, LinkedIn/Facebook-compatible, and X/Twitter social metadata by default.
- Added a `<tvi-content />` marker that inserts the owning View node's source followed by child visual components connected to the boilerplate.
- Included the standard `html_head` and `html_footer` action hooks in the default document.
- The shared View Builder stylesheet remains standards-compliant: it is emitted inside the document immediately before the first connected visual component, never before `<!doctype html>` or `<html>`.
- If the content marker is removed, compilation falls back to inserting content before `</body>` and reports a warning.


## Changes in 0.12.65

- Added a **Load Code Asset** View Builder node for selecting existing architecture Code Asset nodes instead of manually typing `<link>` or `<script>` tags.
- The node lists every available Code Asset with its type, destination path and connected Look.
- CSS and JavaScript tags are generated at the node's exact View Builder position and may also be placed inside normal named visual components.
- Selected assets use their owning Look path, so an explicitly selected asset works even when it belongs to a different Look.
- Added a request-scoped URL guard so the same selected asset is emitted only once across multiple Views and reusable partials during one page load.
- The utility node does not load the shared View Builder component stylesheet when it is the only visual node in a View.


## Changes in 0.12.64

- Added a request-scoped guard around the shared `assets/css/thunder-view-components.css` link.
- When several Views or Reusable Views render during the same route request, the shared component stylesheet is emitted only before the first visual component insertion.
- Route-less Views use the same guard, so they can render on every route without duplicating the stylesheet when another View has already loaded it.
- The guard is keyed by the resolved stylesheet URL, allowing different plugins or Looks to load their own distinct component stylesheet when necessary.



## Changes in 0.12.63

- Component base CSS is no longer prepended to the beginning of every generated View file.
- The shared `assets/css/thunder-view-components.css` link is now emitted immediately before the first visual component insertion in each View or Reusable View.
- Component-owned scoped CSS remains inside its generated component partial and is emitted immediately before that component's first markup instance.
- Preserved source indentation when replacing explicit `<tvi-component>` markers with the CSS link and component include.


## Changes in 0.12.62

- Moved the shared form theme and palette from individual View and Reusable View nodes to the Look node.
- Look nodes are now required for architecture compilation, alongside the required Plugin node.
- The compiler now generates one Look-level `frontend/theme-colors.php` file and loads it once through `before_view` at priority `100`.
- Added Look options to enable or disable automatic theme loading and customize the theme file path, hook name, and hook priority.
- Removed per-View form-theme CSS and JavaScript assets so multiple Views on one page use one consistent theme scope.


## Changes in 0.12.61

- Reformatted every bundled HTML snippet package so its HTML, CSS and JavaScript are consistently indented and easier to edit.
- Expanded previously compressed markup into clear nested structures while preserving element order, attributes and rendered content.
- Expanded compact CSS into one declaration per line with readable selectors, values, media queries and keyframes.
- Expanded compact JavaScript callbacks and event handlers into readable blocks without changing component behavior.
- Reformatted the HTML snippet marketplace package template to use the same readable source style.


## Changes in 0.12.60

- Added **Copy Permission** to duplicate an existing permission entry with a unique copied name and slug.
- Added **Copy Column** to duplicate an existing Create Table column while preserving its type and options.
- Form-theme color inputs now update the live preview directly as colors are typed or selected.
- Fixed the HTML Component Designer JavaScript expansion so CodeMirror remains visible while its backing textarea stays hidden.
- Reusable Views now expose the same form-theme chooser, palette preview and theme-aware View Builder/snippet previews as normal Views.


## Changes in 0.12.59

- Added a connectable **Value** input to **Empty Check**.
- A connected value is checked directly with `empty(...)` and takes priority over the inspector settings.
- The existing variable-path fallback remains available for undefined-safe nested array and object checks using dot notation.


## Changes in 0.12.58

- Added **PHP Input** for reading `php://input` once, exposing the raw body, decoded JSON and a JSON-valid boolean.
- Added **File Get Contents** with path/URL, stream context, offset and length inputs plus native contents and strict success outputs.
- Added **File Put Contents** with append, exclusive lock, include-path and warning-suppression options plus bytes-written and strict success outputs.
- File reads and writes are flow nodes so each operation executes once even when its result feeds several downstream nodes.

## Changes in 0.12.57

- Added **Isset Check** for testing whether a PHP variable or nested array/object value exists and is not `null`.
- Added **Empty Check** for testing PHP empty-value semantics without undefined-variable warnings.
- Added **Type Check** with common PHP predicates including `is_numeric`, `is_string`, `is_int`, `is_float`, `is_bool`, `is_array`, `is_object`, `is_null`, `is_scalar`, `is_iterable`, `is_countable`, `is_callable`, and `is_resource`.
- Isset and Empty paths support dot notation with selectable array or object access.


## Changes in 0.12.56

- Added **Set JS Variable** for declaring JavaScript variables or assigning values to existing variables and property paths.
- Event Listener now has a configurable **Event variable name**, defaulting to `e`, and uses that identifier throughout its callback branch.
- Added **PHP Value to JS**, which safely serializes a PHP expression with `json_encode()` before the owning deferred JavaScript Flow asset loads.
- AJAX Request now has a configurable **XHR variable name**, defaulting to `xhr`, available throughout all request callback branches. Each request is block-scoped so multiple AJAX nodes can safely reuse the same name.

## Changes in 0.12.55

- Added a first-class **JavaScript Flow** nested graph to normal Views and Reusable Views, keeping browser behavior separate from server Flow graphs and visual HTML hierarchy.
- Added browser-side execution and value socket families so PHP values, View-child connections, and JavaScript values cannot be connected accidentally.
- Added XMLHttpRequest-based AJAX orchestration with upload/download progress, success, HTTP error, network error, timeout, abort, before-send and completion branches.
- Added View-component event targeting, theme-aware AJAX progress bars, DOM updates, form data, browser-side arrays/objects/concatenation, conditions, delays and custom JavaScript.
- JavaScript Flow output is generated as a dedicated Look asset and loaded with `defer` only when the owning View contains browser behavior.
- Preview Generated Code now targets the owning View's generated JavaScript asset when opened from a JavaScript Flow.

## Changes in 0.12.54

- Added **Select Next Occurrence** to every editable CodeMirror instance with `Ctrl+Shift+F` and `Cmd+Shift+F`.
- Each press retains existing selections and adds the next exact match, wrapping through the document and skipping matches already selected.
- When there is no selection, the first press selects the word under the cursor; subsequent presses add matching occurrences for simultaneous editing.


## Changes in 0.12.53

- Added View Builder **Run Action Hook** for invoking `do_action()` exactly where the node or named component is rendered.
- Added View Builder **Run Filter Hook** for rendering `do_filter()` output inline, with optional HTML escaping.
- Both nodes accept a connectable Data value and can be converted into named `<tvi-component>` placements for embedding hooks inside hand-written View HTML.

## Changes in 0.12.52

- Added a dynamic **Concatenate** data node for joining two to thirty graph inputs into one string, with an optional separator.
- Rebuilt **Preview Generated Code** as a folder browser: breadcrumbs show the current directory and the sidebar lists only immediate folders and files.
- Generated-code preview now opens the file most relevant to the active graph and selected architecture node, including `plugin.php` for controller flows, View files for View Builder graphs, model/class files for method flows, and migration files for migration graphs.

## Changes in 0.12.51

- Uses the bundled plugin logo as the browser favicon across the IDE and tutorial pages.
- Adds a Flow **Unset Variable** node with validated dot-notation array paths.
- Adds 30 built-in HTML snippets: three modal alerts, three inline alerts, and three each for progress bars, user cards, search bars, footers, menu headers, tables, authentication and tab navigation.
- Every new snippet has isolated class names, package-local CSS and root-scoped JavaScript so multiple component instances can coexist.


## Changes in 0.12.50

- Added a connectable **Initially Open** input to Modal nodes while retaining the inspector checkbox as a fallback.
- Data Variable, Page Value Get and Boolean nodes can now be used in View graphs as expression sources.
- Added the **Route Scope** architecture node for explicitly compiling `routes.on` and `routes.off` prefix arrays.

## Changes in 0.12.49

- Fixed Chart View compilation so the Chart.js source-variable placeholder is resolved before the loader is inserted into generated PHP.
- Added a regression check ensuring Chart View output contains no unresolved `%%...%%` template tokens.

### Reusable Views and modal components

- Added **Reusable View**, an architecture node with a nested View Builder that emits a Look partial without registering a route or hook.
- Added **Render Reusable View**, with an architecture-aware dropdown for including reusable partials in any normal View graph.
- Added **Modal**, a visual container with backdrop, positioning, sizing, z-index, Escape, backdrop-close and page-scroll-lock options.
- Added **Modal Trigger**, whose first HTML element can open, close or toggle a connected Modal. Any number of triggers can target one modal, including close triggers placed inside modal content.
- Added a shared modal runtime with delegated events, focus restoration, keyboard focus trapping, trigger `aria-expanded` state and the `ThunderModal` JavaScript API.
- Added dedicated modal-target graph connections that do not interfere with visual parent/child placement.

### Bundled Chart.js fallback

- Bundles the standalone Chart.js 4.5.0 UMD build for generated Chart View nodes at `assets/js/chart.min.js`.
- Leaving the Chart View custom path empty copies and loads the bundled file automatically.
- A custom Chart.js path overrides the bundled asset without copying it.

## Changes in 0.12.46

### Chart.js graph workflow

- Added **Chart Dataset** for defining one styled Chart.js dataset from graph data.
- Added **Chart Builder** with dynamic dataset inputs, common chart options, advanced options merging, and `set_value()` transfer to the View graph.
- Added **Array Column** for extracting object or array properties—including dot-notation paths—from query-style rows into chart labels or values.
- Added **Chart View** for rendering the prepared configuration through the bundled/global Chart.js instance, with empty-state handling and safe chart replacement during repeated rendering.
- Added inspector controls for adding and removing Chart Builder dataset ports without manually editing the count.
- Chart View can render inline or as a reusable View component and supports an optional custom Chart.js source override.

## Changes in 0.12.45


- Added **Mute Node** / **Unmute Node** to the graph context menu for non-structural nodes.
- Muted flow and migration nodes are bypassed when they have one execution output; muted branch nodes stop that path with a compiler warning instead of choosing a branch arbitrarily.
- Muted architecture and View nodes are omitted, while muted data inputs behave as disconnected values. Common fluent method nodes pass their service object through so a muted chain step can be skipped safely.
- Added **Group Selected** and **Manage Presets…** to graph context menus.
- Updated plugin authorship to **Eathorne Choongo** and added the support email to `config.json` and the About dialog.

## Changes in 0.12.44

### Asset Studio column focus correction

- Corrected the Asset Studio header controls so clicking a column expands that column and collapses the other two into narrow rails.
- Clicking the already-focused column restores the normal three-column layout, while clicking another rail changes focus directly.
- Added version query strings to the main editor CSS and JavaScript assets so browser caching cannot leave the previous independent-collapse behavior active after an IDE update.

### Asset Studio editor access and focused columns

- Corrected Asset Studio source-field interaction so editable custom package inputs receive pointer and keyboard events normally, including the expanded CodeMirror view.
- Strengthened nested Code Editor stacking by moving it above the Asset Studio modal while it is open.
- Built-in package source remains read-only but selectable for inspection and copying.
- Replaced independent column collapsing with a focused-column toggle: clicking a column header button expands that column and collapses the other two into rails; clicking the same button again restores the normal three-column layout.
- Clicking a collapsed rail's button focuses that column directly.
- Kept the package editor at the smaller relative width requested, leaving more default room for assets and preview.

## Changes in 0.12.42

### Asset Studio workspace, visual palettes and PHP editor tags

- Rebalanced the Asset Studio so the package editor starts at roughly half its previous share, giving more room to the asset library and live preview.
- Added independent collapse rails for the Assets, Package editor, and Preview/checks columns.
- Added **Expand** controls to every Asset Studio HTML, CSS, JavaScript, and component-template source input, using the IDE's full CodeMirror editor.
- Replaced the raw palettes JSON editor with a visual palette manager that can create, duplicate, select, rename, delete, set defaults, add semantic color tokens, and preview changes immediately.
- Clarified in the UI and documentation that form-theme preview HTML does not create graph fields, and HTML snippets are inserted as one HTML Design Component rather than being extracted into separate input nodes.
- Updated the PHP Code node so an initial `<?php` tag can be used for syntax highlighting; the compiler removes that opening tag and an optional final closing tag before generating the containing PHP flow.

## Changes in 0.12.41

### Marketplace Asset Studio and reliable chain dragging

- Added **Marketplace → Asset Studio…** for authoring, previewing, validating, importing and exporting distributable View form-theme and HTML-snippet ZIP packages.
- Built-in themes and snippets remain read-only. They can be inspected or copied into a new custom package, while edit and delete actions are restricted to packages under `storage/marketplace/`.
- Added package metadata for stable IDs, versions, authors, websites, licenses, categories, tags and ordering.
- Added live sandboxed previews and rule checks for reusable snippet fragments, scoped CSS, isolated JavaScript, required form-template tokens and semantic theme palettes.
- Custom packages are loaded by the normal theme/snippet registries and included in full IDE data export, import and clear operations.
- Added self-contained ZIP export/import formats suitable for distributing or selling marketplace assets.
- Replaced unreliable native dragging from the Method Browser with a pointer-driven drag interaction and visible drag grip, while retaining double-click and **Add to chain** alternatives.

## Changes in 0.12.40

### Visual Method Chain Builder

- Expanded the Method Browser with a drag-and-drop linear Chain Builder for framework services, models, and custom classes.
- Methods can be appended by double-clicking, dragged into the chain, reordered, configured, and removed before graph creation.
- Added an equivalent PHP preview, chain validation, terminal-method checks, and safety warnings for operations such as unrestricted update or delete calls.
- Parameter values can be left as graph inputs or generated from normal String, Number, Boolean, Null, Variable, and Array data nodes.
- **Create Chain in Graph** emits ordinary method-call nodes and normal connections; no persistent chain object or special chain-editing state is stored.
- Added chainable method metadata and visual badges. Custom model and class methods can explicitly enable **Can continue a method chain**.
- Added a Boolean data node for chain parameters and other graph workflows.

## Changes in 0.12.39

### Restore simple collapsed-folder behavior

- Removed collapsed-folder proxy sockets and their temporary connection rendering.
- Collapsed folders once again hide their member nodes and all related connection lines, matching the original stable behavior.
- Expanding a folder restores the original nodes and connections without modifying saved graph data or generated PHP.
- Retained the curved connection rendering for normal visible node-to-node connections.

## Changes in 0.12.38

### Folder drag connections and embedded framework method reference

- Corrected live connection geometry while dragging collapsed folders by anchoring edges to the visible socket position and scheduling edge measurement after node transforms are applied.
- Added full Markdown help, examples, behavioral notes, and method groups for all 21 Image methods and all 29 Session methods.
- Expanded all 32 Request methods with examples, upload behavior, validation guidance, return-shape notes, and practical security guidance from the supplied Request help file.
- Aligned the Session Method node's `message()` metadata with the current three-parameter signature and documented the safe check-without-erasing, display-with-erasing pattern.

## Changes in 0.12.37

### Query Builder nodes, method documentation and folder proxies

- Added **Query Builder Service** and **Query Builder Method** nodes backed by one shared `\Core\Database` instance.
- Added all 41 public methods from the supplied QueryBuilder trait with dynamic typed ports, exact signatures, descriptions, and Markdown examples.
- Added a searchable two-column **Method Browser** for Model Method Call, Class Method Call, Request Method, Session Method, Image Method, and Query Builder Method nodes.
- Added Markdown help information to model and custom-class method definitions.

## Changes in 0.12.36

### Request Service and Request Method refresh

- Revalidated **Request Service** and **Request Method** against the supplied current `\Core\Request` documentation.
- Confirmed all 32 documented methods are available, covering request state, POST/GET input, uploaded files, upload configuration, upload execution, upload errors, server data and headers.
- Added documentation-derived method groups and descriptions to the editable Request Method metadata.
- Added a selected-method signature and description panel in the inspector for Request, Session and Image method nodes.
- Corrected dynamic method port typing so methods documented as returning an object expose an object Result port instead of mixed. This is especially useful for Request upload setter chaining.
- Expanded the Request node package documentation with method signatures, upload rule keys and chaining guidance.

## Changes in 0.12.35

### Flash Message erase control

- Added an **Erase after display** checkbox to the View Builder Flash Message node.
- Updated generated PHP to check message availability with `message($type, '', false)` and only call `message($type, '', true)` while rendering the message.
- Removed the obsolete Message ID field so the node matches the current three-parameter `message(string $type, ?string $msg = '', bool $erase = false): ?string` signature.
- Existing Flash Message nodes without the new property default to erasing after successful display.

## Changes in 0.12.34

### ThunderPHP Session nodes

Added **Session Service** and **Session Method** under the Session category. Session Service exposes the shared `\Core\Session` instance already used by Session Get and Session Set nodes.

Session Method reads editable method metadata from `nodes/Session/Method/node.json` and changes its parameter connectors according to the selected method. Included defaults cover session data and dot notation, arrays, authentication, lifecycle, flash messages, old input, counters, raw data, user utilities, and the Session message helper.

## Changes in 0.12.33

- Added recursive `<tvi-component>` support inside HTML Design Components and generated component partials.
- Added missing-component warnings and direct/indirect circular dependency protection.
- Nested component CSS and JavaScript are deduplicated while component HTML remains repeatable.
- Added an **Insert component** selector to the HTML Component Designer.
- Component markers are inserted at the current HTML cursor or selection, with end-of-content fallback when no cursor is available.
- View-source marker actions now use the current Inspector code-field cursor when available.
- The live HTML preview resolves nested HTML components and shows approximate visual trees, loops, missing references, and cycles.

## Changes in 0.12.32

- Added **Wrapper CSS classes** and **Wrapper inline styles** to the View Builder Loop node.
- Loop wrapper styling is applied to its existing outer `div` while retaining responsive width classes.

## Changes in 0.12.31

- Added inline-style fields wherever View Builder components expose custom CSS classes.
- Form controls now support separate wrapper, control, and inline-error style declarations.
- Pagination components support inline styles for wrappers, controls, states, ellipses, and summaries, including live preview updates.
- Loop nodes now hoist child CSS and executable JavaScript outside the generated `foreach`, deduplicate identical blocks, and emit them once per loop.

## Changes in 0.12.30

- The IDE is now consistently described as a **node-based visual programming IDE**.
- Added the supplied Thunder Visual IDE logo to the main workspace, About modal, tutorial library, and plugin thumbnail.
- The About modal now reads name, version, author, plugin ID, and website directly from `config.json`.
- Added six form themes: Accent Edge, Glass Frost, Compact Admin, Neon Night, Side Labels, and Capsule Forms, bringing the bundled total to ten.
- Added 24 HTML snippets: two each for Product Cards, User Cards, Image Galleries, Image Sliders, Contact Us Forms, Page Title Headings, Menus, Headers, Footers, Blog Post Cards, Single Blog Posts, and Single Product Pages.
- Expanded the bundled HTML snippet library from 24 to 48 designs.

## Changes in 0.12.29

- Added a new **Help** menu with Tutorials and About actions.
- Added the folder-backed `/thunder-ide/tutorials` library with category filtering and search.
- Added individual tutorial pages with optional YouTube, Vimeo, or local video playback.
- Added downloadable companion-file ZIPs generated directly from each tutorial's `files/` folder.
- Added a tutorial registry driven by `tutorials/index.json` and per-tutorial `tutorial.json` files.
- Added three starter written tutorials and a copy-ready `tutorial-package-template/`.
- Added an About modal showing the IDE version, author, purpose, plugin ID, storage model, tutorials link, and ThunderPHP website.


## Changes in 0.12.28

- Prevented link navigation inside every live preview iframe.
- Replaced snippet category tabs with a compact category dropdown.
- Fixed malformed gradient CSS in bundled snippets, including both authentication designs and the marketing hero.
- Raised toast notifications above the snippet browser and improved text contrast.


- HTML snippets now use the owning View's named theme colors instead of fixed design-specific colors.
- Added one central sandboxed iframe to the Snippet Library; selecting a snippet updates that live preview.
- Added the View theme palette selector and live color editor directly to the Snippet Library.
- Palette edits can be reset, imported, exported, and applied back to the View so form inputs and HTML components stay synchronized.
- HTML Design Components now inherit the View theme scope class in generated markup.
- Added semantic palette tokens for on-primary text, success, warning, and informational states.

## Changes in 0.12.26

- Added conditional inline validation messages to visible form input components.
- Each input can use an automatic or custom error path and display the first message or all messages.
- Form themes can position errors through the `{{error_html}}` template token.

- Added folder-based View-level themes for built-in Forms, Text, Email, Password, Number, Date and File inputs, Textareas, Selects, Checkboxes and Buttons.
- Added a live iframe theme browser opened from the selected View node.
- Added four bundled themes: Classic Clean, Floating Labels, Minimal Underline and Soft Panel.
- Added named theme palettes with live color editing, palette defaults, and portable color import/export.
- Theme selection belongs to the View rather than individual form nodes, so copied nodes automatically adopt the destination View's theme.
- Generated theme CSS is isolated per View, and optional theme JavaScript is emitted as a View-specific asset.
- Added the extensible `form-themes/` folder format and authoring documentation in `FORM_THEMES.md`.

## Changes in 0.12.24

- Added optional raw PHP default values to class and model method parameters.
- Constructor, class-method and model-method call nodes omit trailing unconnected optional arguments so PHP uses the declared defaults.
- Dynamic call-node ports and method signatures display configured defaults for visual clarity.
- Query Builder starter insert and update methods now pass data through `filterInsertData()` and `filterUpdateData()` before persistence.

## Changes in 0.12.21

- Replaced raw validation-rule JSON on Validate nodes with a visual rules manager.
- Included all documented Core\Validate rules through editable definitions in `validation-rules/rules.json`.
- Rules are grouped by category and show descriptions, syntax, parameters, optional display names and custom error messages.
- Added advanced unique-rule options for table, column, primary key, ignored values, scoped `where` conditions and `whereNot` conditions.
- Validate Form Schema uses the same manager for extra or replacement rules.
- Custom application rules remain supported, and the underlying JSON can still be edited directly.

### Adding a validation rule definition

Edit `validation-rules/rules.json`, add the rule metadata, then reload the IDE. The compiler continues to emit the normal `Core\Validate::setRules()` array format.

## Changes in 0.12.20

- Controller nodes can now register either `add_action()` or `add_filter()` callbacks.
- Added configurable callback argument names, defaulting to `$data`.
- Added a dedicated **Hook Data Argument** expression node that automatically follows the owning controller's callback variable.
- Filter graphs receive protected Start and Return Filter Data nodes.
- Filter callbacks always return the supplied argument, including a compiler fallback for incomplete branches.

## Changes in 0.12.19

- Export any selected node group directly to a portable `.thunder-nodes.json` file.
- Import node bundles into the current graph from the canvas context menu.
- Keep the Node Library search field visible while its node list scrolls.
- Wrap HTML snippet category tabs so all folders can be seen without horizontal scrolling.

## Changes in 0.12.18

- Added a terminal **Exit** flow node that generates `exit`, `exit($value)`, `die`, or `die($value)`.
- Added a **Display Data** debugging node that generates `dd($data);`.
- Added optional named result variables to class instances, model/class/function/helper calls, hook filters, image operations, request methods, form data, and Pager creation.
- Validation nodes now expose optional names for both the internal validator object and errors array.
- Named variables are displayed directly on nodes as `$variable` badges.
- Blank variable fields preserve the existing automatic node-specific variable naming, so existing projects remain compatible.

## Changes in 0.12.17

- Lowered generated View Builder form-control selector specificity with `:where()`, allowing later custom CSS such as `.my-field input` to override defaults normally.
- Added CSS custom-property hooks for control background, text color, border, radius, padding, and textarea sizing.
- Split form-control styling into **Wrapper CSS classes** and **Control CSS classes**.
- Existing `css_class` project data remains the wrapper class, eliminating the previous duplicate class on both wrapper and control.
- Updated Text, Email, Password, Number, Date, File, Textarea, Select, and Checkbox node documentation with override examples.

## Changes in 0.12.16

- Removed the IDE page and API `user_can()` checks, and removed the IDE access permission from `config.json`.
- The IDE now works on a clean ThunderPHP installation without an authentication plugin, login, role, or session.
- Disabled IDE API CSRF verification and removed frontend CSRF token generation.
- Added **Export All IDE Data ZIP** for server-saved projects, library archives, browser presets, the recovery copy, and the open project.
- Added **Import All IDE Data ZIP** to replace and restore that complete dataset.
- Added **Clear All User Data** to remove projects, libraries, presets, recovery data, and the current workspace without removing built-in samples, snippets, definitions, or preferences.

## Changes in 0.12.15

- Fixed HTML snippet cards so their Add All, HTML, CSS and JavaScript buttons always remain visible.
- Snippet cards now use content-sized grid rows and visible action overflow inside the paginated snippet modal.
- Added independent Node Library and Inspector visibility controls to the main workspace toolbar.
- Added hide controls inside each visible sidebar header.
- Sidebar visibility is remembered in the browser, and the graph workspace expands into the released space.

## Changes in 0.12.14

- Added a persistent CodeMirror word-wrap preference beside the theme selector.
- Word wrap now applies to generated-code preview, fullscreen source editing, and HTML/CSS/JavaScript component editors.
- Replaced crowded workspace, HTML designer, snippet, and node-library controls with compact inline SVG icon buttons.
- Added accessible `title` and `aria-label` descriptions to icon-only controls.
- Kept text labels where they provide important context, such as menus and destructive confirmation actions.


A full-screen, vanilla-JavaScript visual programming IDE for ThunderPHP. It uses small composable programming nodes, editable node packages, nested execution graphs, reusable presets, framework-aware compilation, and direct test deployment.

## Installation

1. Install the ZIP through the ThunderPHP plugin manager.
2. Open `/thunder-ide`. No authentication plugin, login, role, or permission assignment is required.

The IDE continues to use project schema version 4, so v0.5 projects remain compatible.

## Editable node packages

Every node lives under:

```text
nodes/<group>/<node>/
├── node.json
├── Compiler.php
└── docs.md
```

Edit those files directly and choose **Nodes → Reload Node Definitions**. See `NODE_AUTHORING.md` and `node-package-template/`.






## Changes in 0.12.13

- New HTML Design Components now start with empty HTML, CSS and JavaScript editors.
- Added a destructive **Clear All** action for returning the component studio to a clean slate.
- HTML, CSS, JavaScript and Preview panels can each expand to occupy the full studio workspace.
- Fixed the collapsed JavaScript control being covered by the Preview panel.
- Returning from a nested Controller, View or method graph restores the previously selected owner node in the main graph.
- Added eight more folder-based snippets: Gradient Hero Callout, Three Tier Pricing, Stats Overview Grid, FAQ Accordion, Project Timeline, Toast Notifications, Mini Kanban Board and Breadcrumb Page Header.
- The bundled HTML snippet library now contains 24 designs.

## Changes in 0.12.12

- Expanded the folder-based HTML snippet library from 4 to 16 ready-made designs.
- Added two responsive tables: a searchable customer table and an activity/status table.
- Added split-screen and compact login forms.
- Added a full profile dashboard and a compact profile summary panel.
- Added an interactive team-member card and a searchable user directory.
- Added a photo lightbox gallery and a filterable portfolio gallery.
- Added a split contact form and a support-ticket form.
- Every new snippet includes editable HTML, isolated CSS, optional JavaScript and an SVG preview.

## Changes in 0.12.11

- The HTML Component Designer now occupies the complete browser viewport.
- The designer has its own vertical scrolling, so the lower editor panels and controls remain reachable on shorter screens.
- Header actions wrap instead of being clipped.
- The toolbar and editor grid adapt at narrower widths.
- On small screens, the snippet library and HTML, CSS, JavaScript and Preview panels stack vertically.
- Background page scrolling is locked while the designer is open and restored when it closes.

## Changes in 0.12.9

### Fullscreen HTML Component Designer

Added **Nodes → HTML Component Designer…** and the editable **HTML Design Component** View Builder node. The designer has four live sections:

- HTML
- automatically namespaced CSS
- JavaScript scoped through a component `root` element
- an isolated iframe preview

The preview updates while HTML, CSS, or JavaScript changes. It can be expanded to use the full designer width. Finished components compile into normal reusable View partials and appear in the View inspector beside Forms and Pagination Components, where they can be inserted with a `<tvi-component>` marker.

CSS selectors are prefixed with a generated component scope class, and keyframe names are renamed to reduce collisions with the website or other components. View template expressions such as `{{ user.name }}` remain supported inside component HTML.

HTML components can be saved through the existing View-tree preset system or exported/imported as `.tvi-component.json` files.

### Folder-based snippet library

Ready-made snippets are loaded from:

```text
html-snippets/<snippet>/
├── snippet.json
├── snippet.html
├── style.css
├── script.js
└── preview.svg
```

The bundled library includes a Feature Card, Content Tabs, Testimonial Slider, and Mosaic Gallery. A snippet may append all three code sections at once or insert only its HTML, CSS, or JavaScript. Copy a snippet folder, change its ID and files, then reload the IDE to add your own design without editing the main JavaScript registry.

See `HTML_COMPONENTS.md` for the component and snippet authoring format.

## Changes in 0.12.8

- Added folder-based pagination view templates under `pagination-templates/`.
- Added a visual Pagination Component node powered by `Core\Pager::set_renderer()`.
- Added **Nodes → Pagination Designs…** with live previews and multi-add workflow.
- Pagination components support editable labels, classes, responsive width, base-style removal, custom CSS and normal View markers.
- Pagination components can be saved and reused through the existing View-tree preset system.

### Adding another pagination design

Copy one of the folders under `pagination-templates/`, then edit:

- `template.json` for metadata and defaults
- `renderer.php.tpl` for generated Pager markup
- `style.css` for default styles
- `preview.html` for the design-browser preview

Change the template ID and reload the IDE.

## Changes in 0.12.7

- **All Permissions** now writes permission definitions only to the generated `config.json`. It no longer generates the obsolete top-level `permissions` filter.
- Added a Sample Projects modal under **File → Browse Samples**.
- Included four focused sample projects: Authentication & Profiles, AJAX Contact Form, Paginated Posts & Migration, and Image Upload Pipeline.
- Each sample includes a difficulty label and node-focus tags so new users can choose a project based on what they want to learn.
- The Authentication sample remains the default project when no browser recovery project exists.

## Changes in 0.12.6

- All Permissions contributes its definitions to generated `config.json`. Runtime permission discovery no longer requires loading the plugin.
- Nested Controller, View, Function, Model method, Class method and Migration graphs show a prominent context banner with the owner name.
- Added portable project package ZIP export/import.
- Package export can include uploaded Asset files, library ZIP archives, both, or neither.
- Imported packages restore Asset node file contents and register bundled libraries in local IDE storage automatically.
- Plain `.thunder.json` import/export remains available for lightweight projects.


## Changes in 0.12.5

### ThunderPHP Request nodes

Added **Request Service** and **Request Method** under the Request category. Request Service exposes the same shared `\Core\Request` instance used by existing form, URL and AJAX nodes. Request Method reads editable method metadata from `nodes/Request/Method/node.json` and changes its parameter connectors according to the selected method.

Included defaults cover request method checks, POST/GET/all input, file access, upload configuration, per-field upload rules, upload execution and errors, IP/URI/server/header values, and the documented chainable setter methods.

## Changes in 0.12.4

### ThunderPHP Image nodes

Added **Image Processor** and **Image Method** under the Images category. Image Processor creates and configures `\Core\Image` with JPEG, PNG and WEBP quality, thumbnail naming, upscale behavior and an optional thumbnail directory.

Image Method exposes editable method metadata from its `node.json`. Selecting a method changes its input ports to match the method signature. Included methods cover resize, crop, contain, format conversion, thumbnail generation, uploads, binary data, generic processing, metadata, browser output and runtime configuration. Complex method options connect through normal Array nodes.

## Changes in 0.12.3

### Bundled PHP Library Package node

Added **Library Package** under Libraries. Upload a ZIP of a folder-based PHP library, choose its generated destination, and optionally load an autoloader from `plugin.php`. Uploaded archives are stored in the IDE under `storage/libraries/`, keeping project JSON and build requests small. The compiler supports normal stored and deflated ZIP entries and rejects unsafe, encrypted, or Zip64 archives.

For an mPDF package, a typical destination is `libraries/mpdf` and the autoloader is `vendor/autoload.php`, provided the uploaded ZIP contains that structure.

### Plugin dependency manager

The Plugin node no longer requires manually typed dependency JSON. Its inspector now provides a structured manager for plugin ID, name, version requirement, and the required flag. Generated `config.json` retains ThunderPHP's normal associative dependency format.

## Changes in 0.12.2

### Public hook invocation nodes

- **Run Action Hook** compiles to `do_action($hookName, $data)` and continues the execution flow.
- **Run Filter Hook** compiles to `do_filter($hookName, $data)`, exposes the modified value, and continues the flow.
- Both nodes live in editable packages under `nodes/Hooks/`.

### Unscoped Controller and View hooks

Controller and View nodes now generate hooks even when they are not connected to a Route. The fourth route-name argument is omitted in that case:

```php
add_action('shared_plugin_hook', function ($data = []) {
    // Compiled graph or View loader
}, 10);
```

The callback payload is available to a Variable node named `data`. If any Controller or View is unscoped, generated `config.json` uses `routes.on: ["all"]` so the plugin is loaded broadly enough to register callbacks for hooks invoked by other plugins. Route-connected lifecycle nodes continue to generate route-specific callbacks.

## Changes in 0.12.1

### Update test plugin files

**Build & Export → Update Test Plugin Files** compiles and copies the current generated plugin into the ThunderPHP plugins directory without opening another browser tab. It uses the same safe overwrite confirmation and IDE self-protection as normal test deployment.

### Editable CSS and JavaScript assets

Added the **Code Asset** node under Looks. It creates a normal CSS or JavaScript file inside a connected Look and uses the same fullscreen CodeMirror editor as View source.

Connect a Code Asset to a Look, select a View connected to that Look, and use the View inspector to insert an asset marker:

```html
<tvi-asset name="main-styles" />
```

During compilation, CSS markers become `current_look_http()` stylesheet links and JavaScript markers become script tags. Because the marker refers to the asset node by name, changing its destination path keeps generated View links synchronized.

## Changes in 0.11.2

### ThunderPHP authorization filters

- **All Permissions** defines the catalog written into generated `config.json`.
- **All Roles** now adds role definitions to the associative array received by the top-level `roles` filter and returns it.
- **Set Current Permissions** is now an architecture node that creates a top-level `user_permissions` filter. Enter literal slugs or open its nested Permission Loader graph to load and return permissions from a Model or another source.
- **Set Current Roles** creates the equivalent top-level `user_roles` filter and nested Role Loader graph.
- Runtime permission and role hooks are no longer generated inside Controller, View, Function, or Method callbacks.

### Combined conditions

Added **Combine Conditions**, with a configurable number of boolean inputs and an AND/OR selector. It compiles to grouped `&&` or `||` expressions and connects directly to the Condition input of an If node.


## Changes in 0.11.1

- Prevents transient browser text selection while nodes, selection boxes, or the canvas are dragged.
- Show All reveals manually hidden nodes without expanding collapsed Visual Folders.
- Adds Ctrl/Cmd+C and Ctrl/Cmd+V node copy/paste across compatible nested graphs.
- Copy/paste preserves internal connections and nested graphs while retaining references to shared project definitions.
- Adds visual Controller and View hook order badges per Route.
- Hook order follows vertical canvas position and compiles to route-specific ThunderPHP priorities.
- Adds Run Earlier and Run Later controls in the Controller/View inspector.

## Changes in 0.11.0

### Viewport focus tools

- Added **Fit All** and **Show All** controls above the canvas.
- Right-click a selection for **Fit Selection to View** or **Focus on Selection**.
- Focus on Selection hides unrelated nodes without changing generated code. Show All clears editor-hidden state while preserving collapsed Visual Folders.
- Fit operations calculate the actual node bounds and cap automatic zoom at 125%.
- Canvas text selection is disabled.
- Moving a Visual Folder now moves all of its members, whether the folder is expanded or collapsed.

### Route-aware test deployment

When exactly one GET Route node is selected, **Deploy Test Plugin** opens that route. Otherwise it opens the first created GET route. Dynamic route parameters are filled with `1` for testing.

### Authorization nodes

- **All Permissions** manages the permission catalog written into generated `config.json`.
- **All Roles** manages the role catalog and registers it through the top-level ThunderPHP `roles` filter.
- **Set Current Permissions** owns a nested loader flow and creates a top-level `user_permissions` filter.
- **Set Current Roles** owns a nested loader flow and creates a top-level `user_roles` filter.
- **Has Permission** compiles to `user_can()`.
- **Has Role** compiles to `contains_role()`.

Permission and role checks use dropdowns populated from the corresponding catalog nodes.

### Type conversion

Added **Cast Value** for array, object, string, integer, float, boolean, JSON-to-array, and JSON-to-object conversions.


## Changes in 0.10.2

- Reformatted all 81 node `Compiler.php` source files for human readability.
- Expanded minified one-line classes, methods, conditions, loops, arrays, and return contributions.
- Refactored larger compilers into named helper methods where useful, especially Controller, View, Model, Custom Class, migration, JSON, validation, and View Builder compilers.
- Limited compiler source lines to 120 characters or fewer.
- Added `COMPILER_STYLE.md` and replaced the node-package compiler template with a documented, readable example.
- Compiler behavior and the schema-v4 project format remain compatible.


## Changes in 0.10.0

- Browser context menus are suppressed throughout the graph canvas while the IDE context menus remain active.
- Added a visual Migration File architecture node with ordered nested migration graphs. Migration operation nodes cover table creation, form-schema table generation, adding/modifying/renaming/dropping columns, indexes, foreign keys, and reversible raw SQL for existing databases.
- Added Merge Arrays for combining Read Form Data with generated or custom fields before model inserts/updates.
- Fixed inspector value leakage between View input nodes by binding each control to the node that created it.
- Lifecycle hook generation now resolves connected Route and Look relationships in either graph direction and deduplicates generated hooks.

## Changes in 0.9.0

### Reliable nested-graph opening

Node selection no longer redraws the node DOM on the first click, so the browser can deliver a real double-click event. Double-click Controller, View and Function nodes to open their nested graph. Double-click Model or Custom Class definitions to open their Method Manager.

### Graph context menus

Right-click a node for Open/Manage Methods, Duplicate, Save as Preset and Delete. Right-click a connection to delete it. Right-click empty canvas to add nodes at that location, select all nodes or reset the view.

### Remaining View Builder nodes

Added Plain Text, Old Input and Empty State nodes. Plain Text safely escapes literal display text, Old Input uses ThunderPHP `old_value()`, and Empty State renders when a View variable path is empty.

### Form schemas and controller integration

Visual Form nodes now act as reusable controller-facing schemas. Fields infer required, email, numeric and date rules and accept additional ThunderPHP validation rules. The new Read Form Data node reads every field through `Core\Request`; Validate Form Schema builds `Core\Validate` rules from the same form definition. This keeps View field names and controller logic synchronized.

### AJAX and JSON response nodes

Added Is AJAX Request, backed by `Core\Request::is_ajax()`, and a terminal JSON Response node that sets the content type and HTTP status, encodes the connected value and exits.

### Selectable CodeMirror themes

Settings → Code Editor Theme provides Thunder Dark, Material Darker, Darcula, Monokai, Nord, Eclipse Light and Neo Light. Theme CSS is stored in `looks/main/assets/vendor/codemirror/theme/`, so more CodeMirror 5 themes can be added later without replacing the editor library.

## Changes in 0.8.1

### Visual View output ordering

View Builder rendering order remains tied to the graph itself. Sibling items are compiled from top to bottom, then left to right. Every visual View node now displays its current sibling order as a numbered badge. The inspector also shows the node's position and provides **Render Earlier** and **Render Later** controls that swap its canvas position with the adjacent sibling.

Ordering is scoped to the same parent and output branch, so children connected to separate If/Else branches are ordered independently.

### CodeMirror source editing and preview

The generated-code preview and fullscreen node source editor now use a bundled CodeMirror editor with syntax highlighting, scrolling, and line numbers. The fullscreen editor also supports:

- `Ctrl+D` or `Cmd+D` to duplicate selected lines
- Alt-click to add another cursor
- `Ctrl/Cmd+Alt+Up/Down` to add cursors vertically
- bracket matching, automatic bracket closing, active-line highlighting, and four-space indentation

The editor is bundled inside the plugin and does not depend on a CDN.

### Test overwrite tab timing

The IDE no longer opens an empty tab before checking for an existing plugin. When destructive overwrite approval is required, the warning remains visible in the IDE. The new test tab is opened only after the user clicks the confirmation button.

## Changes in 0.8.0

### Visual View Builder

Every View node now owns a nested **View Builder** graph. Double-click the View or use **Open View Builder** in the inspector. View Builder nodes are small HTML/layout operations rather than whole pages.

The first node set includes reusable Component and Form roots, Container, Grid, common input types, Textarea, Select, Checkbox, Hidden Input, CSRF, Button, HTML Block, Variable Output, If/Else, Loop, Validation Error, and Flash Message.

### Responsive form layout

Visual elements use a built-in 12-column CSS Grid system with four editable breakpoints:

- Mobile
- Small at 576px
- Medium at 768px
- Large at 1200px

The generated plugin receives `assets/css/thunder-view-components.css`; Bootstrap is not required.

### Reusable visual components

A Form or Component root may have a component name. It compiles to a normal ThunderPHP partial under:

```text
looks/<look>/<view-folder>/components/<component-name>.php
```

Place it in handwritten View source with:

```html
<tvi-component name="signup-form" />
```

The View inspector lists visual components and can insert this marker. Components not explicitly placed are appended to the View. A connected form tree can be saved as a normal node preset and reused in other View Builder graphs.

### Compact template syntax

View source and HTML Block nodes support:

```text
{{ user.name }}
{{ ROOT }}/home
{{ const:App\VERSION }}
{!! article.body !!}
{% if user %} ... {% else %} ... {% endif %}
{% foreach users as user %} ... {% endforeach %}
```

Escaped values use ThunderPHP's `esc()` helper. Dot paths work with arrays and objects through the framework-level `get_nested_value()` helper. Bare all-uppercase identifiers such as `ROOT`, `APP_VERSION`, and `PHP_VERSION` resolve as PHP constants when defined, while still falling back to a View variable of the same name. Use the explicit `const:` prefix for namespaced or mixed-case constants.

## Changes in 0.12.88

### Constants in compact View templates

Escaped and raw template expressions now recognize PHP constants. A bare uppercase value such as `{{ ROOT }}` resolves the constant when it exists and otherwise falls back to the normal View variable lookup. The explicit `{{ const:App\VERSION }}` syntax supports namespaced and mixed-case constant names.

## Changes in 0.7.0

### Automatic view data

Every generated View hook now calls `get_value()` and extracts the returned array with `EXTR_SKIP` before requiring the view. Values previously stored with `set_value()` therefore become normal variables inside every generated view.

### Named project storage

The File menu now provides **Save Project**, **Save Project As**, and **Load Saved Project**. Named projects are stored as JSON records under `storage/projects`, can be updated or deleted, and still support JSON import/export plus an optional browser recovery copy.

### Safe test overwrite

When a generated test plugin conflicts with a real plugin folder, the IDE displays a replacement confirmation. The user must explicitly approve deleting and replacing the existing folder. The IDE refuses to overwrite itself.

### Model and class method flows

Both Model methods and Custom Class methods can use either a typed PHP body or a nested execution graph. Choosing **Execution graph** enables **Save & Open Method Flow**, which creates the graph automatically and opens it immediately.

## Changes in 0.6.0

### Compact menu bar

The top toolbar is grouped into four dropdown menus:

- **File** — new, sample, save, import, and export project JSON
- **Edit** — undo, redo, visual groups, and presets
- **Nodes** — expanded library and node-definition reload
- **Build & Export** — validate, preview, test deployment, and ZIP build

Graph navigation and zoom controls remain directly visible.

### Typed model methods

The Model Method manager now provides a CRUD-style parameter editor. Every parameter has:

- required parameter name
- optional PHP data type

A method may also have an optional return type. These signatures are used when generating PHP and when creating typed input/output ports on Model Method Call nodes. Existing string-only parameter definitions remain supported.

### Custom classes

Three editable node packages implement custom PHP classes:

- **Class Definition** — architecture node that creates `classes/<ClassName>.php`
- **New Class Instance** — creates an object and exposes constructor parameters
- **Class Method Call** — calls static or instance methods with signature-derived ports

Use **Manage Methods** on a Class Definition. Each method supports:

- public, protected, or private visibility
- static or instance mode
- optional parameter types
- optional return type
- its own nested execution graph

A method named `__construct` defines the constructor flow and the inputs shown by New Class Instance.

### Canvas deselection

Clicking an empty area of the canvas now clears selected nodes and selected connections. Dragging from the same empty area still creates a selection box.

## Other editor capabilities

- Scrollable, line-numbered CodeMirror generated-code preview
- Fullscreen CodeMirror editor for View and other code properties
- Reusable graph presets with JSON import/export
- Expanded node library that remains open while adding several nodes
- Safe test deployment into the current ThunderPHP plugins directory
- Visual folders, multi-select, group movement, and nested Controller/Function flows

## Test Plugin safety

**Build & Export → Deploy Test Plugin** compiles the project into the current ThunderPHP plugins directory. It opens a selected GET Route node when one is selected, otherwise the first created GET route.

- Only folders containing `.tvi-test-build.json` are replaced.
- A real unmarked plugin with the same ID is never overwritten.
- PHP must have write access to the ThunderPHP plugins directory.

## Version 0.7 additions

- Every generated View hook calls `get_value()` and extracts the returned array with `EXTR_SKIP` before loading the view file.
- Named projects can be saved, loaded, updated, and deleted in `storage/projects`.
- Test deployment can replace an existing real plugin only after a replacement confirmation.
- Model and custom-class methods can use either typed PHP bodies or nested execution graphs.
- The Method Flow button automatically saves a valid method signature before opening its graph.


## Fixes in 0.10.1

- Suppresses the native browser context menu across every canvas descendant, including empty canvas, SVG connections, and nodes.
- Corrects generated-code preview line spacing by preventing fallback `<pre>` styles from affecting CodeMirror's internal line elements.


## Version 0.12 additions

- Database menu for running migrations, rolling back the current plugin, and viewing migration status.
- Structured Create Table column manager.
- Query Builder starter methods for newly created Model nodes, plus a Method Manager action to add missing defaults.
- Minimum and Maximum nodes with dynamic value inputs.
- Core Pager creation, value-method, limit/offset and display nodes.

## Bundled PHP libraries

Use the **Library Package** node when a generated plugin must carry a folder-based library such as mPDF.

- ZIP the library folder.
- Upload it from the node inspector.
- Choose the generated destination, for example `libraries/mpdf`.
- Optionally load an autoloader such as `vendor/autoload.php` in generated `plugin.php`.

Uploaded library ZIPs are stored in `storage/libraries/` to keep project JSON files small. Re-upload the ZIP after moving a project JSON to another ThunderPHP installation.

## Plugin dependencies

The Plugin node contains a structured dependency manager. Each dependency has a plugin ID, display name, version requirement, and required/optional flag. The manager writes the normal keyed `dependencies` object in generated `config.json`.


## Expanded managers and bundled presets

Create Table column lists and Validate rule lists can be expanded from the Inspector into a full-screen structured editor. Both views edit the same node data immediately.

Bundled node presets are discovered recursively from `presets/`. Copy any file exported with **Export Selected Nodes to File…** into that folder, optionally inside category subfolders, and reload the IDE. Bundled presets appear separately from browser-owned user presets and are safe to ship in tutorials.

The graph toolbar also includes a draggable four-direction joystick for continuous viewport panning.
