---
title: "Hook Priority Explained"
slug: "hook-priority-explained"
description: "How execution order works and why it matters."
published: true
order: 4
source_id: 273
keywords: ["hook", "priority", "explained", "execution", "order", "works", "why", "matters", "getting", "started", "routing", "hooks"]
---

Hooks use priority numbers:

```php
add_action('view', fn() => ..., 5);
add_action('view', fn() => ..., 10);
```

Lower number runs first.

Execution order:

- 5 runs before 10
- 10 runs before 20

Use cases:

- layout first (low number)
- content later (default 10)
- overrides last (higher number)

This lets you control rendering layers precisely.
