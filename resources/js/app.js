import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import './interactions';
import './theme';
import { initFeedNavigation } from './feed-navigation';
import { initAppNavigation } from './app-navigation';
import { initProfileAvatar } from './profile-avatar';
import { initSecuritySettings } from './security-settings';

document.addEventListener('DOMContentLoaded', async () => {
    initAppNavigation();
    initFeedNavigation();
    initProfileAvatar();
    initSecuritySettings();
    if (document.getElementById('tiptap-editor')) {
        const { initPostEditor } = await import('./editor');
        initPostEditor();
    }
});
