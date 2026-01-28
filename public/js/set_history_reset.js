import { resetHistory } from './history.js';

$(document).ready(function() {
    $('#reset-history').click(function() {
        resetHistory();
    });
});
