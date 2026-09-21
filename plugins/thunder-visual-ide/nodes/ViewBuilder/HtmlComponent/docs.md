# HTML Design Component

Creates a reusable View partial from HTML, CSS and JavaScript edited in the fullscreen HTML Component Designer.

- CSS selectors are automatically prefixed with the component's generated scope class and written to a generated cacheable stylesheet.
- Keyframe names are also renamed to reduce collisions.
- JavaScript runs once for every matching component root and receives that element as the `root` variable.
- View template expressions such as `{{ user.name }}` remain supported in the HTML section.
- Insert the component into a View with `<tvi-component name="component-name" />`.
- Insert another component marker directly into this component's HTML to build nested component trees.
- The designer's component selector inserts the marker at the current HTML cursor position.
- Missing component references produce warnings, while direct and indirect circular references are blocked during compilation.
- Component CSS is collected into the owning View or Reusable View stylesheet and JavaScript is guarded; repeated HTML does not duplicate its assets.

File-based snippets are loaded from `html-snippets/<snippet>/`.

The component wrapper receives the connected Look's form-theme scope class. Snippet and custom CSS can therefore use semantic variables such as `var(--thv-theme-primary)`, `var(--thv-theme-surface)`, and `var(--thv-theme-text)`. Moving or copying the component to a View under another Look automatically adopts that Look's palette.
