---
title: "Rendering Pagination Output"
slug: "rendering-pagination-output"
description: "How to output the default HTML pager and when render or display should be used."
published: true
order: 7
source_id: 107
keywords: ["rendering", "pagination", "output", "default", "html", "pager", "render", "display", "classes", "class"]
---

The pager provides two main output methods:

- `display()`: directly echoes the HTML
- `render()`: returns the HTML as a string

Standard usage:

```php
$pager = new \Core\Pager(10, 2, $total_rows);
$pager->display();
```

If you want the HTML first, use `render()`:

```php
$pager = new \Core\Pager(10, 2, $total_rows);

$html = $pager->render();

echo '<div class="table-footer">';
echo $html;
echo '</div>';
```

The class only renders when there is more than one page, unless you explicitly allow rendering for a single-page dataset.

You can check this with:

```php
if($pager->should_render()){
    $pager->display();
}
```

This helps keep the interface clean when a list is too small to need pagination.
