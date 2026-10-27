import { Editor, Extension, Mark, mergeAttributes } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';

// ============================================================================
// CUSTOM TIPTAP EXTENSIONS FOR WORDPRESS/TINYMCE-STYLE EDITING
// ============================================================================

/**
 * Text Color Mark: applies inline style color: ... to selected text.
 * Completely independent from background highlight color.
 */
export const TextColor = Mark.create({
    name: 'textColor',
    addAttributes() {
        return {
            color: {
                default: null,
                parseHTML: (element) => element.style.color || null,
                renderHTML: (attributes) => {
                    if (!attributes.color) return {};
                    return { style: `color: ${attributes.color}` };
                },
            },
        };
    },
    parseHTML() {
        return [
            {
                tag: 'span',
                getAttrs: (element) => {
                    const color = element.style.color;
                    return color ? { color } : false;
                },
            },
        ];
    },
    renderHTML({ HTMLAttributes }) {
        return ['span', mergeAttributes(HTMLAttributes), 0];
    },
    addCommands() {
        return {
            setTextColor: (color) => ({ chain }) => {
                return chain().setMark(this.name, { color }).run();
            },
            unsetTextColor: () => ({ chain }) => {
                return chain().unsetMark(this.name).run();
            },
        };
    },
});

/**
 * Text Highlight Mark: applies background-color and data-color to text.
 * Does NOT set or override text color, allowing text color and highlight to coexist independently.
 */
export const TextHighlight = Mark.create({
    name: 'textHighlight',
    addAttributes() {
        return {
            color: {
                default: '#fef08a',
                parseHTML: (element) => element.getAttribute('data-color') || element.style.backgroundColor || null,
                renderHTML: (attributes) => {
                    if (!attributes.color) return {};
                    return {
                        'data-color': attributes.color,
                        style: `background-color: ${attributes.color}`,
                    };
                },
            },
        };
    },
    parseHTML() {
        return [
            {
                tag: 'mark',
                getAttrs: (element) => {
                    const color = element.getAttribute('data-color') || element.style.backgroundColor;
                    return { color: color || '#fef08a' };
                },
            },
            {
                tag: '[data-color]',
                getAttrs: (element) => ({ color: element.getAttribute('data-color') }),
            },
            {
                tag: 'span',
                getAttrs: (element) => {
                    const bg = element.style.backgroundColor;
                    return bg ? { color: bg } : false;
                },
            },
        ];
    },
    renderHTML({ HTMLAttributes }) {
        return ['mark', mergeAttributes(HTMLAttributes), 0];
    },
    addCommands() {
        return {
            setHighlight: (color) => ({ chain }) => {
                return chain().setMark(this.name, { color }).run();
            },
            toggleHighlight: (color = '#fef08a') => ({ chain }) => {
                return chain().toggleMark(this.name, { color }).run();
            },
            unsetHighlight: () => ({ chain }) => {
                return chain().unsetMark(this.name).run();
            },
        };
    },
});

/**
 * Paragraph & Heading Text Alignment Extension.
 */
export const CustomTextAlign = Extension.create({
    name: 'textAlign',
    addOptions() {
        return {
            types: ['heading', 'paragraph'],
            alignments: ['left', 'center', 'right', 'justify'],
            defaultAlignment: 'left',
        };
    },
    addGlobalAttributes() {
        return [
            {
                types: this.options.types,
                attributes: {
                    textAlign: {
                        default: this.options.defaultAlignment,
                        parseHTML: (element) => element.style.textAlign || this.options.defaultAlignment,
                        renderHTML: (attributes) => {
                            if (!attributes.textAlign || attributes.textAlign === this.options.defaultAlignment) {
                                return {};
                            }
                            return { style: `text-align: ${attributes.textAlign}` };
                        },
                    },
                },
            },
        ];
    },
    addCommands() {
        return {
            setTextAlign: (alignment) => ({ commands }) => {
                if (!this.options.alignments.includes(alignment)) return false;
                return this.options.types.some((type) => commands.updateAttributes(type, { textAlign: alignment }));
            },
            unsetTextAlign: () => ({ commands }) => {
                return this.options.types.some((type) => commands.resetAttributes(type, 'textAlign'));
            },
        };
    },
});

/**
 * Custom Indent / Outdent Extension: works seamlessly for both list items and blocks.
 */
export const CustomIndent = Extension.create({
    name: 'indent',
    addOptions() {
        return {
            types: ['paragraph', 'heading', 'blockquote'],
            minLevel: 0,
            maxLevel: 6,
        };
    },
    addGlobalAttributes() {
        return [
            {
                types: this.options.types,
                attributes: {
                    indent: {
                        default: 0,
                        parseHTML: (element) => {
                            const indentAttr = element.getAttribute('data-indent');
                            if (indentAttr) return parseInt(indentAttr, 10) || 0;
                            const paddingLeft = element.style.paddingLeft || element.style.marginLeft;
                            if (paddingLeft && paddingLeft.includes('rem')) {
                                return Math.min(6, Math.max(0, parseInt(paddingLeft, 10) || 0));
                            }
                            return 0;
                        },
                        renderHTML: (attributes) => {
                            if (!attributes.indent || attributes.indent <= 0) return {};
                            return {
                                'data-indent': attributes.indent,
                                style: `padding-left: ${attributes.indent * 2}rem`,
                            };
                        },
                    },
                },
            },
        ];
    },
    addCommands() {
        return {
            indent: () => ({ editor, tr, state, dispatch }) => {
                const { selection } = state;
                const { from, to } = selection;
                const isList = editor.isActive('listItem');
                if (isList) {
                    return editor.chain().focus().sinkListItem('listItem').run();
                }
                state.doc.nodesBetween(from, to, (node, pos) => {
                    if (this.options.types.includes(node.type.name)) {
                        const currentIndent = node.attrs.indent || 0;
                        if (currentIndent < this.options.maxLevel) {
                            tr = tr.setNodeMarkup(pos, undefined, {
                                ...node.attrs,
                                indent: currentIndent + 1,
                            });
                        }
                    }
                });
                if (dispatch) dispatch(tr);
                return true;
            },
            outdent: () => ({ editor, tr, state, dispatch }) => {
                const { selection } = state;
                const { from, to } = selection;
                const isList = editor.isActive('listItem');
                if (isList) {
                    return editor.chain().focus().liftListItem('listItem').run();
                }
                state.doc.nodesBetween(from, to, (node, pos) => {
                    if (this.options.types.includes(node.type.name)) {
                        const currentIndent = node.attrs.indent || 0;
                        if (currentIndent > this.options.minLevel) {
                            tr = tr.setNodeMarkup(pos, undefined, {
                                ...node.attrs,
                                indent: currentIndent - 1,
                            });
                        }
                    }
                });
                if (dispatch) dispatch(tr);
                return true;
            },
        };
    },
});

/**
 * Custom Shortcuts Extension: binds OS-aware shortcuts strictly when editor is focused.
 */
export const CustomShortcuts = Extension.create({
    name: 'customShortcuts',
    addKeyboardShortcuts() {
        return {
            'Mod-k': () => {
                openLinkModal(this.editor);
                return true;
            },
            // Lists
            'Mod-Shift-8': () => this.editor.commands.toggleBulletList(),
            'Mod-Shift-7': () => this.editor.commands.toggleOrderedList(),
            // Headings 1 to 6
            'Mod-Alt-1': () => this.editor.commands.toggleHeading({ level: 1 }),
            'Mod-Alt-2': () => this.editor.commands.toggleHeading({ level: 2 }),
            'Mod-Alt-3': () => this.editor.commands.toggleHeading({ level: 3 }),
            'Mod-Alt-4': () => this.editor.commands.toggleHeading({ level: 4 }),
            'Mod-Alt-5': () => this.editor.commands.toggleHeading({ level: 5 }),
            'Mod-Alt-6': () => this.editor.commands.toggleHeading({ level: 6 }),
            // Windows / Linux Ctrl+Alt mappings
            'Ctrl-Alt-1': () => this.editor.commands.toggleHeading({ level: 1 }),
            'Ctrl-Alt-2': () => this.editor.commands.toggleHeading({ level: 2 }),
            'Ctrl-Alt-3': () => this.editor.commands.toggleHeading({ level: 3 }),
            'Ctrl-Alt-4': () => this.editor.commands.toggleHeading({ level: 4 }),
            'Ctrl-Alt-5': () => this.editor.commands.toggleHeading({ level: 5 }),
            'Ctrl-Alt-6': () => this.editor.commands.toggleHeading({ level: 6 }),
            // Paragraph (Ctrl+Alt+7)
            'Mod-Alt-7': () => this.editor.commands.setParagraph(),
            'Ctrl-Alt-7': () => this.editor.commands.setParagraph(),
            // Blockquote (Ctrl+Alt+Q)
            'Mod-Alt-q': () => this.editor.commands.toggleBlockquote(),
            'Ctrl-Alt-q': () => this.editor.commands.toggleBlockquote(),
            // Strikethrough
            'Mod-Alt-d': () => this.editor.commands.toggleStrike(),
            'Ctrl-Alt-d': () => this.editor.commands.toggleStrike(),
            'Mod-Shift-x': () => this.editor.commands.toggleStrike(),
            // Alignment
            'Mod-Alt-l': () => {
                if (this.editor.isActive('image')) {
                    return this.editor.commands.updateAttributes('image', { alignment: 'left' });
                }
                return this.editor.commands.setTextAlign('left');
            },
            'Ctrl-Alt-l': () => {
                if (this.editor.isActive('image')) {
                    return this.editor.commands.updateAttributes('image', { alignment: 'left' });
                }
                return this.editor.commands.setTextAlign('left');
            },
            'Mod-Alt-e': () => {
                if (this.editor.isActive('image')) {
                    return this.editor.commands.updateAttributes('image', { alignment: 'center' });
                }
                return this.editor.commands.setTextAlign('center');
            },
            'Ctrl-Alt-e': () => {
                if (this.editor.isActive('image')) {
                    return this.editor.commands.updateAttributes('image', { alignment: 'center' });
                }
                return this.editor.commands.setTextAlign('center');
            },
            'Mod-Alt-r': () => {
                if (this.editor.isActive('image')) {
                    return this.editor.commands.updateAttributes('image', { alignment: 'right' });
                }
                return this.editor.commands.setTextAlign('right');
            },
            'Ctrl-Alt-r': () => {
                if (this.editor.isActive('image')) {
                    return this.editor.commands.updateAttributes('image', { alignment: 'right' });
                }
                return this.editor.commands.setTextAlign('right');
            },
            'Mod-Alt-j': () => this.editor.commands.setTextAlign('justify'),
            'Ctrl-Alt-j': () => this.editor.commands.setTextAlign('justify'),
        };
    },
});

