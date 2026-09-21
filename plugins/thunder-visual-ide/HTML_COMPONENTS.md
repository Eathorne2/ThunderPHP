# HTML Components and Snippets

Thunder Visual IDE HTML components are reusable View partials containing HTML, CSS and JavaScript.

## Creating a component

Open a View Builder and choose **Nodes → HTML Component Designer…**. The IDE creates a new `HTML Design Component` node when one is not already selected.

New components begin with all three code editors empty. Use **Browse Snippets…** to insert a starting design or begin from a clean slate. **Clear All** removes the current HTML, CSS and JavaScript after confirmation.

Each code panel and the Preview panel can be expanded to the full studio workspace. The Preview header also provides Fluid, Desktop, Tablet and Mobile width controls for responsive testing. JavaScript starts minimised and can be opened only when needed.

The designer contains:

1. **HTML** — ordinary markup plus the compact View template syntax.
2. **CSS** — selectors are scoped automatically during compilation.
3. **JavaScript** — code receives the component wrapper as `root`.
4. **Preview** — a sandboxed iframe updated while you edit.

Insert the resulting component into its View with:

```html
<tvi-component name="feature-card" />
```

The normal View inspector provides this marker button automatically.

## Nesting components

The same marker can be placed inside an HTML Design Component:

```html
<section class="tabs__panel">
    <tvi-component name="looped-cards" />
</section>
```

The compiler converts the marker to a nested View partial include. Components may reference other components recursively, including visual Components containing Loops, Forms, Pagination components, and other HTML Design Components.

The HTML Component Designer includes an **Insert component** selector. It inserts the selected marker at the current HTML CodeMirror cursor or replaces the current selection. If no editor cursor is available, the marker is appended to the HTML.

Direct and indirect circular references are rejected during compilation. For example, `tabs → cards → tabs` produces a clear compiler error rather than an infinite include loop. Missing component names become HTML comments and compiler warnings.

Nested component CSS is collected into a generated View stylesheet, while executable JavaScript remains guarded so each component root is initialised once. Component HTML remains repeatable, including when the containing component is rendered inside a Loop.

## CSS isolation

A component named `feature-card` receives a wrapper similar to:

```html
<div class="tvi-html-feature-card" data-tvi-html-component="feature-card">
```

CSS such as:

```css
.card { padding: 1rem; }
.card h3 { margin: 0; }
```

is compiled as:

```css
.tvi-html-feature-card .card { padding: 1rem; }
.tvi-html-feature-card .card h3 { margin: 0; }
```

Nested `@media`, `@supports`, `@container`, and `@layer` rules are scoped recursively. Keyframe names and animation references are renamed with the component scope.

You may explicitly refer to the wrapper using `:scope` or `{{scope}}` in CSS.

During compilation, all HTML Design Component CSS used by a View is written to a generated file such as:

```text
looks/main/assets/css/thunder-html-components-page-view-id.css
```

The View loads that file immediately before its first visual component. A request-level URL guard prevents duplicate stylesheet links when the same View or Reusable View is rendered more than once. This keeps component CSS cacheable while preserving automatic selector scoping.

## JavaScript isolation

JavaScript is wrapped automatically:

```js
(function (root) {
    // Your code
})(root);
```

Prefer:

```js
const button = root.querySelector('[data-action]');
```

instead of querying the entire document. Each matching component root is initialised once.

## Component export

The designer can export one component as a `.tvi-component.json` file. Importing this file loads its title, component name, HTML, CSS and JavaScript into the current designer. Click **Save Component** to apply it to the selected node.

The existing preset system can save the component node together with connected View children.

## Adding a snippet

Create a folder under `html-snippets/`:

```text
html-snippets/my-card/
├── snippet.json
├── snippet.html
├── style.css
├── script.js
└── preview.svg
```

Example `snippet.json`:

```json
{
  "id": "my-card",
  "name": "My Card",
  "description": "A reusable content card.",
  "category": "Cards",
  "order": 50,
  "tags": ["card", "content"],
  "html": "snippet.html",
  "css": "style.css",
  "js": "script.js",
  "preview": "preview.svg"
}
```

The Snippet Library uses one central sandboxed iframe. Selecting a snippet renders its real HTML, CSS and JavaScript in that iframe; static preview images remain optional metadata and are no longer required by the browser UI.

After adding or changing a snippet, reload the IDE. Snippet CSS does not need to be manually prefixed; the generated component compiler applies isolation.

A snippet is a starting source package for one HTML Design Component node. The IDE does not scan form controls in the fragment and create Text Input, Select, Checkbox, or other graph nodes. Those controls remain ordinary HTML. Use View Builder form/input nodes when you need per-field graph connections, validation configuration, or theme-template rendering.

## Look theme colors in snippets

HTML snippets inherit the same named color variables as the connected Look's shared form-input theme. Prefer semantic variables instead of fixed colors:

```css
.card {
    color: var(--thv-theme-text, #0f172a);
    background: var(--thv-theme-surface, #ffffff);
    border-color: var(--thv-theme-border, #cbd5e1);
}
.card__button {
    color: var(--thv-theme-on-primary, #ffffff);
    background: var(--thv-theme-primary, #2563eb);
}
```

