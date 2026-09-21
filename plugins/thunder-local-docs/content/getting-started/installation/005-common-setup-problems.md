---
title: "Common Setup Problems"
slug: "common-setup-problems"
description: "Resolve common PHP, port, working-directory, and database issues encountered during installation."
published: true
order: 5
keywords: ["troubleshooting", "php not recognized", "port in use", "database connection", "mysql", "working directory", "serve error"]
---

## `php` is not recognized

PHP is either not installed or its directory is not in your system `PATH`.

When using XAMPP on Windows, add this folder to `PATH`:

```text
C:\xampp\php
```

Close and reopen the terminal after changing `PATH`, then run:

```bash
php -v
```

## The `thunder` file cannot be found

Make sure the terminal is in the ThunderPHP project root.

You can inspect the current folder before running the command:

```bash
php thunder --help
```

If PHP reports that it cannot open the `thunder` file, change into the extracted or cloned ThunderPHP directory and try again.

## Port 8000 is already in use

Start the server on another port:

```bash
php thunder serve --port=8080
```

You can choose another unused local port when necessary.

## The page opens but a database error appears

Check that:

- MySQL or MariaDB is running.
- The application is using the correct database name, username, password, host, and driver.
- The `PDO` and `pdo_mysql` PHP extensions are enabled.
- The required plugin migrations have been run for the application.

The local development server and the database server are separate processes. Starting `php thunder serve` does not automatically start MySQL.

## Changes are not visible

Stop the development server with `Ctrl+C`, start it again, and refresh the browser. Also verify that you are editing the same ThunderPHP folder from which the server was started.
