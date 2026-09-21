---
title: "Filename Generation and Security"
slug: "filename-generation-and-security"
description: "How filenames are sanitized, randomized, and optionally customized with callbacks."
published: true
order: 15
source_id: 81
keywords: ["filename", "generation", "security", "filenames", "sanitized", "randomized", "optionally", "customized", "callbacks", "classes", "request", "class", "custom", "callback"]
---

The upload system does not store files under their original names directly.

Instead, it:

- sanitizes the base filename
- preserves the extension
- adds a random value to reduce collisions and guessing
- optionally applies a custom prefix
- optionally allows custom filename generation through a callback

Example using a prefix:

```php
$req = new \\Core\Request();

$req->set_file_prefix('user_');
```

A file such as `My Photo.jpg` may become something like:

```php
uploads/avatars/user_my_photo_a1b2c3d4e5f6g7h8.jpg
```

This is safer than predictable names because it reduces:

- accidental overwriting
- easy URL guessing
- information leakage through filenames
- predictable file enumeration

**Custom filename callback**

If you need full control, use `set_filename_callback()`.

```php
$req->set_filename_callback(function($original_name, $safe_name, $extension, $field_name, $file_info){

	$id = rand(1000,9999);
	return $field_name . '_' . $id . '.' . $extension;
});
```

The callback receives:

- original name
- safe base name
- extension
- field name
- file information array

Use this carefully. A custom callback should still generate safe and unique file names.

In most cases, the built-in randomized naming is the safest default.
