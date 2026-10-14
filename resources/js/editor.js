import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';

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
                    levels: [2, 3],
                },
                dropcursor: {
                    color: '#3b82f6',
                    width: 2,
                },
            }),
            Underline,
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

    const actionMap = {
        bold: () => editor.isActive('bold'),
        italic: () => editor.isActive('italic'),
        underline: () => editor.isActive('underline'),
        h2: () => editor.isActive('heading', { level: 2 }),
        h3: () => editor.isActive('heading', { level: 3 }),
        quote: () => editor.isActive('blockquote'),
        code: () => editor.isActive('codeBlock'),
        'bullet-list': () => editor.isActive('bulletList'),
        'ordered-list': () => editor.isActive('orderedList'),
        link: () => editor.isActive('link'),
    };

    document.querySelectorAll('[data-editor-action]').forEach((btn) => {
        const action = btn.getAttribute('data-editor-action');
        const checkFn = actionMap[action];
        if (checkFn) {
            btn.classList.toggle('active', Boolean(checkFn()));
        }
    });
}

function setupToolbar(editor) {
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
                case 'h2':
                    editor.chain().focus().toggleHeading({ level: 2 }).run();
                    break;
                case 'h3':
                    editor.chain().focus().toggleHeading({ level: 3 }).run();
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
                case 'hr':
                    editor.chain().focus().setHorizontalRule().run();
                    break;
                case 'link':
                    openLinkModal(editor);
                    break;
                case 'image':
                    triggerImageUpload();
                    break;
            }
        });
    });
}

function setupLinkModal(editor) {
    const modalEl = document.getElementById('editor-link-modal');
    if (!modalEl) return;

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
        } catch (error) {
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
    // If global notification system exists, use it
    const toastContainer = document.getElementById('global-toast-container');
    if (!toastContainer) {
        alert(message);
        return;
    }

    const toastId = 'toast-' + Math.random().toString(36).substring(2, 9);
    const bgClass = type === 'success' ? 'bg-success text-white' : type === 'danger' ? 'bg-danger text-white' : 'bg-dark text-white';

    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center ${bgClass} border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body small fw-medium">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    const toastEl = document.getElementById(toastId);
    if (window.bootstrap && window.bootstrap.Toast && toastEl) {
        const toast = new window.bootstrap.Toast(toastEl, { delay: 4000 });
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }
}

