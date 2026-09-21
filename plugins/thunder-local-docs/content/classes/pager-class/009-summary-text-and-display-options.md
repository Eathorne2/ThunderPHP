---
title: "Summary Text and Display Options"
slug: "summary-text-and-display-options"
description: "How to show record ranges, control summary output, and handle single-page datasets."
published: true
order: 9
source_id: 109
keywords: ["summary", "text", "display", "options", "show", "record", "ranges", "control", "output", "handle", "single-page", "datasets", "classes", "pager", "class"]
---

The pager can show a summary line describing the visible range of records.

Example output:

`Showing 21-30 of 145`

To get the summary text directly:

```php
$pager = new \Core\Pager(10, 2, 145);

echo $pager->summary_text();
```

Related methods include:

- `showing_from()`
- `showing_to()`
- `summary_text()`
- `show_summary()`
- `is_summary_enabled()`

Example:

```php
$pager = new \Core\Pager(10, 2, 145);

echo $pager->showing_from();
echo $pager->showing_to();
echo $pager->summary_text();
```

To hide the summary:

```php
$pager = new \Core\Pager(10, 2, 145);
$pager->show_summary(false);
```

To force the pager to render even when there is only one page:

```php
$pager = new \Core\Pager(10, 2, 8);
$pager->render_if_single_page(true);
```

This is useful if you want a consistent footer layout even for small datasets.
