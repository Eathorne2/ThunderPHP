---
title: "Create the Frontend View"
slug: "create-the-frontend-view"
description: "Build the first frontend page using the active look."
published: true
order: 5
source_id: 258
keywords: ["create", "frontend", "view", "build", "first", "page", "active", "look", "getting", "started", "plugin"]
---

Create:

`looks/main/frontend/index.php`

Example:

```php
<div class="container py-4">
    <h1>Hello from ThunderPHP</h1>
    <p>This is my first plugin frontend page.</p>
</div>
```

Because the plugin registered a `view` hook for `hello.index`, visiting the `/hello` route should now render this file through the active look system.
