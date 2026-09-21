---
title: "Images and Media Workflows"
slug: "images-and-media-workflows"
description: "Current Image class features for resizing, cropping, thumbnails, conversion, and browser output."
published: true
order: 12
source_id: 235
keywords: ["images", "media", "workflows", "current", "image", "class", "features", "resizing", "cropping", "thumbnails", "conversion", "browser", "output", "plugin", "reference", "refrence", "thunderphp", "developers", "configuration", "methods", "choosing", "right", "method"]
---

The current `\Core\Image` class is much more capable than a simple resize helper.

It supports:

- proportional resize
- exact crop
- contain fit inside a box
- save-as conversion
- WebP conversion
- single thumbnail generation
- bulk thumbnail generation
- uploaded image processing
- building files from binary strings
- direct browser output

**Configuration Methods**

- `set_jpeg_quality()`
- `set_png_compression()`
- `set_webp_quality()`
- `allow_upscale()`
- `set_thumbnail_dir()`
- `use_hashed_names()`

**Choosing the Right Method**

- use `resize()` for proportional downscaling
- use `crop()` for fixed rectangles such as avatars or cards
- use `fit_contain()` when the full image must remain visible, such as logos or admin previews

This makes the Image class especially useful inside upload pipelines, media libraries, blog plugins, and admin systems.
