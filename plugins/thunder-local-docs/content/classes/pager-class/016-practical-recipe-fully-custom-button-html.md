---
title: "Practical Recipe: Fully Custom Button HTML"
slug: "practical-recipe-fully-custom-button-html"
description: "Recipe for replacing the default pagination markup with your own HTML structure."
published: true
order: 16
source_id: 116
keywords: ["practical", "recipe", "fully", "custom", "button", "html", "replacing", "default", "pagination", "markup", "own", "structure", "classes", "pager", "class"]
---

This recipe is useful when your design system does not use the default `ul > li > a` structure.

```php
$pager = new \Core\Pager(15, 2, 320);

$pager->set_renderer(function(array $items, \Core\Pager $pager){

    $html = '<div class="tp-pagination">';

    if($pager->is_summary_enabled()){
        $html .= '<div class="tp-pagination-summary">' . $pager->summary_text() . '</div>';
    }

    $html .= '<div class="tp-pagination-buttons">';

    foreach($items as $item){

        if($item['type'] === 'ellipsis'){
            $html .= '<span class="tp-pagination-gap">' . $item['label'] . '</span>';
            continue;
        }

        $class = 'tp-pagination-btn';

        if($item['active']){
            $class .= ' is-active';
        }

        if($item['disabled']){
            $class .= ' is-disabled';
            $html .= '<span class="' . $class . '">' . $item['label'] . '</span>';
        }else{
            $html .= '<a class="' . $class . '" href="' . $item['url'] . '">' . $item['label'] . '</a>';
        }
    }

    $html .= '</div>';
    $html .= '</div>';

    return $html;
});

$pager->display();
```

This approach gives you full control over the markup while keeping all pagination logic inside the pager class.
