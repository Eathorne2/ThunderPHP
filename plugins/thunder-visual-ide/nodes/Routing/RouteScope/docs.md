# Route Scope

Writes an explicit list to either `routes.on` or `routes.off` in the generated plugin `config.json`.

Enter route prefixes one per line or separated by commas. Leading and trailing slashes are optional. Only the first URL segment is stored, so `/page/reports` becomes `page` and applies to every URL beginning with `/page`.

Use two nodes when both lists are required:

- one node set to `on`, for example `page`
- one node set to `off`, for example `admin, login`

When at least one `on` Route Scope node exists, its combined values replace the IDE's automatically inferred `routes.on` list. `off` nodes are always combined into `routes.off`.
