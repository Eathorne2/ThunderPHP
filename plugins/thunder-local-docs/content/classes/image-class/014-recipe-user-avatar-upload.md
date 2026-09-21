---
title: "Recipe: User Avatar Upload"
slug: "recipe-user-avatar-upload"
description: "A common avatar workflow using square crops and cleanup of previous thumbnails."
published: true
order: 14
source_id: 98
keywords: ["recipe", "user", "avatar", "upload", "common", "workflow", "square", "crops", "cleanup", "previous", "thumbnails", "classes", "image", "class", "case", "example", "why", "top", "help"]
---

This recipe creates a square avatar thumbnail from a user upload.

**Use Case**

A user profile picture upload where the system stores one original image and one or more square crops.

**Example**

```php
use \Core\Image;

$image = new Image();

$image->set_thumbnail_dir(ROOTPATH . 'public/uploads/avatars/thumbs/')
      ->set_webp_quality(80)
      ->allow_upscale(false);

$stored = $image->from_upload(
	$_FILES['avatar'],
	ROOTPATH . 'public/uploads/avatars/originals/user_45.jpg'
);

if(!$stored['success'])
{
	die($stored['error']);
}

$image->delete_thumbnails($stored['path']);

$thumbs = $image->smart_thumbnail($stored['path'], [
	['width' => 80, 'height' => 80, 'mode' => 'crop', 'ext' => 'webp', 'position' => 'top'],
	['width' => 200, 'height' => 200, 'mode' => 'crop', 'ext' => 'webp', 'position' => 'top'],
	['width' => 400, 'height' => 400, 'mode' => 'crop', 'ext' => 'webp', 'position' => 'top'],
]);
```

**Why `top` Can Help**

Many profile photos place the person's face higher in the image. Using `top` instead of `center` may give a better crop for avatars.
