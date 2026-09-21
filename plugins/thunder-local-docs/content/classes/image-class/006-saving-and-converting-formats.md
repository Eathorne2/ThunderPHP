---
title: "Saving and Converting Formats"
slug: "saving-and-converting-formats"
description: "Reference for save_as and convert_to_webp methods."
published: true
order: 6
source_id: 90
keywords: ["saving", "converting", "formats", "reference", "save_as", "convert_to_webp", "methods", "classes", "image", "class", "convert", "jpg", "webp", "png", "custom", "path", "why"]
---

The class can save an image into another file and even another format using the destination file extension.

**save_as()**

Method signature:

`save_as(string $filename, string $dest): string`

This reads an existing image and writes it to a new destination. The output format is determined by the extension of `dest`.

**Convert JPG To WEBP**

```php
$image = new \Core\Image();

$image->save_as(
	ROOTPATH . 'public/uploads/photo.jpg',
	ROOTPATH . 'public/uploads/photo.webp'
);
```

**Convert PNG To JPG**

```php
$image = new \Core\Image();

$image->save_as(
	ROOTPATH . 'public/uploads/image.png',
	ROOTPATH . 'public/uploads/image.jpg'
);
```

**convert_to_webp()**

Method signature:

`convert_to_webp(string $filename, ?string $dest = null): string`

This is a convenience method for saving an image as `webp`.

```php
$image = new \Core\Image();
$image->convert_to_webp(ROOTPATH . 'public/uploads/photo.jpg');
```

If `dest` is omitted, the method creates a path with the same filename and a `.webp` extension.

**Convert To Custom WEBP Path**

```php
$image = new \Core\Image();

$image->convert_to_webp(
	ROOTPATH . 'public/uploads/photo.jpg',
	ROOTPATH . 'public/uploads/converted/photo.webp'
);
```

**Why Convert To WEBP**

WEBP often provides smaller file sizes than JPEG or PNG at similar visual quality, especially for frontend thumbnails and card images.