/**
 * Custom Image Extension supporting Alignment & Caption (Figure/Figcaption HTML5)
 */
export const CustomImage = Image.extend({
    name: 'image',

    addAttributes() {
        return {
            ...this.parent?.(),
            alignment: {
                default: 'center',
                parseHTML: (element) => {
                    const figure = element.closest('figure');
                    if (figure && figure.getAttribute('data-align')) {
                        return figure.getAttribute('data-align');
                    }
                    if (element.getAttribute('data-align')) {
                        return element.getAttribute('data-align');
                    }
                    if (element.classList.contains('align-left') || (figure && figure.classList.contains('align-left'))) {
                        return 'left';
                    }
                    if (element.classList.contains('align-right') || (figure && figure.classList.contains('align-right'))) {
                        return 'right';
                    }
                    return 'center';
                },
                renderHTML: (attributes) => {
                    const align = attributes.alignment || 'center';
                    return {
                        'data-align': align,
                        class: `align-${align}`,
                    };
                },
            },
            caption: {
                default: null,
                parseHTML: (element) => {
                    const figure = element.closest('figure');
                    if (figure) {
                        const figcaption = figure.querySelector('figcaption');
                        if (figcaption) {
                            return figcaption.textContent?.trim() || null;
                        }
                        if (figure.getAttribute('data-caption')) {
                            return figure.getAttribute('data-caption');
                        }
                    }
                    return element.getAttribute('data-caption') || null;
                },
                renderHTML: (attributes) => {
                    if (!attributes.caption) {
                        return {};
                    }
                    return {
                        'data-caption': attributes.caption,
                    };
                },
            },
        };
    },

    parseHTML() {
        return [
            {
                tag: 'figure.editor-image-figure',
                getAttrs: (element) => {
                    const img = element.querySelector('img');
                    if (!img) return false;
                    const figcaption = element.querySelector('figcaption');
                    let align = element.getAttribute('data-align');
                    if (!align) {
                        if (element.classList.contains('align-left')) align = 'left';
                        else if (element.classList.contains('align-right')) align = 'right';
                        else align = 'center';
                    }
                    return {
                        src: img.getAttribute('src'),
                        alt: img.getAttribute('alt'),
                        title: img.getAttribute('title'),
                        alignment: align || 'center',
                        caption: figcaption ? figcaption.textContent?.trim() : null,
                    };
                },
            },
            {
                tag: 'figure',
                getAttrs: (element) => {
                    const img = element.querySelector('img');
                    if (!img) return false;
                    const figcaption = element.querySelector('figcaption');
                    let align = element.getAttribute('data-align');
                    if (!align) {
                        if (element.classList.contains('align-left')) align = 'left';
                        else if (element.classList.contains('align-right')) align = 'right';
                        else align = 'center';
                    }
                    return {
                        src: img.getAttribute('src'),
                        alt: img.getAttribute('alt'),
                        title: img.getAttribute('title'),
                        alignment: align || 'center',
                        caption: figcaption ? figcaption.textContent?.trim() : null,
                    };
                },
            },
            {
                tag: 'img[src]',
                getAttrs: (element) => {
                    let align = element.getAttribute('data-align');
                    if (!align) {
                        if (element.classList.contains('align-left')) align = 'left';
                        else if (element.classList.contains('align-right')) align = 'right';
                        else align = 'center';
                    }
                    return {
                        src: element.getAttribute('src'),
                        alt: element.getAttribute('alt'),
                        title: element.getAttribute('title'),
                        alignment: align || 'center',
                        caption: element.getAttribute('data-caption') || null,
                    };
                },
            },
        ];
    },

    renderHTML({ HTMLAttributes }) {
        const align = HTMLAttributes['data-align'] || HTMLAttributes.alignment || 'center';
        const caption = HTMLAttributes['data-caption'] || HTMLAttributes.caption || null;

        const imgAttrs = {
            src: HTMLAttributes.src,
            alt: HTMLAttributes.alt || '',
            title: HTMLAttributes.title || '',
            loading: 'lazy',
            class: `editor-content-image align-${align}`,
            'data-align': align,
        };
        if (caption) {
            imgAttrs['data-caption'] = caption;
            return [
                'figure',
                {
                    class: `editor-image-figure align-${align}`,
                    'data-align': align,
                    'data-caption': caption,
                },
                ['img', imgAttrs],
                ['figcaption', { class: 'editor-image-caption' }, caption],
            ];
        }

        return [
            'figure',
            {
                class: `editor-image-figure align-${align}`,
                'data-align': align,
            },
            ['img', imgAttrs],
        ];
    },
});

// ============================================================================
// COLOR CONTRAST CALCULATION (WCAG 2.0 GUIDELINE HINT)
// ============================================================================

function parseColorToRgb(colorStr) {
    if (!colorStr) return null;
    colorStr = colorStr.trim().toLowerCase();
    if (colorStr.startsWith('#')) {
        let hex = colorStr.slice(1);
        if (hex.length === 3) {
            hex = hex.split('').map((c) => c + c).join('');
        }
        if (hex.length === 6) {
            return {
                r: parseInt(hex.substring(0, 2), 16),
                g: parseInt(hex.substring(2, 4), 16),
                b: parseInt(hex.substring(4, 6), 16),
            };
        }
    }
    const rgbMatch = colorStr.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
    if (rgbMatch) {
        return {
            r: parseInt(rgbMatch[1], 10),
            g: parseInt(rgbMatch[2], 10),
            b: parseInt(rgbMatch[3], 10),
        };
    }
    const named = {
        white: { r: 255, g: 255, b: 255 },
        black: { r: 0, g: 0, b: 0 },
        red: { r: 239, g: 68, b: 68 },
        orange: { r: 249, g: 115, b: 22 },
        yellow: { r: 234, g: 179, b: 8 },
        green: { r: 16, g: 185, b: 129 },
        blue: { r: 59, g: 130, b: 246 },
        purple: { r: 139, g: 92, b: 246 },
    };
    return named[colorStr] || null;
}

function calculateLuminance(rgb) {
    const a = [rgb.r, rgb.g, rgb.b].map((v) => {
        v /= 255;
        return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
    });
    return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
}

function checkContrastWarning(textColor, bgColor) {
    const rgbText = parseColorToRgb(textColor);
    const rgbBg = parseColorToRgb(bgColor);
    if (!rgbText || !rgbBg) return;

    const lumText = calculateLuminance(rgbText);
    const lumBg = calculateLuminance(rgbBg);
    const brighter = Math.max(lumText, lumBg);
    const darker = Math.min(lumText, lumBg);
    const ratio = (brighter + 0.05) / (darker + 0.05);

    if (ratio < 2.5) {
        showEditorToast('Gợi ý: Độ tương phản giữa màu chữ và màu nền đang chọn khá thấp, có thể gây khó đọc.', 'warning');
    }
}

// ============================================================================
// MODULE LIFECYCLE & STATE
// ============================================================================

let currentEditor = null;
let isDirty = false;
let isSubmitting = false;
let cleanupHandlers = [];
let floatingImageToolbarEl = null;

function registerCleanup(fn) {
    if (typeof fn === 'function') {
        cleanupHandlers.push(fn);
    }
}

function registerListener(target, type, handler, options) {
    if (!target) return;
    target.addEventListener(type, handler, options);
    registerCleanup(() => {
        target.removeEventListener(type, handler, options);
    });
}

export function isPostEditorDirty() {
    return Boolean(isDirty && !isSubmitting);
}

export function setPostEditorDirty(val = true) {
    isDirty = Boolean(val);
    if (typeof window !== 'undefined') {
        window.__postEditorIsDirty = isDirty;
    }
}

