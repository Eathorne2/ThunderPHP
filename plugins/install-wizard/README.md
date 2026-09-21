# Install Wizard Plugin

A first-run ThunderPHP installation wizard that creates `config.php` when the file does not exist.

## Behavior

- Loads on `/install`.
- Uses `before_controller` at priority `1` to redirect visitors to `/install` when `config.php` is missing.
- Does not require database access except for the explicit connection test on Step 2.
- Uses `config-sample.php` as the required source file check, then writes a clean `config.php` using collected values.
- Saves an optional logo to `/assets/images/logo.jpg` and resizes it to max `512px` using GD.
- Redirects to `login` after installation using `redirect('login')`.
- Can disable itself by setting this plugin's `config.json` `active` value to `false`.

## Installation

Copy the `install-wizard` folder into your ThunderPHP `plugins` directory.

Make sure the main project directory contains:

```text
config-sample.php
assets/images/
```

The main project directory must be writable while the wizard creates `config.php`.

