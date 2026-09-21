# Loop

Repeats connected children. Inside them, use Variable Output paths such as `item.name`.

## CSS and JavaScript

CSS `<style>` blocks produced by connected children are moved before the generated loop. Executable JavaScript `<script>` blocks are moved after it. Identical blocks are deduplicated, so the item HTML repeats while its component assets are emitted once.

Non-JavaScript script blocks, such as `application/json` data payloads, remain inside the loop because they may contain item-specific data.

## Optional wrapper styling

By default, the compiler emits the loop and its repeated child HTML directly without adding a container. Entering **Wrapper CSS classes** or **Wrapper inline styles** creates one outer `div`. Responsive width classes are applied only when that wrapper exists.

For example, a class of `product-grid` generates markup similar to:

```html
<div class="thv-col-mobile-12 thv-col-small-12 thv-col-medium-12 thv-col-large-12 product-grid">
    <!-- repeated items -->
</div>
```