export function destroyPostEditor() {
    if (currentEditor) {
        try {
            currentEditor.destroy();
        } catch (e) {
            console.warn('TipTap destroy error:', e);
        }
        currentEditor = null;
    }

    cleanupHandlers.forEach((cleanup) => {
        try {
            cleanup();
        } catch (e) {
            console.warn('Cleanup handler error:', e);
        }
    });
    cleanupHandlers = [];

    if (floatingImageToolbarEl) {
        floatingImageToolbarEl.classList.add('d-none');
        floatingImageToolbarEl.remove();
        floatingImageToolbarEl = null;
    }

    // Clean up portaled modals attached directly to document.body
    if (typeof document !== 'undefined') {
        ['editor-link-modal', 'editor-image-modal', 'editor-shortcuts-modal', 'thumbnail-remove-confirm-modal'].forEach((id) => {
            if (typeof document.getElementById !== 'function') return;
            const modalEl = document.getElementById(id);
            if (modalEl && document.body && modalEl.parentElement === document.body) {
                if (typeof window !== 'undefined' && window.bootstrap?.Modal) {
                    const inst = window.bootstrap.Modal.getInstance(modalEl);
                    if (inst) inst.dispose();
                }
                modalEl.remove();
            }
        });

        if (typeof document.querySelectorAll === 'function') {
            document.querySelectorAll('.modal-backdrop').forEach((el) => el.remove());
        }
        if (document.body) {
            document.body.classList?.remove?.('modal-open');
            document.body.style?.removeProperty?.('overflow');
            document.body.style?.removeProperty?.('padding-right');
        }
    }

    isDirty = false;
    isSubmitting = false;
    if (typeof window !== 'undefined') {
        window.__postEditorIsDirty = false;
    }
}

// Global exposure for Seamless Router & Interop
if (typeof window !== 'undefined') {
    window.destroyPostEditor = destroyPostEditor;
    window.isPostEditorDirty = isPostEditorDirty;
    window.setPostEditorDirty = setPostEditorDirty;
    window.__postEditorSetDirty = () => setPostEditorDirty(true);
}

// ============================================================================
// MAIN EDITOR INITIALIZATION
// ============================================================================

