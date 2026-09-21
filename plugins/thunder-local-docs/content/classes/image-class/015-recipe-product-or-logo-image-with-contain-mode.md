---
title: "Recipe: Product or Logo Image With Contain Mode"
slug: "recipe-product-or-logo-image-with-contain-mode"
description: "A common workflow when the whole image must stay visible inside a fixed box."
published: true
order: 15
source_id: 99
keywords: ["recipe", "product", "logo", "image", "contain", "mode", "common", "workflow", "whole", "must", "stay", "visible", "inside", "fixed", "box", "classes", "class", "case", "example", "transparent", "why", "useful"]
---

This recipe is useful for logos, packaging images, and product images where no part of the source should be cropped.

**Use Case**

You want a `500x500` image box for consistent layout, but each source image has a different shape.

**Example**

```php
use \Core\Image;

$image = new Image();

$image->set_jpeg_quality(85)
      ->set_png_compression(7)
      ->allow_upscale(false);

$result = $image->process(ROOTPATH . 'public/uploads/logo.png', 'contain', [
	'width' => 500,
	'height' => 500,
	'bg_color' => [255,255,255],
	'dest' => ROOTPATH . 'public/uploads/logo_box.jpg',
]);

if(!$result['success'])
{
	die($result['error']);
}
```

**Transparent Box Example**

```php
use \Core\Image;

$image = new Image();

$result = $image->process(ROOTPATH . 'public/uploads/logo.png', 'contain', [
	'width' => 500,
	'height' => 500,
	'bg_color' => [0,0,0,127],
	'dest' => ROOTPATH . 'public/uploads/logo_box.webp',
]);
```

**Why This Is Useful**

Contain mode keeps the entire image visible and still gives you consistent outer dimensions for the layout.
