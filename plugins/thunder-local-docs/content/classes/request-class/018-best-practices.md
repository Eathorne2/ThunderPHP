---
title: "Best Practices"
slug: "best-practices"
description: "Recommended habits for using the Request class safely and cleanly in ThunderPHP plugins."
published: true
order: 18
source_id: 84
keywords: ["best", "practices", "recommended", "habits", "request", "class", "safely", "cleanly", "thunderphp", "plugins", "classes", "prefer", "over", "direct", "superglobals", "per-field", "rules", "uploads", "validate", "images", "both", "mime", "types", "extensions"]
---

Here are some recommended practices when using the `Request` class.

**Prefer the class over direct superglobals**

Instead of reading from `$_POST`, `$_GET`, `$_FILES`, and `$_SERVER` everywhere, use the `Request` class methods consistently. That keeps your code cleaner and easier to update later.

**Use per-field rules for uploads**

If a form contains different upload fields, do not try to force them through one shared configuration. Use `set_upload_rule()` so each field has the correct rules.

**Validate images as images**

If a field is meant for images, enable `verify_image` and set size limits where appropriate.

**Validate both MIME types and extensions**

This gives you more reliable upload validation than checking only one of them.

**Do not allow `any` unless you truly need it**

Using `any` for MIME types or extensions weakens upload security and should only be used for controlled internal workflows.

**Use detailed results when storing files in the database**

If you need metadata like file size or MIME type, prefer `upload_files_detailed()`.

**Avoid publicly exposing sensitive uploads**

For private documents, consider storing files outside the public web root and serving them through controlled routes later.

**Use randomized filenames**

Do not rely on original filenames or predictable names if the uploads may later be publicly accessible.

**Check for errors every time**

After any upload attempt, always check `has_upload_errors()` before assuming the file was stored successfully.

Example:

```php
$req = new \\Core\Request();

$file = $req->upload_files('avatar');

if($req->has_upload_errors())
{
	message('error', 'The upload failed');
}
else
{
	message('success', 'Upload completed successfully');
}
```

These practices will help keep your plugins more predictable, safer, and easier to maintain.
