---
title: "Thumbnail Generation"
slug: "thumbnail-generation"
description: "Reference for get_thumbnail, make_thumbnail, make_thumbnail_as, and thumbnail naming."
published: true
order: 7
source_id: 91
keywords: ["thumbnail", "generation", "reference", "get_thumbnail", "make_thumbnail", "make_thumbnail_as", "naming", "classes", "image", "class", "basic", "example", "resize", "mode", "contain", "choose", "output", "extension", "default"]
---

The class provides several helpers for creating thumbnail files.

**get_thumbnail()**

Method signature:

`get_thumbnail(string $filename, int $width = 700, int $height = 700, bool $replace = false, string $mode = 'crop', array $bg_color = [255,255,255], ?string $output_ext = null): string`

This generates a thumbnail using the internal naming rules. If a thumbnail already exists and `replace` is `false`, the existing file is returned.

**Basic Example**

```php
$image = new \Core\Image();
$thumb = $image->get_thumbnail(ROOTPATH . 'public/uploads/photo.jpg', 300, 300);
```

**Resize Mode Thumbnail**

```php
$image = new \Core\Image();
$thumb = $image->get_thumbnail(ROOTPATH . 'public/uploads/photo.jpg', 600, 400, true, 'resize');
```

**Contain Mode Thumbnail**

```php
$image = new \Core\Image();
$thumb = $image->get_thumbnail(ROOTPATH . 'public/uploads/logo.png', 400, 300, true, 'contain', [255,255,255]);
```

**Choose Output Extension**

```php
$image = new \Core\Image();
$thumb = $image->get_thumbnail(ROOTPATH . 'public/uploads/photo.jpg', 300, 300, true, 'crop', [255,255,255], 'webp');
```

**make_thumbnail()**

If you want full control over the destination path, use `make_thumbnail()`.

```php
$image = new \Core\Image();

$image->make_thumbnail(
	ROOTPATH . 'public/uploads/photo.jpg',
	ROOTPATH . 'public/uploads/thumbs/photo_small.jpg',
	300,
	300,
	'crop'
);
```

**make_thumbnail_as()**

Use `make_thumbnail_as()` when the thumbnail should be saved in a different format from the source.

```php
$image = new \Core\Image();

$image->make_thumbnail_as(
	ROOTPATH . 'public/uploads/photo.jpg',
	ROOTPATH . 'public/uploads/thumbs/photo_small.webp',
	300,
	300,
	'crop'
);
```

**Default Thumbnail Naming**

Readable names typically look like this:

- `photo_crop_300x300.jpg`
- `photo_resize_800x600.jpg`
- `photo_contain_400x300.webp`

If `use_hashed_names(true)` is enabled, the class uses hashed filenames instead.
