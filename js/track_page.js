import { trackPage, getEndpoint } from './history.js'

document.addEventListener('DOMContentLoaded', _ => {
    trackPage(document.title, getEndpoint());
});
