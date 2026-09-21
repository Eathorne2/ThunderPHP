# Form input themes

Each folder is a View-level form theme discovered automatically from `theme.json`.

Required files:

```text
form-themes/my-theme/
├── theme.json
├── preview.html
├── style.css
├── script.js
└── components/
    ├── field.html.tpl
    ├── textarea.html.tpl
    ├── select.html.tpl
    ├── checkbox.html.tpl
    ├── button.html.tpl
    └── form.html.tpl
```

`theme.json` maps View Builder component types to template files. Templates may use:

- `{{wrapper_attributes}}`
- `{{label_html}}`
- `{{control_html}}`
- `{{children_html}}`

Theme CSS must use `{{scope}}` as its root selector. Named palette colors are exposed as CSS variables such as `--thv-theme-primary`, `--thv-theme-surface`, and `--thv-theme-text`.

Copy an existing folder, change its ID and name, then reload the IDE. No central JavaScript registry edit is required.


Input component templates may use `{{error_html}}` to position the field's conditional validation messages.
