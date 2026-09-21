# AJAX Request

Uses `XMLHttpRequest` so upload progress is available. Callback outputs create separate JS execution branches. Data outputs are valid inside those branches.

## XHR variable

Set **XHR variable name** to the JavaScript identifier you want available inside the request and every callback branch. It defaults to `xhr`. The compiler validates the identifier and gives every AJAX Request its own block scope, so separate requests may safely use the same variable name.