export function initPostEditor(root = document) {
    const editorElement = (root && root.querySelector)
        ? (root.querySelector('#tiptap-editor') || document.getElementById('tiptap-editor'))
        : document.getElementById('tiptap-editor');

    const textarea = (root && root.querySelector)
        ? (root.querySelector('#content') || document.getElementById('content'))
        : document.getElementById('content');

    if (!editorElement || !textarea) return null;

    // Teardown previous editor if re-entering or re-hydrating
    if (currentEditor) {
        destroyPostEditor();
    }

    isDirty = false;
    isSubmitting = false;
    editorElement.innerHTML = '';

    const initialContent = textarea.value || '';

    const editor = new Editor({
        element: editorElement,
        extensions: [
            StarterKit.configure({
                heading: {
                    levels: [1, 2, 3, 4, 5, 6],
                },
                dropcursor: {
                    color: '#3b82f6',
                    width: 2,
                },
            }),
            Underline,
            TextColor,
            TextHighlight,
            CustomTextAlign,
            CustomIndent,
            CustomShortcuts,
            Link.configure({
                openOnClick: false,
                HTMLAttributes: {
                    class: 'editor-content-link text-decoration-underline',
                    target: '_blank',
                    rel: 'noopener noreferrer',
                },
                validate: (href) => /^(https?:\/\/|\/|#|mailto:)/i.test(href),
            }),
            CustomImage.configure({
                inline: false,
                allowBase64: false,
            }),
        ],
        content: initialContent,
        editorProps: {
            attributes: {
                class: 'editor-wysiwyg-surface p-3',
                spellcheck: 'false',
            },
            transformPastedHTML(html) {
                try {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    doc.querySelectorAll('script, iframe, object, embed, style').forEach((el) => el.remove());
                    doc.querySelectorAll('*').forEach((el) => {
                        for (let i = el.attributes.length - 1; i >= 0; i--) {
                            const attr = el.attributes[i];
                            if (attr.name.toLowerCase().startsWith('on')) {
                                el.removeAttribute(attr.name);
                            }
                        }
                    });
                    return doc.body.innerHTML;
                } catch {
                    return html;
                }
            },
        },
        onUpdate: ({ editor }) => {
            textarea.value = editor.getHTML();
            setPostEditorDirty(true);
            updateEditorStats(editor);
            updateToolbarState(editor);
            updateFloatingImageToolbar(editor);
        },
        onSelectionUpdate: ({ editor }) => {
            updateToolbarState(editor);
            updateFloatingImageToolbar(editor);
        },
    });

    currentEditor = editor;

    setupToolbar(editor, root);
    setupLinkModal(editor, root);
    setupImageAltModal(editor, root);
    setupFloatingImageToolbar(editor);
    setupShortcutsModal(root);
    setupImageUpload(editor, root);
    setupThumbnailUploader(root);
    setupTagSelector(root);
    setupFormHandling(editor, root);

    updateEditorStats(editor);
    updateToolbarState(editor);

    if (typeof window !== 'undefined') {
        window.initPostEditor = initPostEditor;
    }

    return editor;
}

// ============================================================================
// STATS & TOOLBAR STATE
// ============================================================================

function updateEditorStats(editor) {
    if (!editor) return;
    const text = editor.getText().trim();
    const words = text ? text.split(/\s+/).filter(Boolean).length : 0;
    const chars = text.length;
    const minutes = Math.max(1, Math.ceil(words / 200));

    const wordsEl = document.getElementById('stat-words');
    const charsEl = document.getElementById('stat-chars');
    const readingEl = document.getElementById('stat-reading');

    if (wordsEl) wordsEl.textContent = words.toLocaleString() + ' từ';
    if (charsEl) charsEl.textContent = chars.toLocaleString() + ' ký tự';
    if (readingEl) readingEl.textContent = '~' + minutes + ' phút đọc';
}

function updateToolbarState(editor) {
    if (!editor) return;

    let currentLabel = 'Đoạn văn';
    let currentBlock = 'p';

    if (editor.isActive('heading', { level: 1 })) {
        currentLabel = 'Tiêu đề 1';
        currentBlock = 'h1';
    } else if (editor.isActive('heading', { level: 2 })) {
        currentLabel = 'Tiêu đề 2';
        currentBlock = 'h2';
    } else if (editor.isActive('heading', { level: 3 })) {
        currentLabel = 'Tiêu đề 3';
        currentBlock = 'h3';
    } else if (editor.isActive('heading', { level: 4 })) {
        currentLabel = 'Tiêu đề 4';
        currentBlock = 'h4';
    } else if (editor.isActive('heading', { level: 5 })) {
        currentLabel = 'Tiêu đề 5';
        currentBlock = 'h5';
    } else if (editor.isActive('heading', { level: 6 })) {
        currentLabel = 'Tiêu đề 6';
        currentBlock = 'h6';
    } else if (editor.isActive('blockquote')) {
        currentLabel = 'Trích dẫn';
        currentBlock = 'quote';
    }

    const currentBlockLabelEl = document.getElementById('editor-current-block-label');
    if (currentBlockLabelEl) {
        currentBlockLabelEl.textContent = currentLabel;
    }

    document.querySelectorAll('[data-editor-block]').forEach((item) => {
        const blockType = item.getAttribute('data-editor-block');
        item.classList.toggle('active', blockType === currentBlock);
    });

    const actionMap = {
        bold: () => editor.isActive('bold'),
        italic: () => editor.isActive('italic'),
        underline: () => editor.isActive('underline'),
        strike: () => editor.isActive('strike'),
        quote: () => editor.isActive('blockquote'),
        code: () => editor.isActive('codeBlock'),
        'bullet-list': () => editor.isActive('bulletList'),
        'ordered-list': () => editor.isActive('orderedList'),
        link: () => editor.isActive('link'),
        'align-left': () => editor.isActive('image') ? (editor.getAttributes('image').alignment === 'left') : editor.isActive({ textAlign: 'left' }),
        'align-center': () => editor.isActive('image') ? ((editor.getAttributes('image').alignment || 'center') === 'center') : editor.isActive({ textAlign: 'center' }),
        'align-right': () => editor.isActive('image') ? (editor.getAttributes('image').alignment === 'right') : editor.isActive({ textAlign: 'right' }),
        'align-justify': () => editor.isActive('image') ? false : editor.isActive({ textAlign: 'justify' }),
    };

    document.querySelectorAll('[data-editor-action]').forEach((btn) => {
        const action = btn.getAttribute('data-editor-action');
        const checkFn = actionMap[action];
        if (checkFn) {
            btn.classList.toggle('active', Boolean(checkFn()));
        }

        if (action === 'undo') {
            const canUndo = editor.can().undo();
            btn.classList.toggle('opacity-50', !canUndo);
            btn.style.pointerEvents = canUndo ? 'auto' : 'none';
        } else if (action === 'redo') {
            const canRedo = editor.can().redo();
            btn.classList.toggle('opacity-50', !canRedo);
            btn.style.pointerEvents = canRedo ? 'auto' : 'none';
        }
    });

    const indentBtn = document.querySelector('[data-editor-action="indent"]');
    const outdentBtn = document.querySelector('[data-editor-action="outdent"]');

    if (indentBtn) {
        if (editor.isActive('listItem')) {
            const canSink = editor.can().sinkListItem('listItem');
            indentBtn.classList.toggle('opacity-50', !canSink);
            indentBtn.style.pointerEvents = canSink ? 'auto' : 'none';
        } else {
            indentBtn.classList.remove('opacity-50');
            indentBtn.style.pointerEvents = 'auto';
        }
    }

    if (outdentBtn) {
        if (editor.isActive('listItem')) {
            const canLift = editor.can().liftListItem('listItem');
            outdentBtn.classList.toggle('opacity-50', !canLift);
            outdentBtn.style.pointerEvents = canLift ? 'auto' : 'none';
        } else {
            outdentBtn.classList.remove('opacity-50');
            outdentBtn.style.pointerEvents = 'auto';
        }
    }
}

// ============================================================================
// TOOLBAR SETUP
// ============================================================================

function setupToolbar(editor, root) {
    const scope = root && root.querySelectorAll ? root : document;

    // 1. Heading & Paragraph block dropdown
    scope.querySelectorAll('[data-editor-block]').forEach((btn) => {
        registerListener(btn, 'click', (e) => {
            e.preventDefault();
            const blockType = btn.getAttribute('data-editor-block');

            switch (blockType) {
                case 'p':
                    editor.chain().focus().setParagraph().run();
                    break;
                case 'h1':
                    editor.chain().focus().toggleHeading({ level: 1 }).run();
                    break;
                case 'h2':
                    editor.chain().focus().toggleHeading({ level: 2 }).run();
                    break;
                case 'h3':
                    editor.chain().focus().toggleHeading({ level: 3 }).run();
                    break;
                case 'h4':
                    editor.chain().focus().toggleHeading({ level: 4 }).run();
                    break;
                case 'h5':
                    editor.chain().focus().toggleHeading({ level: 5 }).run();
                    break;
                case 'h6':
                    editor.chain().focus().toggleHeading({ level: 6 }).run();
                    break;
                case 'quote':
                    editor.chain().focus().toggleBlockquote().run();
                    break;
            }
            updateToolbarState(editor);
        });
    });

    // 2. Toolbar action buttons
    scope.querySelectorAll('[data-editor-action]').forEach((btn) => {
        registerListener(btn, 'click', (e) => {
            e.preventDefault();
            const action = btn.getAttribute('data-editor-action');

            switch (action) {
                case 'bold':
                    editor.chain().focus().toggleBold().run();
                    break;
                case 'italic':
                    editor.chain().focus().toggleItalic().run();
                    break;
                case 'underline':
                    editor.chain().focus().toggleUnderline().run();
                    break;
                case 'strike':
                    editor.chain().focus().toggleStrike().run();
                    break;
                case 'clear-format':
                    editor.chain().focus().unsetAllMarks().run();
                    showEditorToast('Đã xóa toàn bộ định dạng ký tự vùng chọn.', 'info');
                    break;
                case 'quote':
                    editor.chain().focus().toggleBlockquote().run();
                    break;
                case 'code':
                    editor.chain().focus().toggleCodeBlock().run();
                    break;
                case 'bullet-list':
                    editor.chain().focus().toggleBulletList().run();
                    break;
                case 'ordered-list':
                    editor.chain().focus().toggleOrderedList().run();
                    break;
                case 'align-left':
                    if (editor.isActive('image')) {
                        editor.chain().focus().updateAttributes('image', { alignment: 'left' }).run();
                    } else {
                        editor.chain().focus().setTextAlign('left').run();
                    }
                    updateToolbarState(editor);
                    updateFloatingImageToolbar(editor);
                    break;
                case 'align-center':
                    if (editor.isActive('image')) {
                        editor.chain().focus().updateAttributes('image', { alignment: 'center' }).run();
                    } else {
                        editor.chain().focus().setTextAlign('center').run();
                    }
                    updateToolbarState(editor);
                    updateFloatingImageToolbar(editor);
                    break;
                case 'align-right':
                    if (editor.isActive('image')) {
                        editor.chain().focus().updateAttributes('image', { alignment: 'right' }).run();
                    } else {
                        editor.chain().focus().setTextAlign('right').run();
                    }
                    updateToolbarState(editor);
                    updateFloatingImageToolbar(editor);
                    break;
                case 'align-justify':
                    if (!editor.isActive('image')) {
                        editor.chain().focus().setTextAlign('justify').run();
                    }
                    updateToolbarState(editor);
                    break;
                case 'indent':
                    if (editor.isActive('listItem')) {
                        editor.chain().focus().sinkListItem('listItem').run();
                    } else {
                        editor.chain().focus().indent().run();
                    }
                    break;
                case 'outdent':
                    if (editor.isActive('listItem')) {
                        editor.chain().focus().liftListItem('listItem').run();
                    } else {
                        editor.chain().focus().outdent().run();
                    }
                    break;
                case 'hr':
                    editor.chain().focus().setHorizontalRule().run();
                    break;
                case 'undo':
                    editor.chain().focus().undo().run();
                    break;
                case 'redo':
                    editor.chain().focus().redo().run();
                    break;
                case 'link':
                    openLinkModal(editor);
                    break;
                case 'image':
                    triggerImageUpload();
                    break;
                case 'paste-text':
                    pasteAsPlainText(editor);
                    break;
                case 'shortcuts-help':
                    openShortcutsModal();
                    break;
            }
        });
    });

    // 3. Text color palette selection
    scope.querySelectorAll('[data-editor-color]').forEach((btn) => {
        registerListener(btn, 'click', (e) => {
            e.preventDefault();
            const color = btn.getAttribute('data-editor-color');
            if (color === 'reset') {
                editor.chain().focus().unsetTextColor().run();
                showEditorToast('Đã xóa màu chữ.', 'info');
            } else {
                const currentBg = editor.getAttributes('textHighlight').color;
                if (currentBg) {
                    checkContrastWarning(color, currentBg);
                }
                editor.chain().focus().setTextColor(color).run();
            }
        });
    });

    // Custom color picker
    const customColorInput = document.getElementById('editor-custom-color-picker');
    if (customColorInput) {
        registerListener(customColorInput, 'input', (e) => {
            const color = e.target.value;
            const currentBg = editor.getAttributes('textHighlight').color;
            if (currentBg) {
                checkContrastWarning(color, currentBg);
            }
            editor.chain().focus().setTextColor(color).run();
        });
    }

    // 4. Highlight palette selection
    scope.querySelectorAll('[data-editor-highlight]').forEach((btn) => {
        registerListener(btn, 'click', (e) => {
            e.preventDefault();
            const highlightColor = btn.getAttribute('data-editor-highlight');
            if (highlightColor === 'reset') {
                editor.chain().focus().unsetHighlight().run();
                showEditorToast('Đã xóa màu nền.', 'info');
            } else {
                const currentText = editor.getAttributes('textColor').color;
                if (currentText) {
                    checkContrastWarning(currentText, highlightColor);
                }
                editor.chain().focus().setHighlight(highlightColor).run();
            }
        });
    });
}

async function pasteAsPlainText(editor) {
    if (!navigator.clipboard || !navigator.clipboard.readText) {
        showEditorToast('Vui lòng sử dụng phím tắt Ctrl+Shift+V (hoặc Cmd+Shift+V trên Mac) để dán văn bản thuần.', 'info');
        return;
    }
    try {
        const text = await navigator.clipboard.readText();
        if (text) {
            editor.chain().focus().insertContent(text).run();
            showEditorToast('Đã dán văn bản thuần.', 'success');
        } else {
            showEditorToast('Bộ nhớ tạm (clipboard) đang trống.', 'info');
        }
    } catch {
        showEditorToast('Trình duyệt chặn đọc Clipboard. Vui lòng nhấn Ctrl+Shift+V (Cmd+Shift+V) để dán văn bản thuần.', 'info');
    }
}

// ============================================================================
// MODALS SETUP (LINK, IMAGE ALT, SHORTCUTS)
// ============================================================================

function openShortcutsModal() {
    const modalEl = document.getElementById('editor-shortcuts-modal');
    if (!modalEl) return;

    if (modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }

    if (window.bootstrap && window.bootstrap.Modal) {
        const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl, {
            backdrop: true,
            keyboard: true,
            focus: true,
        });
        modal.show();
    } else {
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
    }
}

