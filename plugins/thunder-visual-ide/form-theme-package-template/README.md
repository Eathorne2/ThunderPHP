# Form theme package template

This folder matches the ZIP format used by **Marketplace → Asset Studio…**.

1. Change the permanent package ID, metadata, palettes and component mappings in `theme.json`.
2. Paste a representative complete form into `preview.html`.
3. Split reusable field structures into the files under `components/`.
4. Put isolated theme rules in `style.css`; every rule must use `{{scope}}` as its root.
5. Put optional behavior in `script.js` and keep selectors scoped to the generated theme/View.
6. ZIP the files with `theme.json` at the package root, or import the folder into the Asset Studio and export it there.

The compiler supplies `{{wrapper_attributes}}`, `{{label_html}}`, `{{control_html}}`, `{{error_html}}` and `{{children_html}}` to component templates.

The Asset Studio validates the package and stores imported packages under `storage/marketplace/form-themes/`. Built-in themes are never replaced.
