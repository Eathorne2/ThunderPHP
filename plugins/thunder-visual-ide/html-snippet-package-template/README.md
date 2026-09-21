# HTML snippet package template

This folder matches the ZIP format used by **Marketplace → Asset Studio…**.

- Keep `snippet.html` as a reusable fragment; do not include `html`, `head`, `body`, `style` or `script` tags.
- Put CSS and JavaScript in their dedicated files.
- The component compiler scopes ordinary CSS selectors.
- JavaScript receives the component wrapper as `root`; prefer `root.querySelector(...)`.
- Keep the package ID stable when publishing updates.

ZIP the files with `snippet.json` at the package root, or import them into the Asset Studio and export the validated package there.
