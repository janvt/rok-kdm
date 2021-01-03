import './styles/app.scss';

import './bootstrap';

const $ = require('jquery');

require('bootstrap');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});