Available bundled tokens include:

- `primary`
- `secondary`
- `surface`
- `surface-alt`
- `text`
- `muted`
- `border`
- `danger`
- `on-primary`
- `success`
- `warning`
- `info`

The Snippet Library includes the same palette selector, color inputs, import, export, and reset controls used by the form-theme browser. Changes update the central iframe immediately. **Apply Colors to Look** stores the palette on the Look so every connected View, reusable partial, form control, and HTML component uses the same values.


## Snippet library organisation

The designer loads snippets from `html-snippets/<snippet>/`. The `category` value in each `snippet.json` determines the folder tab shown in the snippet modal. Search ignores the selected tab and searches every folder. Results are paginated automatically.

## Bundled snippet categories

The bundled library currently includes 114 snippets across Cards, Navigation, Sliders, Tables, Authentication, Profiles, User Cards, Galleries, Contact Forms, Marketing, Commerce, Dashboard, Content, Feedback, Header and Footer Sets, Hero Sections, Call to Action, Sticky Sidebars, Video Players, Audio Players, Testimonial Sliders, Socials, Grid Layouts, and Icon Grids. Each folder is independent, so snippets can be removed, replaced, or copied without changing the IDE registry.


## Bundled snippet library

The library currently contains 114 snippets. Version 0.12.68 adds three coordinated options each for Header and Footer Sets, Profiles, User Cards, Hero Sections, Call to Action, Sticky Sidebars, Video Players, Audio Players, Testimonial Sliders, Socials, Grid Layouts, and Icon Grids.

## Creating custom and marketplace snippets

Open **Marketplace → Asset Studio… → HTML Snippets** to paste or author reusable HTML, CSS and JavaScript as a distributable package. The studio provides package metadata, a sandboxed preview and portability checks.

Bundled snippets are read-only. **Create Custom Copy** creates a separately identified custom package. Custom snippets live under `storage/marketplace/html-snippets/` and can be edited, deleted, imported or exported as ZIP files.

The studio requires fragment HTML and rejects complete documents or embedded `<style>` and `<script>` tags. JavaScript should query from the supplied `root` wrapper. See `MARKETPLACE_ASSETS.md` for package structure and marketplace publishing rules.
## Bundled UI snippet expansion (0.12.51)

The built-in library now includes three independent presets for each of these groups: modal alerts, inline alerts, progress bars, user-list cards, search bars, footers, menu headers, tables, authentication panels and tab navigation. Each package keeps HTML, CSS and JavaScript in separate files. JavaScript uses the component `root`, and snippet-specific class prefixes allow multiple instances to appear on one page.


## Hook components in Views

The View Builder includes **Run Action Hook** and **Run Filter Hook** nodes.

- Use **Run Action Hook** where callbacks registered with `add_action()` should output HTML.
- Use **Run Filter Hook** where callbacks registered with `add_filter()` should transform and return a value for display.
- Connect a View-compatible expression to the Data port to pass page context into the hook.
- Leave the component name blank for normal visual placement.
- To place a hook inside hand-written HTML, keep it as a root node, assign a reusable component name, and add `<tvi-component name="your-hook" />` to the View source.

Filter output is raw by default because View filters commonly return HTML. Enable **Escape filtered output** when the result should be treated as plain text.


## HTML Boilerplate document node

The **HTML Boilerplate** node is a root View Builder component that owns the complete HTML5 document. Its source is fully editable and includes social sharing metadata by default.

Use `<tvi-content />` to choose where the owning View source and connected Page content nodes are rendered. The alias `<tvi-children />` is also accepted. Only one document boilerplate should be used in a View graph.

Because the compiler understands the document boundary, the generated component stylesheet link is inserted at the first actual component location inside the document rather than above the doctype.


## HTML snippet expansion (0.12.68)

The built-in library adds 36 independent snippet packages, with three options in each of these groups:

- Header and Footer Sets
- Profiles
- User Cards
- Hero Sections
- Call to Action
- Sticky Sidebars
- Video Players
- Audio Players
- Testimonial Sliders
- Socials
- Grid Layouts
- Icon Grids

All new packages use readable, non-minified HTML, CSS, and vanilla JavaScript. Coordinated header/footer packages contain both regions in one snippet so their spacing, typography, navigation, and color treatment remain visually matched. Media players use native HTML media elements with editable custom controls, and interactive components scope queries through the provided component `root`.

## Do Action Component (0.12.95)

Use **Do Action Component** when an HTML Design Component needs a ThunderPHP extension point without typing PHP into the HTML editor.

1. Add the node as a root in the same View Builder graph.
2. Enter a hook name and the data variable to pass. The default variable is `$data`.
3. Give the node a component name.
4. In the HTML Component Designer, choose it from **Insert component** and insert the generated marker.

The compiler generates a positioned action call similar to:

```php
<?php do_action('custom_hook', $data ?? []); ?>
```

Custom hook names declared by Do Action Component and Run Action Hook nodes are added to the editable action-hook dropdowns on Controller Hook and View nodes. Filter dropdowns remain limited to filter hook names.
