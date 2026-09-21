---
title: "Processing Multiple Thumbnails"
slug: "processing-multiple-thumbnails"
description: "Reference for smart_thumbnail and how to generate many versions in one call."
published: true
order: 8
source_id: 92
keywords: ["processing", "multiple", "thumbnails", "reference", "smart_thumbnail", "generate", "many", "versions", "one", "call", "classes", "image", "class", "method", "signature", "example", "returned", "structure", "typical", "pattern"]
---

The `smart_thumbnail()` method generates many thumbnails in one call and returns structured result information for each generated asset.

**Method Signature**

`smart_thumbnail(string $filename, array $specs = []): array`

Each spec can define:

- `width`
- `height`
- `mode`
- `ext`
- `bg_color`
- `position`
- `replace`
- `dest`

**Example**

```php
$image = new \Core\Image();

$image->set_thumbnail_dir(ROOTPATH . 'public/uploads/thumbs/');

$result = $image->smart_thumbnail(ROOTPATH . 'public/uploads/photo.jpg', [
	['width' => 100, 'height' => 100, 'mode' => 'crop', 'ext' => 'webp'],
	['width' => 300, 'height' => 300, 'mode' => 'crop', 'ext' => 'webp'],
	['width' => 800, 'height' => 450, 'mode' => 'crop', 'ext' => 'webp'],
	['width' => 1200, 'height' => 630, 'mode' => 'contain', 'ext' => 'jpg', 'bg_color' => [255,255,255]],
]);
```

**Returned Structure**

The method returns an array with:

- top-level `success`
- `items` containing one result array per thumbnail
- `error`

Each item contains:

- `success`
- `path`
- `mime`
- `width`
- `height`
- `size`
- `error`

**When To Use It**

This method is ideal when a single upload must produce multiple frontend versions, such as:

- avatar thumbnail
- card image
- medium preview
- social share image

**Typical Pattern**

```php
$image = new \Core\Image();

$versions = $image->smart_thumbnail($original, [
	['width' => 150, 'height' => 150, 'mode' => 'crop', 'ext' => 'webp'],
	['width' => 400, 'height' => 250, 'mode' => 'crop', 'ext' => 'webp'],
	['width' => 1200, 'height' => 630, 'mode' => 'crop', 'ext' => 'webp'],
]);
```
