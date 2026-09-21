# Image Processor

Creates a configured `\Core\Image` object for use by **Image Method** nodes.

The node exposes common ThunderPHP defaults:

- JPEG quality
- PNG compression
- WEBP quality
- allow/prevent upscaling
- readable or hashed thumbnail names
- default thumbnail directory

Connect its **Image** output to one or more Image Method nodes.
