---
title: "Using Setter Methods"
slug: "using-setter-methods"
description: "Configuring uploads with setter methods."
published: true
order: 9
source_id: 75
keywords: ["setter", "methods", "configuring", "uploads", "classes", "request", "class"]
---

The class includes setter methods so you do not have to mutate upload-related properties directly.

You can do this:

```php
$req = new \\Core\Request();

$req->set_upload_folder('uploads/users')
	->set_upload_max_size(5)
	->set_upload_file_types(['image/jpeg', 'image/png'])
	->set_upload_extensions(['jpg', 'jpeg', 'png'])
	->set_file_prefix('user_')
	->set_verify_images(true)
	->set_image_dimensions(150, 150, 3000, 3000);
```

Available setter methods include:

- `set_upload_folder()`
- `set_upload_max_size()`
- `set_upload_file_types()`
- `set_upload_extensions()`
- `set_file_prefix()`
- `set_verify_images()`
- `set_image_dimensions()`
- `set_filename_callback()`
- `set_upload_rule()`

These methods return the current object, so they can be chained.

This leads to:

- cleaner calling code
- less accidental misconfiguration
- easier to validate values later
- easier to extend without breaking existing code
- keeps intent obvious in controllers and plugins

This style is especially useful in larger plugins where each upload field may need its own configuration.
