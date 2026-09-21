---
title: "Upload Errors"
slug: "upload-errors"
description: "How upload errors are collected, read, and cleared."
published: true
order: 14
source_id: 80
keywords: ["upload", "errors", "collected", "read", "cleared", "classes", "request", "class"]
---

The upload system collects validation and move errors instead of failing silently.

Use `has_upload_errors()` to check whether any errors occurred.

```php
$req = new \\Core\Request();

$req->upload_files('avatar');

if($req->has_upload_errors())
{
	print_r($req->get_upload_errors());
}
```

Use `get_upload_errors()` to retrieve all error messages.

```php
$errors = $req->get_upload_errors();
print_r($errors);
```

Use `get_upload_error_code()` to read the latest upload error code.

```php
$code = $req->get_upload_error_code();
echo $code;
```

Use `clear_upload_errors()` when you want to reset the upload state manually.

```php
$req->clear_upload_errors();
```

Errors are grouped by field name. Example:

```php
Array
(
	[avatar] => Array
	(
		[0] => Invalid file type: image.bmp
	)
	[photos] => Array
	(
		[0] => File too large: holiday.jpg
		[1] => Invalid file extension: notes.txt
	)
)
```

System-level folder problems may be stored under `_system`.

```php
Array
(
	[_system] => Array
	(
		[0] => Upload folder is not writable: uploads/gallery/
	)
)
```

This structure makes it easier to show upload feedback in forms or admin pages.
