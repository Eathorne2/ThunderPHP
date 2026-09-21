---
title: "Create the Admin View"
slug: "create-the-admin-view"
description: "Build the first admin page and load it through admin_main_content."
published: true
order: 6
source_id: 259
keywords: ["create", "admin", "view", "build", "first", "page", "load", "through", "admin_main_content", "getting", "started", "plugin"]
---

Create:

`looks/main/admin/index.php`

Example:

```php
<div class="container py-4">
    <h2>Hello World Admin</h2>
    <p>This is the admin page for my first plugin.</p>
</div>
```

Because the plugin registered `admin_main_content` for the `hello.admin` route, this view will be used when the admin route is matched.
