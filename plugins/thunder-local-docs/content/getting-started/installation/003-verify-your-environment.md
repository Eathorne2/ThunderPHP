---
title: "Verify Your Environment"
slug: "verify-your-environment"
description: "Confirm that PHP and the required database extensions are available before starting ThunderPHP."
published: true
order: 3
keywords: ["verify", "php version", "php modules", "pdo", "pdo_mysql", "mysql", "environment", "terminal"]
---

Before starting the framework, verify that your terminal can find PHP.

## Check PHP

Run:

```bash
php -v
```

You should see the installed PHP version. If the command is not recognized, PHP is not installed or its folder has not been added to your system `PATH`.

## Check PHP extensions

List the enabled PHP modules:

```bash
php -m
```

Confirm that the list includes:

```text
PDO
pdo_mysql
```

These extensions allow ThunderPHP applications to connect to MySQL or MariaDB through PDO.

## Check the database server

Start MySQL or MariaDB before loading an application that requires a database.

When using XAMPP, start **MySQL** from the XAMPP Control Panel. Apache can remain stopped when you plan to use `php thunder serve`.

If the MySQL command-line client is installed and available in your `PATH`, you can also check it with:

```bash
mysql --version
```

The absence of the `mysql` terminal command does not necessarily mean the database server is unavailable. XAMPP users can manage the server from the XAMPP Control Panel or phpMyAdmin.
