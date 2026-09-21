# Pagination Component

Creates a reusable visual pagination partial backed by `\Core\Pager::set_renderer()`.

1. Add a Pager object to View data, normally as `pager` using `set_value()`.
2. Add this node in the View Builder or choose a design from **Nodes → Pagination Designs**.
3. Insert its `<tvi-component>` marker into the View source.
4. Edit labels, classes, colors and CSS in the inspector. The preview updates immediately.

## Color palette

The pagination node provides three color modes:

- **Design defaults** uses the palette supplied by the selected pagination template.
- **Custom colors** lets the View choose its own button, active, disabled, hover and summary colors.
- **Inherit from Look theme** maps each pagination color role to one named Look color. For example, the active background can use `primary`, while active text uses `on_primary` and borders use `border`.

Theme inheritance supports `primary`, `secondary`, `surface`, `surface_alt`, `text`, `muted`, `border`, `on_primary`, `danger`, `success`, `warning`, and `info`.

Designs are file based under `pagination-templates/<design-id>/`:

- `template.json` — metadata, default settings and default color palette
- `renderer.php.tpl` — generated PHP renderer
- `style.css` — optional base styles with class and color tokens
- `preview.html` — static preview markup

Copy an existing folder to create another design, change its `id`, then reload the IDE.

## Reusing custom colors

When **Use custom colors** is selected, the inspector can export the ten pagination color roles to JSON and import them into another Pagination Component. Opening **Browse Designs** from a Pagination Component previews every installed design with that node's current custom or inherited palette, so the design can be judged before it is added.
