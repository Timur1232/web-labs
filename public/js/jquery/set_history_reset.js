import { resetHistory } from '/public/js/jquery/history.js';

$(document).ready(function() {
    $('#reset-history').click(function() {
        resetHistory();
    });
});
