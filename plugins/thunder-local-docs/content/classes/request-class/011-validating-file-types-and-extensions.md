---
title: "Validating File Types and Extensions"
slug: "validating-file-types-and-extensions"
description: "Why both MIME types and file extensions are checked and how to configure them."
published: true
order: 11
source_id: 77
keywords: ["validating", "file", "types", "extensions", "why", "both", "mime", "checked", "configure", "them", "classes", "request", "class", "type", "validation", "extension"]
---

The upload system validates both MIME types and file extensions.

This matters because file validation should not rely only on one layer.

**MIME type validation**

The class detects the real MIME type from the uploaded temporary file using `finfo_file()` or `mime_content_type()`.

Example global configuration:

```php
$req = new \\Core\Request();

$req->set_upload_file_types([
	'image/jpeg',
	'image/png',
	'image/webp',
]);
```

Example per-field configuration:

```php
$req->set_upload_rule('document', [
	'mime_types' => ['application/pdf'],
]);
```

**Extension validation**

The class also checks the file extension from the original filename.

```php
$req = new \\Core\Request();

$req->set_upload_extensions(['jpg', 'jpeg', 'png', 'webp']);
```

Per-field:

```php
$req->set_upload_rule('document', [
	'extensions' => ['pdf'],
]);
```

Why both checks are useful:

- MIME type helps confirm the file content
- extension helps keep naming and downstream usage consistent
- checking both makes it harder for a renamed file to bypass validation
- it reduces surprises when plugins expect certain file types later

If you really want to allow everything, you can use `any`:

```php
$req->set_upload_file_types(['any']);
$req->set_upload_extensions(['any']);
```

That should be used very carefully, especially for public uploads.
