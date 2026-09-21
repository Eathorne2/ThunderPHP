# Thunder Config

`thunder-config` is a ThunderPHP admin plugin for editing safe values in the root `config.php` file.

## Features

- Adds a Configuration link to the Thunder Admin sidebar.
- Edits a fixed allow-list of `define()` constants.
- Creates timestamped backups before writing `config.php`.
- Uploads `APP_LOGO` with AJAX upload progress.
- Resizes uploaded logos proportionally so the largest side is no more than 512px.
- Uses `manage-config` permission.

## Route

```text
/admin/thunder-config
```

Logo upload endpoint:

```text
/admin/thunder-config/upload-logo
```

## Editable Constants

- `USE_SESSIONS`
- `DEBUG`
- `APP_NAME`
- `APP_DESCRIPTION`
- `APP_LOGO`
- `LOCAL_ROOT`
- `REMOTE_ROOT`
- `LOCAL_DB_NAME`
- `LOCAL_DB_USER`
- `LOCAL_DB_PASSWORD`
- `LOCAL_DB_HOST`
- `LOCAL_DB_DRIVER`
- `LOCAL_DB_PORT`
- `REMOTE_DB_NAME`
- `REMOTE_DB_USER`
- `REMOTE_DB_PASSWORD`
- `REMOTE_DB_HOST`
- `REMOTE_DB_DRIVER`
- `REMOTE_DB_PORT`

## Config Path Override

By default, the plugin looks for `config.php` in `ROOTPATH`.

You can override the path with:

```php
add_filter('thunder_config_config_path', function($path){
    return ROOTPATH . 'config.php';
});
```

## Notes

Make sure `config.php` is writable by PHP if you want to save changes from the admin page.
