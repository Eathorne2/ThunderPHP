# Library Package

Bundles a PHP library folder into the generated plugin.

1. ZIP the complete library folder.
2. Select the ZIP in the node inspector.
3. Set a destination such as `libraries/mpdf`.
4. Enable autoloader loading and set the path relative to that destination.

The ZIP is uploaded to `storage/libraries/` inside Thunder Visual IDE, so large packages do not bloat the project JSON or normal build request.

For an mPDF installation that already contains Composer's vendor directory, the autoloader is commonly:

```text
vendor/autoload.php
```

The compiler extracts ordinary stored and deflated ZIP archives. Encrypted and Zip64 archives are rejected. If a project JSON is moved to another ThunderPHP installation, re-upload its library ZIP because the archive itself is stored server-side rather than inside the JSON.
