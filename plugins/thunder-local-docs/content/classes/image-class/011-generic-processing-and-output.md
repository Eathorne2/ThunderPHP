---
title: "Generic Processing and Output"
slug: "generic-processing-and-output"
description: "Reference for process and output methods, plus output download behavior."
published: true
order: 11
source_id: 95
keywords: ["generic", "processing", "output", "reference", "process", "methods", "plus", "download", "behavior", "classes", "image", "class", "basic", "resize", "through", "crop", "contain", "display", "inline", "force", "important", "note"]
---

The class provides a generic `process()` method and a direct browser `output()` method.

**process()**

Method signature:

`process(string $filename, string $mode = 'resize', array $options = []): array`

Supported modes:

- `resize`
- `crop`
- `contain`

**Basic Resize Through process()**

```php
$image = new \Core\Image();

$result = $image->process(ROOTPATH . 'public/uploads/photo.jpg', 'resize', [
	'max_size' => 1200,
	'dest' => ROOTPATH . 'public/uploads/photo_large.jpg',
]);
```

**Basic Crop Through process()**

```php
$image = new \Core\Image();

$result = $image->process(ROOTPATH . 'public/uploads/photo.jpg', 'crop', [
	'width' => 300,
	'height' => 300,
	'position' => 'top',
	'dest' => ROOTPATH . 'public/uploads/photo_square.jpg',
]);
```

**Contain Through process()**

```php
$image = new \Core\Image();

$result = $image->process(ROOTPATH . 'public/uploads/logo.png', 'contain', [
	'width' => 400,
	'height' => 300,
	'bg_color' => [255,255,255],
	'dest' => ROOTPATH . 'public/uploads/logo_box.png',
]);
```

**output()**

Method signature:

`output(string $filename, bool $exit_after = true, ?string $download_name = null): bool`

This sends the image file directly to the browser with the correct headers.

**Display Inline**

```php
$image = new \Core\Image();
$image->output(ROOTPATH . 'public/uploads/thumbs/photo.webp');
```

**Force Download**

```php
$image = new \Core\Image();
$image->output(ROOTPATH . 'public/uploads/thumbs/photo.webp', true, 'downloaded-photo.webp');
```

**Important Note**

If headers have already been sent, `output()` will fail. Use it before any HTML output.
