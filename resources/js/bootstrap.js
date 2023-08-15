/**
 * The axios HTTP library is used by a variety of first-party Laravel packages
 * like Inertia in order to make requests to the Laravel backend. This will
 * automatically handle sending the CSRF token via a header based on the
 * value of the "XSRF" token cookie sent with previous HTTP responses.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
