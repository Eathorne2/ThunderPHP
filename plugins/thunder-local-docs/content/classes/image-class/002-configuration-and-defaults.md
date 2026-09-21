---
title: "Configuration and Defaults"
slug: "configuration-and-defaults"
description: "Learn how to configure quality, compression, thumbnail directories, and file naming behavior."
published: true
order: 2
source_id: 86
keywords: ["configuration", "defaults", "learn", "configure", "quality", "compression", "thumbnail", "directories", "file", "naming", "behavior", "classes", "image", "class", "available", "methods", "jpeg", "png", "webp", "allow", "prevent", "upscaling", "set", "separate"]
---

Before processing images, you can adjust the class configuration to control quality, compression, thumbnail folder behavior, and whether small images should be enlarged.

**Available Configuration Methods**

- `set_jpeg_quality(int $quality)`
- `set_png_compression(int $compression)`
- `set_webp_quality(int $quality)`
- `allow_upscale(bool $upscale = true)`
- `set_thumbnail_dir(string $dir)`
- `use_hashed_names(bool $use_hashed_names = true)`

**JPEG Quality**

JPEG quality is a value from `0` to `100`. Higher values preserve more detail but create larger files.

```php
$image = new \Core\Image();
$image->set_jpeg_quality(85);
```

A practical range for web uploads is often `80` to `88`.

**PNG Compression**

PNG compression is a value from `0` to `9`.

```php
$image = new \Core\Image();
$image->set_png_compression(7);
```

Higher values may reduce file size but can cost more CPU time.

**WEBP Quality**

WEBP quality also uses a value from `0` to `100`.

```php
$image = new \Core\Image();
$image->set_webp_quality(80);
```

A good range for general web thumbnails is often `75` to `85`.

**Allow or Prevent Upscaling**

If upscaling is disabled, small images will not be enlarged beyond their original size during resize or contain operations. This helps prevent blurry results.

```php
$image = new \Core\Image();
$image->allow_upscale(false);
```

**Set a Separate Thumbnail Directory**

You can store thumbnails in a different folder instead of placing them next to the original image.

```php
$image = new \Core\Image();
$image->set_thumbnail_dir(ROOTPATH . 'public/uploads/thumbs/');
```

**Readable or Hashed Thumbnail Names**

By default, thumbnail names are human-readable. You can switch to hashed names if you prefer less predictable output names.

```php
$image = new \Core\Image();
$image->use_hashed_names(true);
```

**Recommended Defaults**

A balanced configuration for many web applications looks like this:

```php
$image = new \Core\Image();

$image->set_jpeg_quality(85)
      ->set_png_compression(7)
      ->set_webp_quality(80)
      ->allow_upscale(false);
```
