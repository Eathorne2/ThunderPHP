---
title: "Practical Recipes"
slug: "practical-recipes"
description: "Real-world examples showing how to use the Request class in common plugin scenarios."
published: true
order: 17
source_id: 83
keywords: ["practical", "recipes", "real-world", "examples", "showing", "request", "class", "common", "plugin", "scenarios", "classes", "recipe", "normal", "form", "submission", "avatar", "upload", "pdf", "document", "gallery", "save", "detailed", "metadata", "database"]
---

This section shows common real-world usage patterns.

**Recipe 1: Normal form submission**

```php
$req = new \\Core\Request();

if($req->posted())
{
	$data = [
		'name' => trim($req->post('name')),
		'email' => trim($req->post('email')),
		'status' => (int)$req->post('status', 1),
	];

	print_r($data);
}
```

**Recipe 2: Avatar upload**

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
	'prefix' => 'avatar_',
]);

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

**Recipe 3: PDF document upload**

```php
$req = new \\Core\Request();

$req->set_upload_rule('manual', [
	'folder' => 'uploads/manuals',
	'max_size' => 20,
	'mime_types' => ['application/pdf'],
	'extensions' => ['pdf'],
	'prefix' => 'manual_',
]);

$file = $req->upload_files('manual');
```

**Recipe 4: Gallery upload**

```php
$req = new \\Core\Request();

$req->set_upload_rule('photos', [
	'folder' => 'uploads/gallery',
	'max_size' => 8,
	'mime_types' => ['image/jpeg', 'image/png', 'image/webp'],
	'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
	'verify_image' => true,
	'prefix' => 'gallery_',
]);

$photos = $req->upload_files('photos');
print_r($photos);
```

**Recipe 5: Save detailed upload metadata to database**

```php
$req = new \\Core\Request();

$req->set_upload_rule('document', [
	'folder' => 'uploads/documents',
	'max_size' => 10,
	'mime_types' => ['application/pdf'],
	'extensions' => ['pdf'],
]);

$result = $req->upload_files_detailed('document');

if(!$req->has_upload_errors() && !empty($result[0]['success']))
{
	$file = $result[0];

	$data = [
		'original_name' => $file['original_name'],
		'file_path' => $file['path'],
		'mime_type' => $file['mime_type'],
		'extension' => $file['extension'],
		'file_size' => $file['size'],
	];
}
```

These recipes cover the most common use cases you are likely to encounter in ThunderPHP plugins.
