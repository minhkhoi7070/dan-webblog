import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import './interactions';
import './theme';

document.addEventListener('DOMContentLoaded', () => {
    // Only load and initialize TipTap Editor on pages with #tiptap-editor
    if (document.getElementById('tiptap-editor')) {
        import('./editor').then(({ initPostEditor }) => {
            initPostEditor();
        }).catch((err) => {
            console.error('Failed to load post editor:', err);
        });
    }
});
