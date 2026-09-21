---
title: "Practical Recipes"
slug: "practical-recipes"
description: "Real-world usage examples."
published: true
order: 15
source_id: 66
keywords: ["practical", "recipes", "real-world", "usage", "examples", "classes", "session", "class", "login", "system", "flash", "message", "display", "form", "validation", "shopping", "cart", "page", "view", "counter", "role", "check"]
---

**Login system**

```php
if($user){
    $session->auth($user, true);
    $session->flash('success', 'Welcome back!');
    redirect('dashboard');
}
```

**Flash message display**

```php
if($msg = $session->get_flash('success')){
    echo "<div class='alert alert-success'>$msg</div>";
}
```

**Form validation**

```php
$session->set_old($_POST);
$session->flash('error', 'Validation failed');
redirect('form');
```

**Shopping cart**

```php
$session->push('cart.items', [
    'id' => 1,
    'qty' => 1
]);

$total = count($session->get('cart.items', []));
```

**Page view counter**

```php
$views = $session->increment('page_views');
```

**Role check**

```php
if(!$session->is_admin()){
    die('Access denied');
}
```
