# Look form theme packages

Form themes belong to a **Look node**, not individual View, Reusable View, or form-control nodes. Every View connected to the same Look therefore uses one component template set and one color palette.

Select a Look node in the architecture graph and choose **Choose Form Theme…**. The live iframe preview shows a complete form and updates immediately when a palette or named color changes.

During compilation, each Look receives a generated PHP theme file. The default path is `frontend/theme-colors.php`. A configurable action hook includes that file from the active Look once per page load; by default the hook is `before_view` at priority `100`.

The Look inspector also provides these controls:

- **Load form theme CSS** — enables or disables the generated loader hook.
- **Theme CSS file path** — changes the generated file path inside the Look.
- **Theme hook name** — defaults to `before_view`.
- **Theme hook priority** — defaults to `100`.

Theme folders live under `form-themes/`. Start by copying one of the bundled themes. The theme's component templates control the HTML arrangement, `style.css` controls the isolated design, and `script.js` can add optional behavior. Palette names and colors are defined in `theme.json`.

## Inline error token

Input templates can place `{{error_html}}` wherever field-level validation messages should appear. The bundled themes place it inside the field wrapper after the control area. Custom themes should include this token in `field.html.tpl`, `textarea.html.tpl`, `select.html.tpl`, and `checkbox.html.tpl`.

## Shared HTML component palette

The Look palette is available to HTML Design Components and folder-based HTML snippets. Generated component wrappers receive the connected Look's form-theme scope class, so CSS variables such as `--thv-theme-primary` and `--thv-theme-surface` inherit automatically.

In addition to the original form tokens, bundled palettes define `on_primary`, `success`, `warning`, and `info` for general-purpose interface components. Custom themes should provide these values when possible; snippet CSS includes fallbacks when they are absent.

## Bundled themes

Thunder Visual IDE currently ships with ten themes:

- Classic Clean
- Floating Labels
- Minimal Underline
- Soft Panel
- Accent Edge
- Glass Frost
- Compact Admin
- Neon Night
- Side Labels
- Capsule Forms

## Creating custom and marketplace themes

Open **Marketplace → Asset Studio… → Form Themes** to create themes without modifying the bundled `form-themes/` directory. The studio provides metadata fields, palette JSON, preview HTML, CSS, optional JavaScript, all required component templates, a sandboxed live preview and validation.

Bundled themes are read-only. **Create Custom Copy** copies their source into a new custom package. Custom themes are stored under `storage/marketplace/form-themes/`, loaded by the normal theme registry and can be edited, deleted, imported or exported as ZIP packages.

Theme CSS must use `{{scope}}`, and the studio enforces the required component-template tokens. See `MARKETPLACE_ASSETS.md` for the complete package contract and publishing guidance.

## Theme previews and graph fields

The Asset Studio preview form is representative HTML for designing and demonstrating a package. It does not create or infer graph nodes. Real fields are created with the View Builder's Form and Input nodes; the connected Look's component templates wrap the HTML those nodes generate.

The visual palette manager edits one named palette at a time and applies the selected palette to the authoring preview immediately. All palettes are stored in the package and remain selectable after installation.

## Shared reusable color presets

The Look Form Input Theme selector also exposes the reusable color presets used by the HTML Snippet Browser. A reusable preset changes the semantic Look colors without changing the selected form design. The selected preset identity and resolved color map are stored on the Look node, so the same palette is shown when either editor is reopened.
