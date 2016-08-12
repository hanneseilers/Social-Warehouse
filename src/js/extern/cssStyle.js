/**
 * Gets css style value
 * From: http://stackoverflow.com/questions/16965515/how-to-get-a-style-attribute-from-a-css-class-by-javascript-jquery
 * @param style		Style name, like background-color
 * @param selector	Selector, like css class
 * @param sheet		CSS stylesheet or null if to use all stylesheets
 * @returns			Value of style at selector in stylesheet, null otherwise
 */
function getStyleRuleValue(style, selector, sheet) {
    var sheets = typeof sheet !== 'undefined' ? [sheet] : document.styleSheets;
    for (var i = 0, l = sheets.length; i < l; i++) {
        var sheet = sheets[i];
        if( !sheet.cssRules ) { continue; }
        for (var j = 0, k = sheet.cssRules.length; j < k; j++) {
            var rule = sheet.cssRules[j];
            if (rule.selectorText && rule.selectorText.split(',').indexOf(selector) !== -1) {
                return rule.style[style];
            }
        }
    }
    return null;
}