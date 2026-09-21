---
title: "Accessing Uploaded File Data"
slug: "accessing-uploaded-file-data"
description: "How `files()` works before upload processing and what PHP file arrays look like."
published: true
order: 7
source_id: 73
keywords: ["accessing", "uploaded", "file", "data", "files", "works", "before", "upload", "processing", "php", "arrays", "look", "like", "classes", "request", "class"]
---

Use `files()` when you want raw access to uploaded file data from `$_FILES`.

With no key, it returns the entire `$_FILES` array.

```php
$req = new \\Core\Request();

print_r($req->files());
```

With a key, it returns the raw file entry for that field.

```php
$req = new \\Core\Request();

$avatar = $req->files('avatar');
print_r($avatar);
```

A normal single upload usually looks like this:

```php
Array
(
	[name] => photo.jpg
	[type] => image/jpeg
	[tmp_name] => /tmp/php12345
	[error] => 0
	[size] => 245120
)
```

A multiple upload field like `photos[]` usually looks like this:

```php
Array
(
	[name] => Array
	(
		[0] => image1.jpg
		[1] => image2.jpg
	)
	[type] => Array
	(
		[0] => image/jpeg
		[1] => image/jpeg
	)
	[tmp_name] => Array
	(
		[0] => /tmp/phpAAA
		[1] => /tmp/phpBBB
	)
	[error] => Array
	(
		[0] => 0
		[1] => 0
	)
	[size] => Array
	(
		[0] => 123456
		[1] => 654321
	)
)
```

The upload system automatically normalizes both shapes into a single internal structure, so you usually do not need to deal with raw file array complexity yourself unless you specifically want to inspect the original file data.
