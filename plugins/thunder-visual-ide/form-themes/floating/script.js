document.querySelectorAll('{{scope}} input:not([type="checkbox"]), {{scope}} textarea').forEach(function (control) {
    if (!control.getAttribute('placeholder')) control.setAttribute('placeholder', ' ');
});
