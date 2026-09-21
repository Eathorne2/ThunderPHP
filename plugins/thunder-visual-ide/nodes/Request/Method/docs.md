# Request Method

Calls a documented method on a connected `\Core\Request` object. Selecting a method changes its parameter ports and Result output type.

The Request input is optional. When it is not connected, the compiler uses the same graph-level `$request = new \Core\Request();` service created by Request Service and other Request-aware nodes. The Request output passes that same object onward for chaining.

## Documented methods

- `method(): string` — Returns the current HTTP request method as an uppercase string such as GET, POST, PUT or DELETE.
- `posted(): bool` — Returns true only when the current HTTP request method is POST.
- `is_get(): bool` — Returns true when the current HTTP request method is GET.
- `is_ajax(): bool` — Detects an AJAX request through the HTTP_X_REQUESTED_WITH header.
- `post(?string $key = null, mixed $default = null): mixed` — Reads one POST value with a fallback, or the full POST array when no key is supplied.
- `input(?string $key = null, mixed $default = null): mixed` — Convenience alias for reading a posted input value with a default fallback.
- `get(?string $key = null, mixed $default = null): mixed` — Reads one query-string value with a fallback, or the full GET array when no key is supplied.
- `all(?string $key = null, mixed $default = null): mixed` — Reads one value or all data from REQUEST. Prefer post() or get() when the source is known.
- `has_post(string $key = ''): bool` — Checks whether a POST key exists without treating valid zero values as missing.
- `has_get(string $key = ''): bool` — Checks whether a GET key exists without treating valid zero values as missing.
- `has_file(string $key = ''): bool` — Checks whether a file field contains at least one actual uploaded file, including array-style fields.
- `files(?string $key = null): mixed` — Returns raw FILES data for one field or for all upload fields before processing.
- `set_upload_folder(string $folder = ''): object` — Sets the default destination folder used by file uploads and returns the Request object for chaining.
- `set_upload_max_size(int|float $max_size = 5): object` — Sets the default maximum upload size in megabytes and returns the Request object for chaining.
- `set_upload_file_types(array $types = []): object` — Sets allowed MIME types and returns the Request object for chaining.
- `set_upload_extensions(array $extensions = []): object` — Sets allowed filename extensions and returns the Request object for chaining.
- `set_file_prefix(string $prefix = ''): object` — Sets the prefix applied to generated upload filenames and returns the Request object for chaining.
- `set_verify_images(bool $verify = true): object` — Enables or disables real-image verification and returns the Request object for chaining.
- `set_image_dimensions(?int $min_width = null, ?int $min_height = null, ?int $max_width = null, ?int $max_height = null): object` — Sets optional minimum and maximum image dimensions and returns the Request object for chaining.
- `set_upload_rule(string $field = '', array $rule = []): object` — Defines upload settings for one field. Per-field rules override the global upload settings.
- `get_upload_rule(string $field = ''): array` — Returns the configured upload rule for one field.
- `set_filename_callback(callable $callback = null): object` — Sets a custom safe filename generator and returns the Request object for chaining.
- `upload_files(?string $field = null): mixed` — Processes one upload field or all fields and returns saved path strings grouped according to the upload shape.
- `upload_files_detailed(?string $field = null): array` — Processes uploads and returns detailed metadata such as original name, path, MIME type, extension, size and image dimensions.
- `has_upload_errors(): bool` — Returns true when the latest upload processing produced validation or move errors.
- `get_upload_errors(): array` — Returns upload errors grouped by field name, with system-level errors under _system.
- `get_upload_error_code(): mixed` — Returns the latest upload error code recorded by the Request upload system.
- `clear_upload_errors(): void` — Clears the current Request upload error state.
- `ip(): string` — Returns the client IP address detected for the current request.
- `uri(): string` — Returns the current request URI.
- `server(?string $key = null, mixed $default = null): mixed` — Reads one SERVER value with a fallback, or returns all server data when no key is supplied.
- `header(string $name = '', mixed $default = null): mixed` — Reads one request header by normal header name, such as Authorization.

## Upload rules

For `set_upload_rule()`, connect an associative Array node. The documented rule keys are:

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

Per-field rules override the global Request upload configuration for that field.

For `set_upload_file_types()` and `set_upload_extensions()`, connect an Array node. The documentation permits `['any']`, but it should only be used for controlled workflows because it weakens upload validation.

For `set_filename_callback()`, connect a callable expression from a compatible custom-code, function or variable node. The callback receives the original name, safe base name, extension, field name and file information array.

## Setter chaining

The upload setter methods return the Request object. You may continue through the node's **Request** output, or use the object-valued **Result** output where an object connection is appropriate.

## Results and errors

- `upload_files()` returns a path for one uploaded file, an array of paths for a multi-file field, or results grouped by field when no field is supplied.
- `upload_files_detailed()` returns upload metadata suitable for database storage.
- Always check `has_upload_errors()` after an upload attempt before assuming files were saved.
- `get_upload_errors()` returns messages grouped by field and may use `_system` for folder or environment failures.

Open **Browse Methods…** in the inspector or double-click the node for searchable signatures, examples, upload-rule guidance, result shapes, validation notes, and security considerations.
