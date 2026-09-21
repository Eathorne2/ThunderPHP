# PHP Code

Executes custom PHP inside a controller or function flow.

You may write the editor content as a normal PHP block:

```php
<?php
$value = do_something();
```

The compiler removes one opening `<?php` tag when it is the first non-whitespace token. It also removes a final `?>` tag when present, because the generated controller or function is already inside PHP. PHP tags elsewhere in the code are left untouched.
