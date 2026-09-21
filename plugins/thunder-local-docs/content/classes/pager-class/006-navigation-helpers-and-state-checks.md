---
title: "Navigation Helpers and State Checks"
slug: "navigation-helpers-and-state-checks"
description: "Reference for current page, previous page, next page, and page state methods."
published: true
order: 6
source_id: 106
keywords: ["navigation", "helpers", "state", "checks", "reference", "current", "page", "previous", "next", "methods", "classes", "pager", "class"]
---

The pager includes helper methods that let you inspect pagination state without manually calculating anything.

Common helpers:

- `current_page()`
- `prev_page()`
- `next_page()`
- `has_prev()`
- `has_next()`

Example:

```php
$pager = new \Core\Pager(10, 2, 200);

echo $pager->current_page();
echo $pager->prev_page();
echo $pager->next_page();
```

You can also use state checks to conditionally show custom UI:

```php
$pager = new \Core\Pager(10, 2, 200);

if($pager->has_prev()){
    echo "There is a previous page";
}

if($pager->has_next()){
    echo "There is a next page";
}
```

These methods are especially helpful if you are not using the built-in `display()` method and prefer to build your own layout.
