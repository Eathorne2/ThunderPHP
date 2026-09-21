---
title: "Pagination in Plugin UIs"
slug: "pagination-in-plugin-uis"
description: "Current Pager class behavior, constructor options, rendering methods, and custom markup strategy."
published: true
order: 13
source_id: 236
keywords: ["pagination", "plugin", "uis", "current", "pager", "class", "behavior", "constructor", "options", "rendering", "methods", "custom", "markup", "strategy", "reference", "refrence", "thunderphp", "developers", "output"]
---

The current `\Core\Pager` class is a full pagination utility, not just an offset helper.

It handles:

- current page detection
- offset and limit calculation
- previous and next state
- HTML output
- custom render control

**Constructor**

```php
$pager = new \Core\Pager($limit, $extras, $total_rows);
```

- `limit`: records per page
- `extras`: page numbers around the current page
- `total_rows`: total result count

The current page is read from the `page` query parameter and sanitized so missing or invalid page values fall back safely to page `1`.

**Output Methods**

- `display()` echoes the pager HTML
- `render()` returns the HTML string

Plugin authors who want custom markup can build their own layout while still using pager methods like `has_prev()` and `has_next()`.
