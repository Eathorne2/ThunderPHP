# Load Code Asset

Selects an existing architecture **Code Asset** node and emits its generated CSS or JavaScript include at this exact position in the View Builder hierarchy.

- The selector lists every `looks.text_asset` node in the architecture graph.
- CSS assets generate a stylesheet `<link>`.
- JavaScript assets generate a `<script src="...">` tag.
- The generated URL points to the selected asset's connected Look folder, so assets from any Look can be selected explicitly.
- A request-scoped URL guard prevents the same asset from being emitted more than once when several Views or reusable partials load it.
- Set **Reusable component name** on a root node to place the loader through a normal `<tvi-component>` marker.
- This utility node does not cause the shared View Builder component stylesheet to load by itself.
