---
title: "Contain Fit Inside a Box"
slug: "contain-fit-inside-a-box"
description: "Reference for fitting an image inside a fixed box without cropping."
published: true
order: 5
source_id: 89
keywords: ["contain", "fit", "inside", "box", "reference", "fitting", "image", "fixed", "without", "cropping", "classes", "class", "method", "signature", "parameters", "basic", "example", "custom", "background", "color"]
---

The `fit_contain()` method resizes an image so that the whole image fits inside a fixed rectangle without being cropped. Any extra space is filled with a background color.

**Method Signature**

`fit_contain(string $filename, int $max_width = 700, int $max_height = 700, ?string $dest = null, array $bg_color = [255,255,255]): string`

**Parameters**

- `filename`: source image path
- `max_width`: outer box width
- `max_height`: outer box height
- `dest`: optional destination file
- `bg_color`: background fill color

**Basic Example**

```php
$image = new \Core\Image();

$image->fit_contain(
	ROOTPATH . 'public/uploads/logo.png',
	400,
	300,
	ROOTPATH . 'public/uploads/logo_preview.png'
);
```

**Custom Background Color**

For JPEG output, a white background is common.

```php
$image = new \Core\Image();

$image->fit_contain(
	ROOTPATH . 'public/uploads/product.png',
	500,
	500,
	ROOTPATH . 'public/uploads/product_box.jpg',
	[255,255,255]
);
```

For transparent-capable output like `png` or `webp`, you can provide alpha values when appropriate.

```php
$image = new \Core\Image();

$image->fit_contain(
	ROOTPATH . 'public/uploads/logo.png',
	400,
	300,
	ROOTPATH . 'public/uploads/logo_box.webp',
	[0,0,0,127]
);
```

**When To Use It**

Use `fit_contain()` when cropping would remove important parts of the image. This is especially useful for:

- logos
- product images
- admin previews
- document-style thumbnails
