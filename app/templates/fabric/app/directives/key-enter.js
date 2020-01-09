/* ============================================================
 * Directive: Keypress
 * Enter key closes the modal.
 * use : key-enter="<modal name>" on input elements in popups
 * ============================================================ */

angular.module('jaws')
    .directive('keyEnter', function () {
        return {
            restrict: 'EA',
            link: function(scope, element, attrs) {
                element.bind("keydown keypress", function (event) {
                    let key = typeof event.which === "undefined" ? event.keyCode : event.which;
                    if (key === 13) { $("#" + attrs.keyEnter).modal('toggle'); event.preventDefault(); }
                });
            }
        }
    });