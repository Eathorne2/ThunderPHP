---
title: "Authentication"
slug: "authentication"
description: "Handling user authentication using sessions."
published: true
order: 7
source_id: 58
keywords: ["authentication", "handling", "user", "sessions", "classes", "session", "class", "store", "data", "regenerate", "id", "logout", "check", "login", "status", "get"]
---

**Store user data**
- The `$user_row` must be a single user's row from the users table, as an object.

```php
$session->auth($user_row);
```

**Store user data and regenerate session ID**

```php
$session->auth($user_row, true); //if false, session ID does not regenerate
```

**Logout**

```php
$session->logout(); //deletes user row in the session
```

**Check login status**

```php
if($session->is_logged_in()){ //checks if a user row exists in the session
    echo "User is logged in";
}
```

**Get user data**

```php
$user = $session->user(); //get entire user row as object
$email = $session->user('email'); //get one column
```
