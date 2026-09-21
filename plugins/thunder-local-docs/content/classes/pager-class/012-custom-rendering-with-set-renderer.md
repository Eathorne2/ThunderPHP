---
title: "Custom Rendering with set_renderer"
slug: "custom-rendering-with-set-renderer"
description: "How to replace the built-in HTML with completely custom markup."
published: true
order: 12
source_id: 112
keywords: ["custom", "rendering", "set_renderer", "replace", "built-in", "html", "completely", "markup", "classes", "pager", "class"]
---

One of the most useful features of the pager is the ability to replace the default HTML output with your own markup.

Use `set_renderer()` to provide a callback. The callback receives:

- the pagination items array
- the pager instance

Example:

```php
$pager = new \Core\Pager(10, 2, $total_rows);

$pager->set_renderer(function(array $items, \Core\Pager $pager){

    $html = '<div class="my-pager-wrap">';

    if($pager->is_summary_enabled()){
        $html .= '<div class="my-pager-summary">' . $pager->summary_text() . '</div>';
    }

    $html .= '<div class="my-pager">';

    foreach($items as $item){

        if($item['type'] === 'ellipsis'){
            $html .= '<span class="my-pager-ellipsis">' . $item['label'] . '</span>';
            continue;
        }

        $active = $item['active'] ? ' is-active' : '';
        $disabled = $item['disabled'] ? ' is-disabled' : '';

        if($item['disabled']){
            $html .= '<span class="my-pager-btn' . $active . $disabled . '">' . $item['label'] . '</span>';
        }else{
            $html .= '<a class="my-pager-btn' . $active . '" href="' . $item['url'] . '">' . $item['label'] . '</a>';
        }
    }

    $html .= '</div>';
    $html .= '</div>';

    return $html;
});

$pager->display();
```

This is the best option when changing button HTML structure is more important than simply changing CSS class names.
