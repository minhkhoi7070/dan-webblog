import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import './interactions';
import './theme';
import { initFeedNavigation } from './feed-navigation';
import { initAppNavigation } from './app-navigation';
import { initProfileAvatar } from './profile-avatar';
import { initSecuritySettings } from './security-settings';
import { initAdminUsersTableScroll } from './admin-users-table-scroll';
import { initAdminPostsTableScroll } from './admin-posts-table-scroll';
import { initAdminCommentsTableScroll } from './admin-comments-table-scroll';

document.addEventListener('DOMContentLoaded', async () => {
    initAppNavigation();
    initFeedNavigation();
    initProfileAvatar();
    initSecuritySettings();
    initAdminUsersTableScroll();
    initAdminPostsTableScroll();
    initAdminCommentsTableScroll();
    if (document.getElementById('tiptap-editor')) {
        const { initPostEditor } = await import('./editor');
        initPostEditor();
    }
});
