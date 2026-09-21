---
title: "Request Handling and File Uploads"
slug: "request-handling-and-file-uploads"
description: "Current Request class behavior for methods, input reading, and modern upload workflows."
published: true
order: 10
source_id: 233
keywords: ["request", "handling", "file", "uploads", "current", "class", "behavior", "methods", "input", "reading", "modern", "upload", "workflows", "plugin", "reference", "refrence", "thunderphp", "developers", "core", "method", "helpers"]
---

The `\Core\Request` class is now more capable than the older summary docs suggested.

It covers:

- method detection
- `$_POST`, `$_GET`, `$_FILES`, and `$_REQUEST`
- request metadata
- structured uploads with validation support

**Core Method Helpers**

- `method()`
- `posted()`
- `is_get()`
- `is_ajax()`

**Reading Input**

- `post()`
- `input()`
- `get()`
- `files()`
- `all()`

Example:

```php
$req = new \Core\Request;

if($req->posted())
{
    $title = $req->post('title');
    $slug  = $req->post('slug', '');
}
```

**Uploads**

`upload_files()` supports:

- single file uploads
- multiple uploads using fields like `photos[]`
- all fields at once
- per-field rules
- MIME checks
- extension checks
- optional image validation
- optional image dimension limits
- grouped upload results

The upload shape is normalized internally so plugin authors do not have to manually untangle raw `$_FILES` structures in normal use. Error checking should still be done through upload error helpers before assuming a file was stored successfully.
