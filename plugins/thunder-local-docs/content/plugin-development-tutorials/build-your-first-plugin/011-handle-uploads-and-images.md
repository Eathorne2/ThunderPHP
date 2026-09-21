---
title: "Handle Uploads and Images"
slug: "handle-uploads-and-images"
description: "Use Request and Image together for featured images and media-friendly plugins."
published: true
order: 11
source_id: 250
keywords: ["handle", "uploads", "images", "request", "image", "together", "featured", "media-friendly", "plugins", "plugin", "dev", "tutorials", "build", "first"]
---

For a featured image upload, combine `Request` and `Image`.

```php
$req = new \Core\Request;

$files = $req->upload_files('featured_image');

if(!$req->has_upload_errors())
{
    $image = new \Core\Image;
    $image->set_webp_quality(80);
    $image->resize(ROOTPATH . $files['featured_image']['full_path'], 1600);
    $thumb = $image->get_thumbnail(ROOTPATH . $files['featured_image']['full_path'], 600, 400);
}
```

The current Request layer supports structured upload rules and grouped results, while the current Image class supports resize, crop, contain fit, conversions, thumbnail generation, and output workflows.
