---
title: "Recipe: Convert Existing Library Images to WEBP"
slug: "recipe-convert-existing-library-images-to-webp"
description: "A batch-style example for converting existing stored images into webp."
published: true
order: 16
source_id: 100
keywords: ["recipe", "convert", "existing", "library", "images", "webp", "batch-style", "example", "converting", "stored", "classes", "image", "class", "case", "simple", "loop", "through", "multiple", "files", "practical", "tip"]
---

This recipe shows how to convert existing image files to `webp` format.

**Use Case**

You already have a library of JPEG or PNG images and want `webp` copies for faster frontend delivery.

**Simple Example**

```php
use \Core\Image;

$image = new Image();
$image->set_webp_quality(80);

$webp = $image->convert_to_webp(
	ROOTPATH . 'public/uploads/gallery/photo1.jpg',
	ROOTPATH . 'public/uploads/gallery-webp/photo1.webp'
);

if(!is_file($webp))
{
	echo $image->error;
}
```

**Loop Through Multiple Files**

```php
use \Core\Image;

$image = new Image();
$image->set_webp_quality(80);

$files = [
	ROOTPATH . 'public/uploads/gallery/photo1.jpg',
	ROOTPATH . 'public/uploads/gallery/photo2.jpg',
	ROOTPATH . 'public/uploads/gallery/photo3.png',
];

foreach($files as $file)
{
	$info = pathinfo($file);

	$dest = ROOTPATH . 'public/uploads/gallery-webp/' . $info['filename'] . '.webp';
	$image->convert_to_webp($file, $dest);

	if(!is_file($dest))
	{
		echo $image->error;
	}
}
```

**Practical Tip**

Keep the originals. Use the `webp` copies only for display delivery unless you are absolutely sure you no longer need the source format.
