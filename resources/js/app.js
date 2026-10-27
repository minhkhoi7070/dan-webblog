import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import './interactions';
import './theme';
import { initFeedNavigation } from './feed-navigation';
import { initAppNavigation } from './app-navigation';

document.addEventListener('DOMContentLoaded', async () => {
    initAppNavigation();
    initFeedNavigation();
    if (document.getElementById('tiptap-editor')) {
        const { initPostEditor } = await import('./editor');
        initPostEditor();
    }
});
