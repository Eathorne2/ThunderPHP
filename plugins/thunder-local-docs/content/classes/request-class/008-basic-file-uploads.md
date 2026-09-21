---
title: "Basic File Uploads"
slug: "basic-file-uploads"
description: "How to upload single files and multiple files using `upload_files()`."
published: true
order: 8
source_id: 74
keywords: ["basic", "file", "uploads", "upload", "single", "files", "multiple", "upload_files", "classes", "request", "class", "one", "input", "uploading", "all", "fields"]
---

The `upload_files()` method is the main method used to process uploaded files.

It supports:

- a single file input such as `avatar`
- multiple files using one field such as `photos[]`
- processing all uploaded fields at once

**Single file upload**

```php
$req = new \\Core\Request();

$req->set_upload_folder('uploads/avatars')
	->set_upload_max_size(5)
	->set_upload_file_types(['image/jpeg', 'image/png'])
	->set_upload_extensions(['jpg', 'jpeg', 'png']);

$avatar = $req->upload_files('avatar');

if($req->has_upload_errors())
{
	print_r($req->get_upload_errors());
}
else
{
	echo $avatar;
}
```

If one specific field is uploaded and it contains one file, `upload_files('avatar')` returns a string path.

**Multiple files from one input**

HTML:

```php
<input type="file" name="photos[]" multiple>
```

PHP:

```php
$req = new \\Core\Request();

$req->set_upload_folder('uploads/gallery')
	->set_upload_max_size(8)
	->set_upload_file_types(['image/jpeg', 'image/png', 'image/webp'])
	->set_upload_extensions(['jpg', 'jpeg', 'png', 'webp']);

$photos = $req->upload_files('photos');

if($req->has_upload_errors())
{
	print_r($req->get_upload_errors());
}
else
{
	print_r($photos);
}
```

If the field contains multiple files, the method returns an array of saved paths.

**Uploading all fields**

```php
$req = new \\Core\Request();

$result = $req->upload_files();
print_r($result);
```

When no key is passed, the result is grouped by field name. This makes it easier to handle forms that contain several upload fields at once.
