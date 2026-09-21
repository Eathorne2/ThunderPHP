---
title: "Detailed Upload Results"
slug: "detailed-upload-results"
description: "Using `upload_files_detailed()` to get metadata such as MIME type, extension, size, width, and height."
published: true
order: 13
source_id: 79
keywords: ["detailed", "upload", "results", "upload_files_detailed", "get", "metadata", "such", "mime", "type", "extension", "size", "width", "height", "classes", "request", "class"]
---

Sometimes you need more than just the saved file path. You may also want to know:

- original file name
- saved path
- MIME type
- extension
- file size
- readable file size text
- image width and height

That is what `upload_files_detailed()` is for.

Example:

```php
$req = new \\Core\Request();

$req->set_upload_rule('document', [
	'folder' => 'uploads/documents',
	'max_size' => 10,
	'mime_types' => ['application/pdf'],
	'extensions' => ['pdf'],
]);

$result = $req->upload_files_detailed('document');
print_r($result);
```

A successful detailed result may look like this:

```php
Array
(
	[0] => Array
	(
		[success] => 1
		[field_name] => document
		[original_name] => report.pdf
		[path] => uploads/documents/report_a1b2c3d4.pdf
		[mime_type] => application/pdf
		[extension] => pdf
		[size] => 245120
		[size_text] => 239.38 KB
	)
)
```

For an image field, width and height may also be included:

```php
Array
(
	[0] => Array
	(
		[success] => 1
		[field_name] => avatar
		[original_name] => me.jpg
		[path] => uploads/avatars/me_fedcba12.jpg
		[mime_type] => image/jpeg
		[extension] => jpg
		[size] => 105000
		[size_text] => 102.54 KB
		[width] => 800
		[height] => 800
	)
)
```

This method is ideal when you want to save upload metadata into the database after the file is processed.
