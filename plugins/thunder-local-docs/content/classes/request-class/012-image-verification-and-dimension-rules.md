---
title: "Image Verification and Dimension Rules"
slug: "image-verification-and-dimension-rules"
description: "How to verify real images and enforce minimum or maximum dimensions."
published: true
order: 12
source_id: 78
keywords: ["image", "verification", "dimension", "rules", "verify", "real", "images", "enforce", "minimum", "maximum", "dimensions", "classes", "request", "class"]
---

When a field is meant to receive an image, it is often useful to verify that the uploaded file is a real image and optionally enforce dimension limits.

Enable image verification globally:

```php
$req = new \\Core\Request();

$req->set_verify_images(true);
```

Set global image dimensions:

```php
$req->set_image_dimensions(200, 200, 3000, 3000);
```

That means:

- minimum width: `200`
- minimum height: `200`
- maximum width: `3000`
- maximum height: `3000`

You can also configure image rules per field:

```php
$req->set_upload_rule('logo', [
	'folder' => 'uploads/logos',
	'mime_types' => ['image/png', 'image/jpeg', 'image/webp'],
	'extensions' => ['png', 'jpg', 'jpeg', 'webp'],
	'verify_image' => true,
	'max_width' => 512,
	'max_height' => 512,
]);
```

This is useful for:

- logos
- avatars
- thumbnails
- gallery images
- cover art

Internally, the class uses `getimagesize()` to confirm the file behaves like a real image.

This helps catch cases where a file may have an allowed extension but is not actually a valid image.

If a file fails dimension rules, it will not be moved and the error will be available through `get_upload_errors()`.
