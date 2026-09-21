---
title: "Cropping Images"
slug: "cropping-images"
description: "Reference for exact-dimension cropping, crop positions, and common use cases."
published: true
order: 4
source_id: 88
keywords: ["cropping", "images", "reference", "exact-dimension", "crop", "positions", "common", "cases", "classes", "image", "class", "method", "signature", "parameters", "basic", "another", "file", "position", "typical", "important", "behavior"]
---

The `crop()` method creates an image with exact dimensions by cropping the source image to fit the target ratio.

**Method Signature**

`crop(string $filename, int $max_width = 700, int $max_height = 700, ?string $dest = null, string $position = 'center'): string`

**Parameters**

- `filename`: source image path
- `max_width`: final width
- `max_height`: final height
- `dest`: optional destination path
- `position`: crop alignment such as `center`, `top`, `bottom`, `left`, or `right`

**Basic Crop**

```php
$image = new \Core\Image();
$image->crop(ROOTPATH . 'public/uploads/photo.jpg', 300, 300);
```

This creates a square crop, commonly used for avatars.

**Crop Into Another File**

```php
$image = new \Core\Image();

$image->crop(
	ROOTPATH . 'public/uploads/banner.jpg',
	1200,
	630,
	ROOTPATH . 'public/uploads/banner_card.jpg'
);
```

**Using Crop Position**

If a portrait photo has the person near the top, `top` may be more useful than `center`.

```php
$image = new \Core\Image();

$image->crop(
	ROOTPATH . 'public/uploads/profile.jpg',
	300,
	300,
	ROOTPATH . 'public/uploads/profile_crop.jpg',
	'top'
);
```

**Typical Use Cases**

- profile pictures
- blog cards
- hero image crops
- uniform product thumbnails

**Important Behavior**

Cropping is best when every output must have identical dimensions. This is usually what you want for grids and cards.
