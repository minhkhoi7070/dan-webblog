import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import './interactions';
import './theme';
import { initFeedNavigation } from './feed-navigation';
import { initAppNavigation } from './app-navigation';
import { initProfileAvatar } from './profile-avatar';
import { initProfileSettings } from './profile-settings';
import { initSecuritySettings } from './security-settings';
import { initAdminUsersTableScroll } from './admin-users-table-scroll';
import { initAdminPostsTableScroll } from './admin-posts-table-scroll';
import { initAdminCommentsTableScroll } from './admin-comments-table-scroll';
import { initTabletSidebar } from './tablet-sidebar';

document.addEventListener('DOMContentLoaded', async () => {
    initAppNavigation();
    initFeedNavigation();
    initProfileAvatar();
    initProfileSettings();
    initSecuritySettings();
    initAdminUsersTableScroll();
    initAdminPostsTableScroll();
    initAdminCommentsTableScroll();
    initTabletSidebar();
    if (document.getElementById('tiptap-editor')) {
        const { initPostEditor } = await import('./editor');
        initPostEditor();
    }
});
