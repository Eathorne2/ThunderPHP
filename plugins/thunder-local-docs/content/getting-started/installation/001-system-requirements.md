---
title: "System Requirements"
slug: "system-requirements"
description: "Prepare PHP, MySQL or MariaDB, and the command-line tools needed to run ThunderPHP locally."
published: true
order: 1
keywords: ["installation", "requirements", "php", "mysql", "mariadb", "xampp", "cli", "pdo_mysql"]
---

Before downloading ThunderPHP, prepare a local PHP development environment.

## Required software

You need:

- PHP available from the command line.
- MySQL or MariaDB for plugins and applications that use the database.
- The PHP PDO and PDO MySQL extensions.
- A web browser.
- Git, if you want to clone the repository. Git is optional when downloading a ZIP file.

## The easiest Windows setup: XAMPP

For many Windows users, XAMPP is the quickest way to install PHP and a MySQL-compatible database together.

After installing XAMPP:

1. Open the XAMPP Control Panel.
2. Start **MySQL**.
3. Make PHP available in your terminal by adding the XAMPP PHP folder to your system `PATH`.

The usual PHP folder is:

```text
C:\xampp\php
```

Alternatively, open the XAMPP Shell and run the ThunderPHP commands from there.

You do not need to start Apache when using ThunderPHP's built-in development server. The `php thunder serve` command starts the local HTTP server for you.

## macOS and Linux

Install PHP, PDO MySQL, and MySQL or MariaDB using your operating system's package manager or a local development package. The package names vary by operating system and PHP version.

After installation, confirm that the `php` command works in a terminal before continuing.
