---
title: "Upload and Binary Data Handling"
slug: "upload-and-binary-data-handling"
description: "Reference for from_upload and from_string helpers."
published: true
order: 9
source_id: 93
keywords: ["upload", "binary", "data", "handling", "reference", "from_upload", "from_string", "helpers", "classes", "image", "class", "store", "uploaded", "works", "example", "useful", "important", "note"]
---

The class can help store uploaded files and create image files from raw binary strings.

**from_upload()**

Method signature:

`from_upload(array $file, string $dest, bool $move = true): array`

This method expects a file array similar to `$_FILES['field_name']`.

**Store An Uploaded Image**

```php
$image = new \Core\Image();

$result = $image->from_upload(
	$_FILES['photo'],
	ROOTPATH . 'public/uploads/originals/photo.jpg'
);

if(!$result['success'])
{
	echo $result['error'];
}
```

**How It Works**

The method checks the temporary file, validates the upload error code, ensures the destination directory exists, and then moves or copies the file into place.

**from_string()**

Method signature:

`from_string(string $data, string $dest): array`

This method writes raw binary image data into a file and validates that the written file is a real image.

**Example**

```php
$image = new \Core\Image();

$data = file_get_contents(ROOTPATH . 'temp/example.jpg');

$result = $image->from_string(
	$data,
	ROOTPATH . 'public/uploads/imported/example.jpg'
);
```

**When This Is Useful**

- saving base64-decoded images
- importing remote image data already downloaded by your code
- rebuilding image files from stored binary content

**Important Note**

This class helps store and process image files, but validation policy such as allowed types, maximum file size, and naming strategy is usually best handled by your upload or request layer.
