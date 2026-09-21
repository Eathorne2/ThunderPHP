# Variable / Constant

Outputs either an in-scope PHP variable or a PHP constant.

- **variable** emits values such as `$user` or `$items`.
- **constant** emits global constants such as `ROOT` and `PHP_VERSION`, namespaced constants such as `\App\VERSION`, and class constants such as `Config::NAME`, `self::STATUS`, `static::TYPE`, or `parent::VALUE`.

The optional path can still access object properties or array keys after either source.
