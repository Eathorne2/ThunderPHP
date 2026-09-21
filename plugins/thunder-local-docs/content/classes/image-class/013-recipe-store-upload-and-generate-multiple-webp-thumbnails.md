---
title: "Recipe: Store Upload and Generate Multiple WEBP Thumbnails"
slug: "recipe-store-upload-and-generate-multiple-webp-thumbnails"
description: "A practical workflow for upload storage and multiple generated sizes."
published: true
order: 13
source_id: 97
keywords: ["recipe", "store", "upload", "generate", "multiple", "webp", "thumbnails", "practical", "workflow", "storage", "generated", "sizes", "classes", "image", "class", "case", "example", "why", "works", "well"]
---

This recipe stores an uploaded image, keeps the original, and generates several `webp` thumbnails for frontend use.

**Use Case**

A blog post cover upload where you need:

- original file
- small card image
- medium card image
- social share image

**Example**

```php
use \Core\Image;

$image = new Image();

$image->set_thumbnail_dir(ROOTPATH . 'public/uploads/posts/thumbs/')
      ->set_jpeg_quality(85)
      ->set_webp_quality(80)
      ->set_png_compression(7)
      ->allow_upscale(false);

$stored = $image->from_upload(
	$_FILES['cover'],
	ROOTPATH . 'public/uploads/posts/originals/post_123.jpg'
);

if(!$stored['success'])
{
	die($stored['error']);
}

$thumbs = $image->smart_thumbnail($stored['path'], [
	['width' => 300, 'height' => 200, 'mode' => 'crop', 'ext' => 'webp'],
	['width' => 600, 'height' => 400, 'mode' => 'crop', 'ext' => 'webp'],
	['width' => 1200, 'height' => 630, 'mode' => 'crop', 'ext' => 'webp'],
]);

if(!$thumbs['success'])
{
	die($thumbs['error']);
}
```

**Why This Works Well**

- the original is preserved
- thumbnails are optimized for the frontend
- `webp` helps reduce bandwidth
- all generated files can be tracked from the returned result arrays
