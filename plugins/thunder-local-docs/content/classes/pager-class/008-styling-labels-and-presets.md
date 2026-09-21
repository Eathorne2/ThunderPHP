---
title: "Styling, Labels, and Presets"
slug: "styling-labels-and-presets"
description: "How to customize classes, text labels, and built-in style presets."
published: true
order: 8
source_id: 108
keywords: ["styling", "labels", "presets", "customize", "classes", "text", "built-in", "style", "pager", "class"]
---

The pager lets you customize both CSS classes and visible button text.

To customize class names, use `set_css()`:

```php
$pager = new \Core\Pager(10, 2, $total_rows);

$pager->set_css([
    'wrapper' => 'my-pager-wrap',
    'summary' => 'my-pager-summary',
    'ul' => 'my-pager-list',
    'li' => 'my-pager-item',
    'a' => 'my-pager-link',
    'active' => 'is-active',
    'disabled' => 'is-disabled',
]);
```

To customize labels, use `set_labels()`:

```php
$pager = new \Core\Pager(10, 2, $total_rows);

$pager->set_labels([
    'first' => '<<',
    'prev' => '<',
    'next' => '>',
    'last' => '>>',
    'summary' => 'Page {page} of {pages} · Showing {from}-{to} of {total}',
]);
```

The pager also supports built-in presets:

- `bootstrap`
- `bootstrap5`
- `minimal`

Example:

```php
$pager = new \Core\Pager(10, 2, $total_rows);
$pager->preset('bootstrap');
```

Presets are useful when you want a quick styling base without manually defining every CSS class.
