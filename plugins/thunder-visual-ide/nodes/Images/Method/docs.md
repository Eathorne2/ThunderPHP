# Image Method

Calls a method on a connected `\Core\Image` instance. Selecting a method changes the input ports to match its documented signature.

Included defaults cover:

- resize, crop and contain
- format conversion and WEBP output
- single and multiple thumbnail creation
- upload and binary-data storage
- generic processing
- image metadata
- browser output
- thumbnail deletion
- runtime quality, directory, naming and upscale configuration

Optional inputs use the ThunderPHP method defaults when left disconnected. The **Image** output passes the same processor to another Image Method node.

For complex arguments such as `bg_color`, thumbnail `specs`, or generic `options`, connect an Array node.

Open **Browse Methods…** in the inspector or double-click the node to search every Image method. The browser includes signatures, usage guidance, examples, return behavior, format notes, and thumbnail result structures directly from the bundled method metadata.
