/*-----------------------------------------
 | Third-party libraries
 | ----------------------------------------
 */
window.$ = window.jQuery = require('jquery');
window._ = require('underscore');
// Require bootstrap like some sort of hobo because for some reason they don't support module imports...
var bootstrap = require('../../../../node_modules/bootstrap-sass/assets/javascripts/bootstrap');

/*-----------------------------------------
 | Our modules
 | ----------------------------------------
 */
require('./components/component');
