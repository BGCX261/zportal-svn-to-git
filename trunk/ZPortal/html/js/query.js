/* requires prototype.js */
/**
 * parses script query parameters
 */
function parseQuery() {
    var scripts = document.getElementsByTagName('script');
    var theScript = scripts[scripts.length - 1]; // gets the last script upon the current call
    var source = theScript.src;
    return source.parseQuery();
}