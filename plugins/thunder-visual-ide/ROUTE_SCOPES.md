# Route Scopes

The **Route Scope** architecture node writes top-level URL prefixes into the generated plugin's `config.json`.

- Select `on` to populate `routes.on`.
- Select `off` to populate `routes.off`.
- Enter one path prefix per line or separate values with commas.
- `/page`, `page`, and `/page/reports` all normalize to the first segment `page`.
- Use two nodes to configure both lists.

When any `on` Route Scope node is present, the combined explicit values replace the compiler's normal route inference. Without an `on` node, existing automatic inference remains unchanged. All `off` nodes are merged and de-duplicated.

Example:

```text
Route Scope (on):  page, shop
Route Scope (off): admin, login
```

Generated configuration:

```json
"routes": {
  "on": ["page", "shop"],
  "off": ["admin", "login"],
  "routes": []
}
```
