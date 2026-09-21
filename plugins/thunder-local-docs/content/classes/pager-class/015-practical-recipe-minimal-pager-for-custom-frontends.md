---
title: "Practical Recipe: Minimal Pager for Custom Frontends"
slug: "practical-recipe-minimal-pager-for-custom-frontends"
description: "Recipe for using custom labels, summary text, and a simplified style preset."
published: true
order: 15
source_id: 115
keywords: ["practical", "recipe", "minimal", "pager", "custom", "frontends", "labels", "summary", "text", "simplified", "style", "preset", "classes", "class"]
---

If you want a lighter-looking interface, combine a preset with custom labels.

```php
$pager = new \Core\Pager(12, 1, 145);

$pager->preset('minimal')
      ->set_labels([
          'first' => '<<',
          'prev' => '<',
          'next' => '>',
          'last' => '>>',
          'summary' => 'Showing {from}-{to} / {total}',
      ]);

$pager->display();
```

This works well for dashboards, compact cards, media listings, and frontend widgets where full Bootstrap markup is not desired.

You can go further and hide some controls:

```php
$pager = new \Core\Pager(12, 1, 145);

$pager->preset('minimal')
      ->show_first_last(false)
      ->show_prev_next(true)
      ->use_ellipsis(true);

$pager->display();
```

This gives you a much smaller pager while still keeping navigation clear.