function setupShortcutsModal() {
    const modalEl = document.getElementById('editor-shortcuts-modal');
    if (!modalEl) return;

    if (modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }

    registerListener(modalEl, 'hidden.bs.modal', () => {
        document.querySelectorAll('.modal-backdrop').forEach((el) => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');

        const triggerBtn = document.querySelector('[data-editor-action="shortcuts-help"]');
        if (triggerBtn) {
            triggerBtn.focus();
        }
    });

    registerListener(modalEl, 'click', (e) => {
        if (e.target === modalEl) {
            if (window.bootstrap && window.bootstrap.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(modalEl).hide();
            } else {
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
            }
        }
    });

    registerListener(document, 'keydown', (e) => {
        if (e.key === 'Escape') {
            if (modalEl.classList.contains('show')) {
                if (window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                } else {
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                }
            }
        }
    });
}

function openLinkModal(editor) {
    const modalEl = document.getElementById('editor-link-modal');
    if (!modalEl) return;

    const urlInput = document.getElementById('editor-link-url');
    const unlinkBtn = document.getElementById('editor-link-unlink-btn');

    const currentHref = editor.getAttributes('link').href || '';
    if (urlInput) {
        urlInput.value = currentHref;
    }

    if (unlinkBtn) {
        if (currentHref) {
            unlinkBtn.classList.remove('d-none');
        } else {
            unlinkBtn.classList.add('d-none');
        }
    }

    if (window.bootstrap && window.bootstrap.Modal) {
        const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
        setTimeout(() => urlInput?.focus(), 250);
    }
}

function closeLinkModal() {
    const modalEl = document.getElementById('editor-link-modal');
    if (!modalEl) return;
    if (window.bootstrap && window.bootstrap.Modal) {
        const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.hide();
    }
}

function setupLinkModal(editor) {
    const modalEl = document.getElementById('editor-link-modal');
    if (!modalEl) return;

    if (modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }

    registerListener(modalEl, 'hidden.bs.modal', () => {
        document.querySelectorAll('.modal-backdrop').forEach((el) => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });

    const urlInput = document.getElementById('editor-link-url');
    const saveBtn = document.getElementById('editor-link-save-btn');
    const unlinkBtn = document.getElementById('editor-link-unlink-btn');

    if (saveBtn) {
        registerListener(saveBtn, 'click', () => {
            let url = (urlInput?.value || '').trim();
            if (!url) {
                showEditorToast('Vui lòng nhập địa chỉ liên kết URL.', 'danger');
                return;
            }

            if (/^(javascript:|data:|vbscript:)/i.test(url)) {
                showEditorToast('Giao thức URL không an toàn. Vui lòng nhập liên kết http:// hoặc https://', 'danger');
                return;
            }

            if (!/^(https?:\/\/|\/|#|mailto:)/i.test(url)) {
                url = 'https://' + url;
            }

            editor.chain().focus().extendMarkRange('link').setLink({
                href: url,
                target: '_blank',
                rel: 'noopener noreferrer',
            }).run();

            closeLinkModal();
            showEditorToast('Đã áp dụng liên kết thành công.', 'success');
        });
    }

    if (unlinkBtn) {
        registerListener(unlinkBtn, 'click', () => {
            editor.chain().focus().unsetLink().run();
            closeLinkModal();
            showEditorToast('Đã xóa liên kết.', 'info');
        });
    }

    const editorEl = document.getElementById('tiptap-editor');
    if (editorEl) {
        registerListener(editorEl, 'click', (e) => {
            const linkTarget = e.target.closest('a');
            if (linkTarget) {
                e.preventDefault();
                openLinkModal(editor);
            }
        });
    }
}

// ============================================================================
// INLINE IMAGE UPLOAD
// ============================================================================

function triggerImageUpload() {
    const fileInput = document.getElementById('editor-image-file-input');
    if (fileInput) {
        fileInput.click();
    }
}

function setupImageUpload(editor) {
    const fileInput = document.getElementById('editor-image-file-input');
    const imageBtn = document.querySelector('[data-editor-action="image"]');
    if (!fileInput) return;

    registerListener(fileInput, 'change', async () => {
        const file = fileInput.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            showEditorToast('Dung lượng hình ảnh vượt quá 2MB. Vui lòng chọn ảnh nhỏ hơn.', 'danger');
            fileInput.value = '';
            return;
        }

        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            showEditorToast('Định dạng hình ảnh không hợp lệ. Vui lòng chọn JPG, PNG, WEBP hoặc GIF.', 'danger');
            fileInput.value = '';
            return;
        }

        const originalHtml = imageBtn ? imageBtn.innerHTML : '';
        if (imageBtn) {
            imageBtn.disabled = true;
            imageBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width: 13px; height: 13px;"></span>
            `;
        }

        try {
            const formData = new FormData();
            formData.append('image', file);

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || document.querySelector('input[name="_token"]')?.value;

            const response = await fetch('/posts/upload-image', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (response.ok && data.url) {
                editor.chain().focus().setImage({
                    src: data.url,
                    alt: '',
                    title: '',
                    alignment: 'center',
                    caption: null,
                }).run();
                setPostEditorDirty(true);
                showEditorToast('Đã tải lên và chèn hình ảnh thành công!', 'success');
            } else {
                const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Tải ảnh thất bại.');
                showEditorToast(errMsg, 'danger');
            }
        } catch {
            showEditorToast('Lỗi kết nối khi tải ảnh lên. Vui lòng thử lại.', 'danger');
        } finally {
            if (imageBtn) {
                imageBtn.disabled = false;
                imageBtn.innerHTML = originalHtml;
            }
            fileInput.value = '';
        }
    });
}

// ============================================================================
// FLOATING IMAGE TOOLBAR
// ============================================================================

export function setupFloatingImageToolbar(editor) {
    if (!floatingImageToolbarEl) {
        floatingImageToolbarEl = document.createElement('div');
        floatingImageToolbarEl.id = 'editor-image-floating-toolbar';
        floatingImageToolbarEl.className = 'editor-image-floating-bar d-none';
        floatingImageToolbarEl.innerHTML = `
            <div class="d-flex align-items-center gap-1 p-1 rounded-pill shadow-lg border border-theme bg-theme-surface text-theme">
                <button type="button" class="btn btn-sm btn-link p-1 px-2 rounded-pill hover-bg-surface-2" data-float-align="left" title="Căn trái">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14"/></svg>
                </button>
                <button type="button" class="btn btn-sm btn-link p-1 px-2 rounded-pill hover-bg-surface-2" data-float-align="center" title="Căn giữa (Mặc định)">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M5 18h14"/></svg>
                </button>
                <button type="button" class="btn btn-sm btn-link p-1 px-2 rounded-pill hover-bg-surface-2" data-float-align="right" title="Căn phải">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M6 18h14"/></svg>
                </button>
                <div class="vr my-1 border-theme opacity-50" style="height: 16px;"></div>
                <button type="button" class="btn btn-sm btn-link p-1 px-2.5 rounded-pill hover-bg-surface-2 d-flex align-items-center gap-1 small text-decoration-none" data-float-action="edit" title="Chỉnh sửa chú thích & ALT">
                    <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span style="font-size: 11px;">Chú thích & ALT</span>
                </button>
                <div class="vr my-1 border-theme opacity-50" style="height: 16px;"></div>
                <button type="button" class="btn btn-sm btn-link text-danger p-1 px-2 rounded-pill hover-bg-surface-2" data-float-action="delete" title="Xóa ảnh">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        `;
        document.body.appendChild(floatingImageToolbarEl);

        floatingImageToolbarEl.querySelectorAll('[data-float-align]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const align = btn.getAttribute('data-float-align');
                editor.chain().focus().updateAttributes('image', { alignment: align }).run();
                setPostEditorDirty(true);
                updateToolbarState(editor);
                updateFloatingImageToolbar(editor);
            });
        });

        const editBtn = floatingImageToolbarEl.querySelector('[data-float-action="edit"]');
        if (editBtn) {
            editBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const selectedDom = editor.view.dom.querySelector('.ProseMirror-selectednode');
                const img = selectedDom ? (selectedDom.tagName === 'IMG' ? selectedDom : selectedDom.querySelector('img')) : null;
                if (img && window.__openEditorImageModal) {
                    window.__openEditorImageModal(img);
                }
            });
        }

        const deleteBtn = floatingImageToolbarEl.querySelector('[data-float-action="delete"]');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                editor.chain().focus().deleteSelection().run();
                floatingImageToolbarEl.classList.add('d-none');
                setPostEditorDirty(true);
            });
        }

        const onScrollOrResize = () => {
            if (floatingImageToolbarEl && !floatingImageToolbarEl.classList.contains('d-none')) {
                updateFloatingImageToolbar(editor);
            }
        };

        registerListener(window, 'scroll', onScrollOrResize, { passive: true });
        registerListener(window, 'resize', onScrollOrResize, { passive: true });
    }
}

export function updateFloatingImageToolbar(editor) {
    if (!floatingImageToolbarEl) return;

    if (!editor.isActive('image')) {
        floatingImageToolbarEl.classList.add('d-none');
        return;
    }

    const selectedDom = editor.view.dom.querySelector('.ProseMirror-selectednode')
        || (editor.state.selection.node?.type?.name === 'image' ? editor.view.nodeDOM(editor.state.selection.from) : null);

    if (!selectedDom) {
        floatingImageToolbarEl.classList.add('d-none');
        return;
    }

    const rect = selectedDom.getBoundingClientRect();
    if (rect.width === 0 && rect.height === 0) {
        floatingImageToolbarEl.classList.add('d-none');
        return;
    }

    floatingImageToolbarEl.classList.remove('d-none');
    const barWidth = floatingImageToolbarEl.offsetWidth || 260;
    const barHeight = floatingImageToolbarEl.offsetHeight || 38;

    const left = Math.max(12, Math.min(window.innerWidth - barWidth - 12, rect.left + (rect.width - barWidth) / 2));
    let top = rect.top - barHeight - 8 + window.scrollY;
    if (top < window.scrollY + 10) {
        top = rect.bottom + 8 + window.scrollY;
    }

    floatingImageToolbarEl.style.left = `${left}px`;
    floatingImageToolbarEl.style.top = `${top}px`;

    const attrs = editor.getAttributes('image');
    const currentAlign = attrs.alignment || 'center';
    floatingImageToolbarEl.querySelectorAll('[data-float-align]').forEach((btn) => {
        btn.classList.toggle('active', btn.getAttribute('data-float-align') === currentAlign);
    });
}

function setupImageAltModal(editor) {
    const modalEl = document.getElementById('editor-image-modal');
    if (!modalEl) return;

    if (modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }

    let currentEditingImg = null;
    let currentEditingPos = null;

    registerListener(modalEl, 'hidden.bs.modal', () => {
        document.querySelectorAll('.modal-backdrop').forEach((el) => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
        currentEditingImg = null;
        currentEditingPos = null;
    });

    const alignButtons = modalEl.querySelectorAll('[data-editor-image-align]');
    const alignInput = document.getElementById('editor-image-align-input');
    const captionInput = document.getElementById('editor-image-caption');
    const altInput = document.getElementById('editor-image-alt');
    const titleInput = document.getElementById('editor-image-title');
    const previewEl = document.getElementById('editor-image-preview');
    const filenameEl = document.getElementById('editor-image-filename');
    const srcEl = document.getElementById('editor-image-src');
    const saveBtn = document.getElementById('editor-image-save-btn');
    const formEl = document.getElementById('editor-image-form');

    function setModalAlign(align) {
        if (!['left', 'center', 'right'].includes(align)) align = 'center';
        if (alignInput) alignInput.value = align;
        alignButtons.forEach((btn) => {
            btn.classList.toggle('active', btn.getAttribute('data-editor-image-align') === align);
        });
    }

    alignButtons.forEach((btn) => {
        registerListener(btn, 'click', (e) => {
            e.preventDefault();
            setModalAlign(btn.getAttribute('data-editor-image-align'));
        });
    });

    function openImageModal(img) {
        if (!img) return;
        if (modalEl.classList.contains('show')) return;

        currentEditingImg = img;
        currentEditingPos = null;

        try {
            const pos = editor.view.posAtDOM(img, 0);
            if (pos !== undefined && pos !== null) {
                const nodeAt = editor.state.doc.nodeAt(pos);
                if (nodeAt && nodeAt.type.name === 'image') {
                    currentEditingPos = pos;
                } else if (pos > 0) {
                    const prevNode = editor.state.doc.nodeAt(pos - 1);
                    if (prevNode && prevNode.type.name === 'image') {
                        currentEditingPos = pos - 1;
                    }
                }
            }
        } catch (e) {
            console.warn('posAtDOM resolution error:', e);
        }

        if (currentEditingPos === null) {
            const imgSrc = img.getAttribute('src');
            editor.state.doc.descendants((node, pos) => {
                if (node.type.name === 'image' && node.attrs.src === imgSrc) {
                    if (currentEditingPos === null) {
                        currentEditingPos = pos;
                    }
                }
            });
        }

        const nodeAt = currentEditingPos !== null ? editor.state.doc.nodeAt(currentEditingPos) : null;
        const currentAlt = nodeAt?.attrs?.alt ?? img.getAttribute('alt') ?? '';
        const currentTitle = nodeAt?.attrs?.title ?? img.getAttribute('title') ?? '';
        const currentAlign = nodeAt?.attrs?.alignment
            ?? img.getAttribute('data-align')
            ?? img.closest('figure')?.getAttribute('data-align')
            ?? (img.classList.contains('align-left') ? 'left' : (img.classList.contains('align-right') ? 'right' : 'center'));
        const currentCaption = nodeAt?.attrs?.caption
            ?? img.getAttribute('data-caption')
            ?? img.closest('figure')?.getAttribute('data-caption')
            ?? img.closest('figure')?.querySelector('figcaption')?.textContent?.trim()
            ?? '';

        const src = img.getAttribute('src') || '';
        const filename = src.split('/').pop() || 'Hình ảnh';

        setModalAlign(currentAlign);
        if (captionInput) captionInput.value = currentCaption;
        if (altInput) altInput.value = currentAlt;
        if (titleInput) titleInput.value = currentTitle;
        if (previewEl) {
            previewEl.src = src;
            previewEl.alt = currentAlt;
            previewEl.style.display = 'block';
        }
        if (filenameEl) filenameEl.textContent = filename;
        if (srcEl) srcEl.textContent = src;

        if (window.bootstrap && window.bootstrap.Modal) {
            const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
            setTimeout(() => (captionInput ? captionInput.focus() : altInput?.focus()), 250);
        }
    }

    window.__openEditorImageModal = openImageModal;

    function handleSave() {
        if (!currentEditingImg) return;
        const newAlt = (altInput?.value || '').trim();
        const newTitle = (titleInput?.value || '').trim();
        const newAlign = (alignInput?.value || 'center');
        const newCaption = (captionInput?.value || '').trim();

        if (currentEditingPos !== null) {
            try {
                editor.chain()
                    .focus()
                    .setNodeSelection(currentEditingPos)
                    .updateAttributes('image', {
                        alt: newAlt,
                        title: newTitle || (newAlt ? newAlt : null),
                        alignment: newAlign,
                        caption: newCaption || null,
                    })
                    .run();
            } catch (err) {
                console.warn('TipTap updateAttributes error:', err);
            }
        }

        if (currentEditingImg) {
            if (newAlt) currentEditingImg.setAttribute('alt', newAlt);
            else currentEditingImg.removeAttribute('alt');

            if (newTitle) currentEditingImg.setAttribute('title', newTitle);
            else if (newAlt) currentEditingImg.setAttribute('title', newAlt);
            else currentEditingImg.removeAttribute('title');

            currentEditingImg.setAttribute('data-align', newAlign);
            currentEditingImg.className = currentEditingImg.className.replace(/\balign-(left|center|right)\b/g, '').trim() + ` align-${newAlign}`;

            if (newCaption) currentEditingImg.setAttribute('data-caption', newCaption);
            else currentEditingImg.removeAttribute('data-caption');

            const figure = currentEditingImg.closest('figure');
            if (figure) {
                figure.setAttribute('data-align', newAlign);
                figure.className = figure.className.replace(/\balign-(left|center|right)\b/g, '').trim() + ` align-${newAlign}`;
                let figcaption = figure.querySelector('figcaption');
                if (newCaption) {
                    if (!figcaption) {
                        figcaption = document.createElement('figcaption');
                        figcaption.className = 'editor-image-caption';
                        figure.appendChild(figcaption);
                    }
                    figcaption.textContent = newCaption;
                } else if (figcaption) {
                    figcaption.remove();
                }
            }
        }

        const textarea = document.getElementById('content');
        if (textarea) {
            textarea.value = editor.getHTML();
        }
        setPostEditorDirty(true);

        closeModal();
        updateToolbarState(editor);
        updateFloatingImageToolbar(editor);
        showEditorToast('Đã lưu căn chỉnh và chú thích hình ảnh thành công.', 'success');
    }

    function closeModal() {
        if (window.bootstrap && window.bootstrap.Modal) {
            const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.hide();
        }
    }

    if (saveBtn) {
        registerListener(saveBtn, 'click', (e) => {
            e.preventDefault();
            handleSave();
        });
    }

    if (formEl) {
        registerListener(formEl, 'submit', (e) => {
            e.preventDefault();
            handleSave();
        });
    }

    [captionInput, altInput, titleInput].forEach((input) => {
        if (input) {
            registerListener(input, 'keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleSave();
                }
            });
        }
    });

    const editorEl = document.getElementById('tiptap-editor');
    if (editorEl) {
        registerListener(editorEl, 'dblclick', (e) => {
            const img = e.target.closest('img') || e.target.closest('figure')?.querySelector('img');
            if (img) {
                e.preventDefault();
                e.stopPropagation();
                openImageModal(img);
            }
        });
    }
}

// ============================================================================
// FORM HANDLING & DIRTY STATE GUARD
// ============================================================================

function setupFormHandling(editor, root) {
    const scope = root && root.querySelector ? root : document;
    const textarea = document.getElementById('content');
    if (!textarea) return;

    const form = scope.querySelector('#create-post-form')
        || scope.querySelector('#edit-post-form')
        || textarea.closest('form');

    if (form) {
        let lastClickedAction = 'submit';

        // Capture action from button clicks inside or outside the form targeting this form
        document.querySelectorAll(`button[type="submit"][form="${form.id}"], form#${form.id} button[type="submit"]`).forEach((btn) => {
            registerListener(btn, 'click', () => {
                lastClickedAction = btn.value || 'submit';
            });
        });

        registerListener(form, 'submit', (e) => {
            if (isSubmitting) {
                e.preventDefault();
                return;
            }

            isSubmitting = true;
            textarea.value = editor.getHTML();

            let actionInput = form.querySelector('input[name="action"][type="hidden"]');
            if (!actionInput) {
                actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                form.appendChild(actionInput);
            }
            actionInput.value = lastClickedAction;

            const allSubmitBtns = document.querySelectorAll(`button[type="submit"][form="${form.id}"], form#${form.id} button[type="submit"]`);
            allSubmitBtns.forEach((btn) => {
                btn.disabled = true;

                const isSubmitAction = (btn.value === 'submit' || (lastClickedAction === 'submit' && btn.classList.contains('btn-action-submit')));
                const isDraftAction = (btn.value === 'draft' || (lastClickedAction === 'draft' && btn.classList.contains('btn-action-draft')));

                if (isSubmitAction && lastClickedAction === 'submit') {
                    btn.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-1.5" role="status" aria-hidden="true" style="width: 13px; height: 13px;"></span>
                        <span>Đang gửi duyệt...</span>
                    `;
                } else if (isDraftAction && lastClickedAction === 'draft') {
                    btn.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-1.5" role="status" aria-hidden="true" style="width: 13px; height: 13px;"></span>
                        <span>Đang lưu...</span>
                    `;
                }
            });
        });
    }

    // Input listeners for title, excerpt, and category
    ['title', 'excerpt', 'category_id'].forEach((id) => {
        const el = document.getElementById(id);
        if (el) {
            const onInput = () => { setPostEditorDirty(true); };
            registerListener(el, 'input', onInput);
            registerListener(el, 'change', onInput);
        }
    });

    // Native beforeunload for refresh (F5) and tab close
    const onBeforeUnload = (e) => {
        if (isDirty && !isSubmitting) {
            e.preventDefault();
            e.returnValue = '';
        }
    };
    registerListener(window, 'beforeunload', onBeforeUnload);
}

// ============================================================================
// THUMBNAIL UPLOADER INITIALIZATION
// ============================================================================

export function setupThumbnailUploader(root = document) {
    const scope = root && root.querySelector ? root : document;
    const widget = scope.querySelector('#thumbnail-uploader-widget') || document.getElementById('thumbnail-uploader-widget');
    if (!widget || widget.dataset.initialized) return;
    widget.dataset.initialized = 'true';

    const initialThumbnail = widget.getAttribute('data-initial-thumbnail') || '';
    const fileInput = document.getElementById('thumbnail-file-input');
    const removeInput = document.getElementById('thumbnail-remove-input');
    const dropZone = document.getElementById('thumbnail-drop-zone');
    const previewContainer = document.getElementById('thumbnail-preview-container');
    const previewImg = document.getElementById('thumbnail-preview-img');
    const badgeStatus = document.getElementById('thumbnail-badge-status');
    const indicator = document.getElementById('thumbnail-indicator');
    const pendingRemoval = document.getElementById('thumbnail-pending-removal');
    const btnChange = document.getElementById('btn-change-thumbnail');
    const btnRevert = document.getElementById('btn-revert-thumbnail');
    const btnRemove = document.getElementById('btn-remove-thumbnail');
    const btnUndoRemove = document.getElementById('btn-undo-remove');
    const btnChooseNew = document.getElementById('btn-choose-new-instead');
    const confirmModalEl = document.getElementById('thumbnail-remove-confirm-modal');
    const btnConfirmRemove = document.getElementById('btn-confirm-remove-thumbnail');
    const errorFeedback = document.getElementById('thumbnail-error-feedback');

    if (!fileInput || !dropZone) return;

    if (confirmModalEl && confirmModalEl.parentElement !== document.body) {
        document.body.appendChild(confirmModalEl);
    }

    function clearError() {
        if (errorFeedback) {
            errorFeedback.textContent = '';
            errorFeedback.classList.add('d-none');
        }
    }

    function showError(msg) {
        if (errorFeedback) {
            errorFeedback.textContent = msg;
            errorFeedback.classList.remove('d-none');
        }
    }

    function showEmptyState() {
        clearError();
        fileInput.value = '';
        if (removeInput) removeInput.value = '0';
        dropZone.classList.remove('d-none');
        if (previewContainer) previewContainer.classList.add('d-none');
        if (pendingRemoval) pendingRemoval.classList.add('d-none');
        if (indicator) {
            indicator.textContent = 'Chưa có ảnh';
            indicator.className = 'badge bg-surface-2 text-theme-muted border border-theme font-monospace small';
        }
    }

    function showPreviewState(url, isNew = false) {
        clearError();
        if (removeInput) removeInput.value = '0';
        if (previewImg) {
            previewImg.src = url;
            previewImg.style.display = 'block';
        }
        dropZone.classList.add('d-none');
        if (pendingRemoval) pendingRemoval.classList.add('d-none');
        if (previewContainer) previewContainer.classList.remove('d-none');

        if (isNew) {
            if (badgeStatus) badgeStatus.textContent = 'Ảnh mới đã chọn';
            if (indicator) {
                indicator.textContent = 'Ảnh mới';
                indicator.className = 'badge bg-primary-subtle text-primary border border-primary border-opacity-25 font-monospace small';
            }
            if (btnRevert) {
                if (initialThumbnail) {
                    btnRevert.classList.remove('d-none');
                    btnRevert.classList.add('d-inline-flex');
                } else {
                    btnRevert.classList.add('d-none');
                    btnRevert.classList.remove('d-inline-flex');
                }
            }
        } else {
            if (badgeStatus) badgeStatus.textContent = 'Ảnh hiện tại';
            if (indicator) {
                indicator.textContent = 'Đã có ảnh';
                indicator.className = 'badge bg-surface-2 text-theme-muted border border-theme font-monospace small';
            }
            if (btnRevert) {
                btnRevert.classList.add('d-none');
                btnRevert.classList.remove('d-inline-flex');
            }
        }
    }

    function showPendingRemovalState() {
        clearError();
        fileInput.value = '';
        if (removeInput) removeInput.value = '1';
        dropZone.classList.add('d-none');
        if (previewContainer) previewContainer.classList.add('d-none');
        if (pendingRemoval) pendingRemoval.classList.remove('d-none');
        if (indicator) {
            indicator.textContent = 'Chờ gỡ ảnh';
            indicator.className = 'badge bg-danger-subtle text-danger border border-danger border-opacity-25 font-monospace small';
        }
        setPostEditorDirty(true);
    }

    function openFilePicker() {
        fileInput.click();
    }

    registerListener(dropZone, 'click', openFilePicker);
    registerListener(dropZone, 'keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            openFilePicker();
        }
    });

    if (btnChange) registerListener(btnChange, 'click', openFilePicker);
    if (btnChooseNew) registerListener(btnChooseNew, 'click', openFilePicker);

    ['dragenter', 'dragover'].forEach((eventName) => {
        registerListener(dropZone, eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('border-primary', 'bg-surface-3');
        });
    });

    ['dragleave', 'drop'].forEach((eventName) => {
        registerListener(dropZone, eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('border-primary', 'bg-surface-3');
        });
    });

    registerListener(dropZone, 'drop', (e) => {
        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files.length) {
            fileInput.files = dt.files;
            handleFileSelected(dt.files[0]);
        }
    });

    registerListener(fileInput, 'change', () => {
        if (!fileInput.files || !fileInput.files[0]) return;
        handleFileSelected(fileInput.files[0]);
    });

    function handleFileSelected(file) {
        if (!file) return;

        const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            showError('Định dạng file không hợp lệ. Vui lòng chọn ảnh JPG, PNG hoặc WEBP.');
            fileInput.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            showError('Dung lượng hình ảnh vượt quá 2MB. Vui lòng chọn ảnh nhỏ hơn.');
            fileInput.value = '';
            return;
        }

        clearError();
        const reader = new FileReader();
        reader.onload = function(e) {
            showPreviewState(e.target.result, true);
            setPostEditorDirty(true);
        };
        reader.readAsDataURL(file);
    }

    if (btnRevert) {
        registerListener(btnRevert, 'click', (e) => {
            e.preventDefault();
            fileInput.value = '';
            if (initialThumbnail) {
                showPreviewState(initialThumbnail, false);
            } else {
                showEmptyState();
            }
            setPostEditorDirty(true);
        });
    }

    if (btnRemove) {
        registerListener(btnRemove, 'click', (e) => {
            e.preventDefault();
            if (!initialThumbnail && fileInput.files && fileInput.files[0]) {
                showEmptyState();
                setPostEditorDirty(true);
                return;
            }

            if (window.bootstrap && window.bootstrap.Modal && confirmModalEl) {
                const modal = window.bootstrap.Modal.getOrCreateInstance(confirmModalEl);
                modal.show();
            } else {
                if (confirm('Bạn có chắc chắn muốn gỡ ảnh đại diện của bài viết này không?')) {
                    showPendingRemovalState();
                }
            }
        });
    }

    if (btnConfirmRemove) {
        registerListener(btnConfirmRemove, 'click', (e) => {
            e.preventDefault();
            if (window.bootstrap && window.bootstrap.Modal && confirmModalEl) {
                const modal = window.bootstrap.Modal.getOrCreateInstance(confirmModalEl);
                modal.hide();
            }
            showPendingRemovalState();
        });
    }

    if (btnUndoRemove) {
        registerListener(btnUndoRemove, 'click', (e) => {
            e.preventDefault();
            if (initialThumbnail) {
                showPreviewState(initialThumbnail, false);
            } else {
                showEmptyState();
            }
            setPostEditorDirty(true);
        });
    }
}

