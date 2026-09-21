---
title: "Image Class Overview"
slug: "image-class-overview"
description: "Introduction to the ThunderPHP Image class, what it does, and when to use it."
published: true
order: 1
source_id: 85
keywords: ["image", "class", "overview", "introduction", "thunderphp", "does", "classes", "supported", "types", "main", "capabilities", "basic", "example", "resize", "crop", "contain"]
---

The `Image` class is a GD-based utility for working with image files inside ThunderPHP. It can resize, crop, contain, convert, generate thumbnails, process uploaded images, create files from binary strings, and output images directly to the browser.

This class is best used when you need server-side image processing for:

- profile pictures
- blog thumbnails
- product images
- admin previews
- format conversion such as `jpg` to `webp`
- image upload workflows

The class is designed to work primarily with file paths, which makes it easy to integrate with existing upload systems and file storage logic.

**Supported Image Types**

The class supports these formats for reading:

- `jpeg`
- `png`
- `gif`
- `webp`

It can also save to these same formats depending on the destination file extension or chosen output type.

**Main Capabilities**

The class provides methods for:

- proportional resize
- exact crop
- contain fit inside a box
- saving as another format
- converting to `webp`
- generating one thumbnail
- generating many thumbnails at once
- processing uploaded files
- building image files from raw binary data
- outputting files directly to the browser

**Basic Example**

```php
use \Core\Image;

$image = new Image();

$image->resize(ROOTPATH . 'public/uploads/photo.jpg', 1200);
```

**When To Use Resize, Crop, or Contain**

Use `resize()` when you want to reduce an image proportionally without forcing exact dimensions.

Use `crop()` when you want a fixed box such as `300x300` for avatars or `1200x630` for article cards.

Use `fit_contain()` when the whole image must remain visible inside a fixed area, such as logos or admin previews.
