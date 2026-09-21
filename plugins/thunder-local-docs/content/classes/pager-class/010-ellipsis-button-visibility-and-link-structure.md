---
title: "Ellipsis, Button Visibility, and Link Structure"
slug: "ellipsis-button-visibility-and-link-structure"
description: "How the pager controls first, last, previous, next, and ellipsis behavior."
published: true
order: 10
source_id: 110
keywords: ["ellipsis", "button", "visibility", "link", "structure", "pager", "controls", "first", "last", "previous", "next", "behavior", "classes", "class"]
---

The pager can shorten long page lists with ellipsis.

Example:

`First Previous 1 ... 8 9 10 ... 40 Next Last`

This helps keep the UI compact when there are many pages.

You can control that behavior with:

- `use_ellipsis()`
- `show_first_last()`
- `show_prev_next()`

Example:

```php
$pager = new \Core\Pager(10, 2, 900);

$pager->use_ellipsis(true)
      ->show_first_last(true)
      ->show_prev_next(true);
```

To disable ellipsis and only show the calculated numeric window:

```php
$pager = new \Core\Pager(10, 2, 900);
$pager->use_ellipsis(false);
```

To hide the first and last buttons:

```php
$pager = new \Core\Pager(10, 2, 900);
$pager->show_first_last(false);
```

To hide previous and next:

```php
$pager = new \Core\Pager(10, 2, 900);
$pager->show_prev_next(false);
```

These switches make it easier to fit the pager into different UI styles without rewriting the class.
