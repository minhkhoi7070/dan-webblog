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
            'Mod-Alt-l': () => this.editor.commands.setTextAlign('left'),
            'Ctrl-Alt-l': () => this.editor.commands.setTextAlign('left'),
            'Mod-Alt-e': () => this.editor.commands.setTextAlign('center'),
            'Ctrl-Alt-e': () => this.editor.commands.setTextAlign('center'),
            'Mod-Alt-r': () => this.editor.commands.setTextAlign('right'),
            'Ctrl-Alt-r': () => this.editor.commands.setTextAlign('right'),
            'Mod-Alt-j': () => this.editor.commands.setTextAlign('justify'),
            'Ctrl-Alt-j': () => this.editor.commands.setTextAlign('justify'),
        };
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

    // If contrast is poor, surface a helpful hint without overriding user choice
    if (ratio < 2.5) {
        showEditorToast('Gợi ý: Độ tương phản giữa màu chữ và màu nền đang chọn khá thấp, có thể gây khó đọc.', 'warning');
    }
}

// ============================================================================
// MAIN EDITOR INITIALIZATION
// ============================================================================

export function initPostEditor() {
    const editorElement = document.getElementById('tiptap-editor');
    const textarea = document.getElementById('content');
    if (!editorElement || !textarea) return null;

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
            Image.configure({
                inline: false,
                allowBase64: false,
                HTMLAttributes: {
                    class: 'editor-content-image',
                    loading: 'lazy',
                },
            }),
        ],
        content: initialContent,
        editorProps: {
            attributes: {
                class: 'editor-wysiwyg-surface p-3',
                spellcheck: 'false',
            },
            transformPastedHTML(html) {
                // Client-side sanitization: strip script, iframe, and dangerous event handlers on paste
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
            updateEditorStats(editor);
            updateToolbarState(editor);
        },
        onSelectionUpdate: ({ editor }) => {
            updateToolbarState(editor);
        },
    });

    // Ensure hidden textarea is synced before form submission
    const form = textarea.closest('form');
    if (form) {
        form.addEventListener('submit', () => {
            textarea.value = editor.getHTML();
        });
    }

    setupToolbar(editor);
    setupLinkModal(editor);
    setupShortcutsModal();
    setupImageUpload(editor);
    updateEditorStats(editor);
    updateToolbarState(editor);

    return editor;
}

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

    // 1. Update Block Type Dropdown Label & State
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

    // 2. Update Inline & Block Button States
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
        'align-left': () => editor.isActive({ textAlign: 'left' }),
        'align-center': () => editor.isActive({ textAlign: 'center' }),
        'align-right': () => editor.isActive({ textAlign: 'right' }),
        'align-justify': () => editor.isActive({ textAlign: 'justify' }),
    };

    document.querySelectorAll('[data-editor-action]').forEach((btn) => {
        const action = btn.getAttribute('data-editor-action');
        const checkFn = actionMap[action];
        if (checkFn) {
            btn.classList.toggle('active', Boolean(checkFn()));
        }

        // Handle Undo / Redo disabled states
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

    // 3. Update List Indent / Outdent disabled states based on active list item
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

function setupToolbar(editor) {
    // 1. Heading & Paragraph block dropdown selection
    document.querySelectorAll('[data-editor-block]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
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
    document.querySelectorAll('[data-editor-action]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
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
                    // Unset character formatting while keeping structure intact
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
                    editor.chain().focus().setTextAlign('left').run();
                    break;
                case 'align-center':
                    editor.chain().focus().setTextAlign('center').run();
                    break;
                case 'align-right':
                    editor.chain().focus().setTextAlign('right').run();
                    break;
                case 'align-justify':
                    editor.chain().focus().setTextAlign('justify').run();
                    break;
                case 'indent':
                    // Genuine structural list nesting if in list, otherwise block indent
                    if (editor.isActive('listItem')) {
                        editor.chain().focus().sinkListItem('listItem').run();
                    } else {
                        editor.chain().focus().indent().run();
                    }
                    break;
                case 'outdent':
                    // Genuine structural list lifting if in list, otherwise block outdent
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
    document.querySelectorAll('[data-editor-color]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
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

    // Custom color input picker
    const customColorInput = document.getElementById('editor-custom-color-picker');
    if (customColorInput) {
        customColorInput.addEventListener('input', (e) => {
            const color = e.target.value;
            const currentBg = editor.getAttributes('textHighlight').color;
            if (currentBg) {
                checkContrastWarning(color, currentBg);
            }
            editor.chain().focus().setTextColor(color).run();
        });
    }

    // 4. Highlight palette selection
    document.querySelectorAll('[data-editor-highlight]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
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
        // Fallback when clipboard permission is restricted
        showEditorToast('Trình duyệt chặn đọc Clipboard. Vui lòng nhấn Ctrl+Shift+V (Cmd+Shift+V) để dán văn bản thuần.', 'info');
    }
}

function openShortcutsModal() {
    const modalEl = document.getElementById('editor-shortcuts-modal');
    if (!modalEl) return;

    // Portal pattern: ensure modal element is directly attached to document.body
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

    // Move to body immediately to prevent container hierarchy conflicts
    if (modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }

    // Single-registration cleanup listener for modal dismissal
    modalEl.addEventListener('hidden.bs.modal', () => {
        // Ensure all leftover backdrops are completely cleaned up
        document.querySelectorAll('.modal-backdrop').forEach((el) => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');

        // Restore focus to shortcuts toolbar button
        const triggerBtn = document.querySelector('[data-editor-action="shortcuts-help"]');
        if (triggerBtn) {
            triggerBtn.focus();
        }
    });

    // Close on clicking backdrop (outside dialog)
    modalEl.addEventListener('click', (e) => {
        if (e.target === modalEl) {
            if (window.bootstrap && window.bootstrap.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(modalEl).hide();
            } else {
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
            }
        }
    });

    // Cross-browser Escape key listener
    document.addEventListener('keydown', (e) => {
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

function setupLinkModal(editor) {
    const modalEl = document.getElementById('editor-link-modal');
    if (!modalEl) return;

    // Move to body immediately
    if (modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }

    modalEl.addEventListener('hidden.bs.modal', () => {
        document.querySelectorAll('.modal-backdrop').forEach((el) => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });

    const urlInput = document.getElementById('editor-link-url');
    const saveBtn = document.getElementById('editor-link-save-btn');
    const unlinkBtn = document.getElementById('editor-link-unlink-btn');

    if (saveBtn) {
        saveBtn.addEventListener('click', () => {
            let url = (urlInput?.value || '').trim();
            if (!url) {
                showEditorToast('Vui lòng nhập địa chỉ liên kết URL.', 'danger');
                return;
            }

            // Reject dangerous protocols
            if (/^(javascript:|data:|vbscript:)/i.test(url)) {
                showEditorToast('Giao thức URL không an toàn. Vui lòng nhập liên kết http:// hoặc https://', 'danger');
                return;
            }

            // Auto prepend https:// if missing protocol
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
        unlinkBtn.addEventListener('click', () => {
            editor.chain().focus().unsetLink().run();
            closeLinkModal();
            showEditorToast('Đã xóa liên kết.', 'info');
        });
    }

    // Allow clicking on links inside the editor to edit them
    const editorEl = document.getElementById('tiptap-editor');
    if (editorEl) {
        editorEl.addEventListener('click', (e) => {
            const linkTarget = e.target.closest('a');
            if (linkTarget) {
                e.preventDefault();
                openLinkModal(editor);
            }
        });
    }
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

    fileInput.addEventListener('change', async () => {
        const file = fileInput.files[0];
        if (!file) return;

        // Client validation: Max 2MB
        if (file.size > 2 * 1024 * 1024) {
            showEditorToast('Dung lượng hình ảnh vượt quá 2MB. Vui lòng chọn ảnh nhỏ hơn.', 'danger');
            fileInput.value = '';
            return;
        }

        // Validate MIME type
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            showEditorToast('Định dạng hình ảnh không hợp lệ. Vui lòng chọn JPG, PNG, WEBP hoặc GIF.', 'danger');
            fileInput.value = '';
            return;
        }

        // Loading state on button
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
                const altText = file.name.replace(/\.[^/.]+$/, '').trim() || 'Hình ảnh bài viết';
                editor.chain().focus().setImage({
                    src: data.url,
                    alt: altText,
                    title: altText,
                }).run();
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

function showEditorToast(message, type = 'info') {
    const toastContainer = document.getElementById('global-toast-container');
    if (!toastContainer) {
        alert(message);
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
