# Tutorial library

The tutorial browser reads `index.json`, then loads each referenced tutorial folder.

To add a lesson:

1. Copy `../tutorial-package-template/` into a category folder here.
2. Edit the copied `tutorial.json`, `content.html`, and thumbnail.
3. Put optional resources in its `files/` directory.
4. Add its relative folder path to the `tutorials` array in `index.json`.
5. Reload `/thunder-ide/tutorials`.

See `../TUTORIALS.md` for the complete metadata, video, content, and companion-file format.
