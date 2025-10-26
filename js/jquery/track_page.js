import { trackPage, getEndpoint } from './history.js'

$(document).ready().on('DOMContentLoaded', function() {
    trackPage(this.title, getEndpoint());
});
