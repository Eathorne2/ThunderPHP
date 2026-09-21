# View

Creates a View file and registers an `add_action()` callback.

- Connect it to a Route to scope the callback to that named route. The Hook name field is an editable dropdown: choose a standard ThunderPHP action hook or type a custom name.
- Leave it unconnected to register the selected hook globally without a route argument.
- Connect it to a Look to choose its output folder and shared form theme.

This is useful for shared UI hooks triggered by another plugin or for a View hook intended to run wherever the plugin is loaded.

## Form input theme

Form theme selection is configured on the connected Look node. Every built-in visual Form, Text, Email, Password, Number, Date and File input, Textarea, Select, Checkbox and Button in this View uses that Look's theme and palette.

Because the theme is loaded by the Look's shared hook, rendering multiple Views on one page does not add competing per-View theme styles.

See `FORM_THEMES.md` and `nodes/Looks/Look/docs.md` for the theme package and compiler behavior.

## Component CSS placement

The shared View Builder stylesheet is generated at `assets/css/thunder-view-components.css`. Its `<link>` tag is inserted immediately before the first visual component placement in the View instead of being prepended before the View source. A request-scoped guard, keyed by the resolved stylesheet URL, prevents duplicate links when multiple Views or route-less Views render during the same page load.

CSS from HTML Design Components is automatically scoped and collected into one generated stylesheet for the owning View or Reusable View. Its guarded `<link>` is placed immediately before the first visual component, so repeated rendering does not repeat the stylesheet link. Pagination designs continue to manage their own required component styling.

## JavaScript Flow

Use **Open JavaScript Flow** in the Inspector to create browser-side event, AJAX, DOM and value logic for this View. The JavaScript graph compiles to a separate Look asset and is loaded with `defer`.
