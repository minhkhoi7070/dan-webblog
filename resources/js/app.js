import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import './interactions';
import './theme';
import { initPostEditor } from './editor';

document.addEventListener('DOMContentLoaded', () => {
    initPostEditor();
});
