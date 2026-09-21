---
title: "Structured Results and Metadata"
slug: "structured-results-and-metadata"
description: "Reference for result arrays, get_size, and how to inspect outputs."
published: true
order: 10
source_id: 94
keywords: ["structured", "results", "metadata", "reference", "result", "arrays", "get_size", "inspect", "outputs", "classes", "image", "class", "array", "structure", "example", "process", "why", "matter"]
---

Several methods return structured result arrays to make integration easier.

**Result Array Structure**

The internal `result()` helper returns this general shape:

- `success`
- `path`
- `mime`
- `width`
- `height`
- `size`
- `error`

**Example Using process()**

```php
$image = new \Core\Image();

$result = $image->process(ROOTPATH . 'public/uploads/photo.jpg', 'crop', [
	'width' => 300,
	'height' => 300,
	'dest' => ROOTPATH . 'public/uploads/photo_square.jpg',
]);

if($result['success'])
{
	echo $result['path'];
	echo $result['mime'];
}
else
{
	echo $result['error'];
}
```

**get_size()**

Method signature:

`get_size(string $filename): object|false`

This returns an object containing:

- `width`
- `height`
- `mime`

**Example**

```php
$image = new \Core\Image();
$info = $image->get_size(ROOTPATH . 'public/uploads/photo.jpg');

if($info)
{
	echo $info->width;
	echo $info->height;
	echo $info->mime;
}
```

**Why Structured Results Matter**

Structured arrays make it easier to:

- pass results to controllers and views
- store generated file information in the database
- detect failures without guessing from paths alone
- build reusable upload pipelines