// ============================================================================
// TAG SELECTOR INITIALIZATION
// ============================================================================

export function setupTagSelector(root = document) {
    const scope = root && root.querySelector ? root : document;
    const widget = scope.querySelector('#tag-manager-component') || document.getElementById('tag-manager-component');
    if (!widget || widget.dataset.initialized) return;
    widget.dataset.initialized = 'true';

    const formId = widget.getAttribute('data-form-id') || 'create-post-form';
    const MAX_TAGS = 10;

    let initialSelectedIds = [];
    let allTags = [];
    try {
        initialSelectedIds = JSON.parse(widget.getAttribute('data-initial-tags') || '[]');
        allTags = JSON.parse(widget.getAttribute('data-all-tags') || '[]');
    } catch (e) {
        console.warn('Tag selector JSON parse error:', e);
    }

    const tagMap = new Map();
    const selectedIds = new Set();

    allTags.forEach((t) => tagMap.set(t.id, t));
    initialSelectedIds.forEach((id) => {
        if (tagMap.has(id)) selectedIds.add(id);
    });

    const chipsContainer = document.getElementById('selected-tags-chips');
    const hiddenInputsContainer = document.getElementById('hidden-tag-inputs');
    const searchInput = document.getElementById('tag-search-input');
    const addBtn = document.getElementById('tag-add-btn');
    const cloudContainer = document.getElementById('available-tags-cloud');
    const counterEl = document.getElementById('tag-selected-count');
    const limitAlert = document.getElementById('tag-limit-alert');
    const feedbackEl = document.getElementById('tag-input-feedback');
    const filterCountEl = document.getElementById('tag-filter-count');

    if (!chipsContainer || !hiddenInputsContainer) return;

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function cleanTagName(name) {
        if (!name) return '';
        return name.replace(/^#+/, '').trim();
    }

    function showFeedback(msg) {
        if (!feedbackEl) return;
        if (msg) {
            feedbackEl.textContent = msg;
            feedbackEl.classList.remove('d-none');
        } else {
            feedbackEl.textContent = '';
            feedbackEl.classList.add('d-none');
        }
    }

    function render() {
        if (counterEl) counterEl.textContent = selectedIds.size;
        if (limitAlert) {
            if (selectedIds.size >= MAX_TAGS) {
                limitAlert.classList.remove('d-none');
            } else {
                limitAlert.classList.add('d-none');
            }
        }

        chipsContainer.innerHTML = '';
        if (selectedIds.size === 0) {
            chipsContainer.innerHTML = `
                <span id="no-tags-selected-placeholder" class="small text-theme-muted fst-italic py-1 px-1" style="font-size: 11.5px;">
                    Chưa có thẻ nào được chọn.
                </span>
            `;
        } else {
            selectedIds.forEach((id) => {
                const tag = tagMap.get(id);
                if (!tag) return;

                const chip = document.createElement('span');
                chip.className = 'd-inline-flex align-items-center gap-1.5 py-1 ps-2.5 pe-1.5 rounded-pill bg-theme-surface text-theme border border-theme shadow-xs';
                chip.style.fontSize = '12px';

                chip.innerHTML = `
                    <span class="fw-medium font-monospace">#${escapeHtml(tag.name)}</span>
                    <button type="button" class="btn btn-link text-theme-muted p-0 d-inline-flex align-items-center justify-content-center hover-danger"
                            style="width: 18px; height: 18px; border-radius: 999px; text-decoration: none;"
                            aria-label="Gỡ thẻ ${escapeHtml(tag.name)}"
                            title="Gỡ thẻ ${escapeHtml(tag.name)}">
                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;

                chip.querySelector('button').addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    removeTag(id);
                });

                chipsContainer.appendChild(chip);
            });
        }

        hiddenInputsContainer.innerHTML = '';
        selectedIds.forEach((id) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tags[]';
            input.value = id;
            if (formId) {
                input.setAttribute('form', formId);
            }
            hiddenInputsContainer.appendChild(input);
        });

        filterCloud();
    }

    function filterCloud() {
        if (!cloudContainer) return;
        const query = cleanTagName(searchInput?.value || '').toLowerCase();
        cloudContainer.innerHTML = '';

        let visibleCount = 0;
        let totalCount = 0;

        tagMap.forEach((tag) => {
            totalCount++;
            const matches = !query || tag.name.toLowerCase().includes(query) || tag.slug.toLowerCase().includes(query);
            if (!matches) return;

            visibleCount++;
            const isSelected = selectedIds.has(tag.id);

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `btn btn-sm rounded-pill font-monospace small px-2.5 py-0.5 d-inline-flex align-items-center gap-1 transition-all ${
                isSelected
                    ? 'btn-editorial-primary text-white border-0 shadow-xs'
                    : 'btn-outline-theme hover-bg-surface-2'
            }`;
            btn.style.fontSize = '11.5px';

            btn.innerHTML = `
                <span>#${escapeHtml(tag.name)}</span>
                ${isSelected ? '<svg style="width: 11px; height: 11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>' : ''}
            `;

            btn.addEventListener('click', (e) => {
                e.preventDefault();
                toggleTag(tag.id);
            });

            cloudContainer.appendChild(btn);
        });

        if (filterCountEl) {
            filterCountEl.textContent = query ? `(Tìm thấy ${visibleCount}/${totalCount})` : `(${totalCount} thẻ)`;
        }
    }

    function toggleTag(id) {
        if (selectedIds.has(id)) {
            removeTag(id);
        } else {
            addTag(id);
        }
    }

    function addTag(id) {
        if (selectedIds.size >= MAX_TAGS) {
            if (limitAlert) limitAlert.classList.remove('d-none');
            showFeedback('Không thể chọn quá 10 thẻ.');
            return;
        }
        selectedIds.add(id);
        showFeedback('');
        setPostEditorDirty(true);
        render();
    }

    function removeTag(id) {
        selectedIds.delete(id);
        showFeedback('');
        setPostEditorDirty(true);
        render();
    }

    async function handleAddNewTag() {
        const raw = searchInput?.value || '';
        const clean = cleanTagName(raw);

        if (!clean) {
            showFeedback('Vui lòng nhập tên thẻ bài viết.');
            return;
        }

        if (clean.length > 50) {
            showFeedback('Tên thẻ không được dài quá 50 ký tự.');
            return;
        }

        for (const id of selectedIds) {
            const t = tagMap.get(id);
            if (t && t.name.toLowerCase() === clean.toLowerCase()) {
                showFeedback(`Thẻ #${t.name} đã được chọn.`);
                return;
            }
        }

        if (selectedIds.size >= MAX_TAGS) {
            if (limitAlert) limitAlert.classList.remove('d-none');
            showFeedback('Không thể chọn thêm thẻ (Tối đa 10 thẻ).');
            return;
        }

        let existingId = null;
        for (const [id, t] of tagMap.entries()) {
            if (t.name.toLowerCase() === clean.toLowerCase() || t.slug.toLowerCase() === clean.toLowerCase()) {
                existingId = id;
                break;
            }
        }

        if (existingId !== null) {
            addTag(existingId);
            if (searchInput) searchInput.value = '';
            filterCloud();
            return;
        }

        const originalText = addBtn ? addBtn.innerHTML : '';
        if (addBtn) {
            addBtn.disabled = true;
            addBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" style="width: 11px; height: 11px;"></span>';
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || document.querySelector('input[name="_token"]')?.value;

            const response = await fetch('/tags', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({ name: clean }),
            });

            const data = await response.json();

            if (response.ok && data.success && data.tag) {
                const newTag = data.tag;
                tagMap.set(newTag.id, newTag);
                addTag(newTag.id);
                if (searchInput) searchInput.value = '';
                showFeedback('');
            } else {
                const err = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Tạo thẻ thất bại.');
                showFeedback(err);
            }
        } catch {
            showFeedback('Lỗi kết nối khi tạo thẻ mới.');
        } finally {
            if (addBtn) {
                addBtn.disabled = false;
                addBtn.innerHTML = originalText;
            }
        }
    }

    if (searchInput) {
        registerListener(searchInput, 'input', filterCloud);
        registerListener(searchInput, 'keydown', (e) => {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                handleAddNewTag();
            }
        });
    }

    if (addBtn) {
        registerListener(addBtn, 'click', (e) => {
            e.preventDefault();
            handleAddNewTag();
        });
    }

    render();
}

// ============================================================================
// TOAST NOTIFICATIONS
// ============================================================================

function showEditorToast(message, type = 'info') {
    const toastContainer = document.getElementById('global-toast-container');
    if (!toastContainer) {
        return;
    }

    const toastId = 'toast-' + Math.random().toString(36).substring(2, 9);
    let bgClass = 'bg-dark text-white';
    let closeBtnClass = 'btn-close-white';

    if (type === 'success') {
        bgClass = 'bg-success text-white';
    } else if (type === 'danger') {
        bgClass = 'bg-danger text-white';
    } else if (type === 'warning') {
        bgClass = 'bg-warning text-dark border border-warning';
        closeBtnClass = '';
    }

    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center ${bgClass} border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body small fw-medium">
                    ${message}
                </div>
                <button type="button" class="btn-close ${closeBtnClass} me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    const toastEl = document.getElementById(toastId);
    if (window.bootstrap && window.bootstrap.Toast && toastEl) {
        const toast = new window.bootstrap.Toast(toastEl, { delay: 4500 });
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }
}
