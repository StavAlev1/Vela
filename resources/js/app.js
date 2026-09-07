

import Alpine from 'alpinejs';
import postSearch from './post-search';

window.Alpine = Alpine;
Alpine.data('postSearch', postSearch);

Alpine.start();
