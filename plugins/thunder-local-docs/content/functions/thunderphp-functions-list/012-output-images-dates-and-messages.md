---
title: "Output, Images, Dates, and Messages"
slug: "output-images-dates-and-messages"
description: "General-purpose helpers for escaping output, formatting dates, loading placeholder images, flashing messages, and debugging."
published: true
order: 12
source_id: 128
keywords: ["output", "images", "dates", "messages", "general-purpose", "helpers", "escaping", "formatting", "loading", "placeholder", "flashing", "debugging", "functions", "thunderphp", "list", "covered", "esc", "string", "str", "get_image", "path", "type", "post", "get_date"]
---

**Functions covered**

- `esc()`
- `get_image()`
- `get_date()`
- `message()`
- `dd()`

**`esc(?string $str)`**

Escapes special HTML characters using `htmlspecialchars()`.

```php
<h1><?= esc($row->title) ?></h1>
```

Use this whenever displaying user-controlled text in HTML.

**`get_image(?string $path = '', string $type = 'post')`**

Returns an image URL if the file exists, otherwise returns a placeholder image.

```php
<img src="<?= get_image($row->image, 'post') ?>" alt="">
<img src="<?= get_image($user->avatar, 'male') ?>" alt="">
```

Supported fallback types in the provided helper are:

- `post`
- `male`
- `female`

Unknown types fall back to the generic no-image asset.

**`get_date(?string $date)`**

Formats a date into a more readable form.

```php
echo get_date('2025-01-10');
// 10th Jan, 2025
```

**`message(string $type, ?string $msg = '', bool $erase = false)`**

Stores or retrieves a session flash message.

To save:

```php
message('success', 'Post saved successfully');
message('fail', 'Unable to save the post');
```

To read:

```php
<?= message('success', '', true) ?>
<?= message('fail', '', true) ?>
```

If `erase` is `true`, the message is removed after reading.

**`dd(mixed $data, bool $stop = false)`**

Prints formatted debug output.

```php
dd($row);
dd($_POST, true);
```

Because it outputs HTML directly, it is best used only during development.

**Recipe: standard flash message block**

```php
<?php if(message('success')): ?>
    <div class="alert alert-success"><?= esc(message('success', '', true)) ?></div>
<?php endif; ?>

<?php if(message('fail')): ?>
    <div class="alert alert-danger"><?= esc(message('fail', '', true)) ?></div>
<?php endif; ?>
```
