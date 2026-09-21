---
title: "Utility Methods and File Management"
slug: "utility-methods-and-file-management"
description: "Reference for thumbnail deletion, directories, naming, and behavior notes."
published: true
order: 12
source_id: 96
keywords: ["utility", "methods", "file", "management", "reference", "thumbnail", "deletion", "directories", "naming", "behavior", "notes", "classes", "image", "class", "delete_thumbnails", "directory", "creation", "readable", "vs", "hashed", "names", "error", "property", "practical"]
---

The class includes helpers for managing thumbnail files and destination directories.

**delete_thumbnails()**

Method signature:

`delete_thumbnails(string $filename): void`

This removes generated thumbnails that match the internal naming pattern for a given source image.

```php
$image = new \Core\Image();
$image->delete_thumbnails(ROOTPATH . 'public/uploads/photo.jpg');
```

This is useful when an original image is replaced and old thumbnails should be removed.

**Directory Creation**

When saving images, the class automatically creates missing destination folders where possible.

That means code like this works without manually creating the folder first:

```php
$image = new \Core\Image();

$image->make_thumbnail(
	ROOTPATH . 'public/uploads/photo.jpg',
	ROOTPATH . 'public/uploads/thumbs/cards/photo_300x200.jpg',
	300,
	200,
	'crop'
);
```

**Readable vs Hashed Names**

Readable names are useful during development and debugging.

Hashed names are useful when:

- you do not want predictable generated filenames
- you want shorter names in a dedicated thumbnails folder
- you want output naming to be derived consistently from settings

**Error Property**

The class exposes an `error` property for the last encountered issue.

```php
$image = new \Core\Image();

$path = $image->convert_to_webp(ROOTPATH . 'public/uploads/test.xyz');

if(!is_file($path))
{
	echo $image->error;
}
```

**Practical Advice**

Use readable names during development. Move to hashed names later if you need them for privacy or cleaner storage.
