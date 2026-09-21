# Reusable View

Creates a view partial inside a Look without registering a route or action hook.

Build its contents in the nested Reusable View Builder, then place it in any normal View using **Render Reusable View**. The partial shares normal page data and variables because it is included in the current View scope.

Connect it to the same Look used by the Views that render it. The partial uses that Look's shared form theme and color palette.


## JavaScript Flow

Use **Open JavaScript Flow** in the Inspector to create browser-side event, AJAX, DOM and value logic for this View. The JavaScript graph compiles to a separate Look asset and is loaded with `defer`.
