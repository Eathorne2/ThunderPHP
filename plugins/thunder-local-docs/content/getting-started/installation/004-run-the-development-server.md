---
title: "Run the Development Server"
slug: "run-the-development-server"
description: "Start ThunderPHP with the built-in development server and customize the host or port."
published: true
order: 4
keywords: ["serve", "development server", "localhost", "127.0.0.1", "port", "host", "thunder cli", "run"]
---

Open a terminal in the ThunderPHP project root.

## Start with the defaults

Run:

```bash
php thunder serve
```

This serves the application at:

```text
http://127.0.0.1:8000
```

Open that address in your browser.

## Use a custom host and port

Options can be written with an equals sign:

```bash
php thunder serve --host=0.0.0.0 --port=8080
```

Spaced options are also supported:

```bash
php thunder serve --host 127.0.0.1 --port 9000
```

Use `0.0.0.0` when the development server must listen on all available network interfaces. Use an appropriate firewall configuration and only expose a development server on a trusted network.

## View command help

Run:

```bash
php thunder serve --help
```

This displays the options supported by the `serve` command.

## Stop the server

Return to the terminal where the server is running and press:

```text
Ctrl+C
```

The built-in server is intended for local development and testing. Use a properly configured production web server when deploying a live application.
