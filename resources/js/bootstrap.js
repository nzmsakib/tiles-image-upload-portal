// Compile bootstrap sass as css
import '../sass/app.scss';
// Bootstrap JS
import * as bootstrap from 'bootstrap';
// jQuery
import $ from 'jquery';
import jQuery from 'jquery';

try {
    window.bootstrap = bootstrap
    window.$ = $
    window.jQuery = jQuery
} catch (error) {
    
}
