---
title: "Per-Field Upload Rules"
slug: "per-field-upload-rules"
description: "How to define different upload rules for different file inputs."
published: true
order: 10
source_id: 76
keywords: ["per-field", "upload", "rules", "define", "different", "file", "inputs", "classes", "request", "class"]
---

Real-world forms often contain different upload fields with different requirements.

For example:

- `avatar` should accept only images
- `resume` should accept only PDFs
- `gallery` should accept multiple images
- `logo` should be limited to a specific dimension

The `set_upload_rule()` method lets you define rules per field.

Example:

```php
$req = new \\Core\Request();

$req->set_upload_rule('avatar', [
	'folder' => 'uploads/avatars',
	'max_size' => 5,
	'mime_types' => ['image/jpeg', 'image/png', 'image/webp'],
	'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
	'verify_image' => true,
	'min_width' => 150,
	'min_height' => 150,
	'max_width' => 3000,
	'max_height' => 3000,
	'prefix' => 'avatar_',
]);

$req->set_upload_rule('resume', [
	'folder' => 'uploads/resumes',
	'max_size' => 10,
	'mime_types' => ['application/pdf'],
	'extensions' => ['pdf'],
	'prefix' => 'resume_',
]);
```

Supported rule keys are:

- `folder`
- `max_size`
- `mime_types`
- `extensions`
- `verify_image`
- `min_width`
- `min_height`
- `max_width`
- `max_height`
- `prefix`

You can retrieve the rule later using `get_upload_rule()`.

```php
$rule = $req->get_upload_rule('avatar');
print_r($rule);
```

Per-field rules override the global upload settings for that field. This gives you precise control over each upload input without needing multiple request classes.
