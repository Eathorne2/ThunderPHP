---
title: "Resizing Images"
slug: "resizing-images"
description: "Reference for proportional resizing and how destination paths work."
published: true
order: 3
source_id: 87
keywords: ["resizing", "images", "reference", "proportional", "destination", "paths", "work", "classes", "image", "class", "method", "signature", "parameters", "basic", "resize", "another", "file", "works", "typical", "cases", "useful", "note"]
---

The `resize()` method resizes an image proportionally so that its longest side fits within a given maximum size.

**Method Signature**

`resize(?string $filename, int $max_size = 700, ?string $dest = null): string`

**Parameters**

- `filename`: path to the source image
- `max_size`: maximum width or height
- `dest`: optional destination file path

If `dest` is omitted, the original file is overwritten.

**Basic Resize**

```php
$image = new \Core\Image();
$image->resize(ROOTPATH . 'public/uploads/photo.jpg', 1200);
```

This reduces the image so that its width or height does not exceed `1200`.

**Resize To Another File**

```php
$image = new \Core\Image();

$image->resize(
	ROOTPATH . 'public/uploads/photo.jpg',
	1200,
	ROOTPATH . 'public/uploads/photo_resized.jpg'
);
```

This preserves the original and writes the resized image to a new file.

**How It Works**

The class keeps the original aspect ratio. It does not stretch the image. If upscaling is disabled, smaller images will not be enlarged past their original size.

**Typical Use Cases**

- reducing large uploads before storage
- making medium-size display versions
- preparing files before conversion to another format

**Useful Note**

If you need exact dimensions such as `300x300`, use `crop()` or `fit_contain()` instead of `resize()`.
