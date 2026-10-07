# BlogMNM — Visual Design System & Theme Engine Specification

## 1. Design Direction & Philosophy

BlogMNM embraces a **Threads-inspired, content-first visual language**:
- **Monochrome Foundation**: Built on an uncompromising black/white palette (dark mode: pure black `#000000`; light mode: pure white `#ffffff`).
- **Centered Feed**: The central reading column is fixed at `max-w-[660px]`, mirroring modern social-content platforms to deliver optimal typographic line lengths (50–75 characters per line).
- **Minimal Borders**: Subtle 1px borders (`#27272a` in dark mode, `#e4e4e7` in light mode) separate feed items and modules cleanly without visual noise.
- **Inverted High-Contrast Pill Buttons**: Primary call-to-actions utilize inverted contrast (`bg-[var(--color-text)] text-[var(--color-bg)]`), creating crisp, striking buttons in both color schemes.

---

## 2. Design Token System (`resources/css/app.css`)

All color values and surface tokens are defined as CSS Custom Properties bound to the root `[data-theme]` attribute:

```css
@layer base {
  [data-theme="dark"] {
    --color-bg: #000000;
    --color-surface: #101010;
    --color-surface-hover: #18181b;
    --color-border: #27272a;
    --color-text: #ffffff;
    --color-text-secondary: #a1a1aa;
    --color-input: #18181b;
  }

  [data-theme="light"] {
    --color-bg: #ffffff;
    --color-surface: #f4f4f5;
    --color-surface-hover: #e4e4e7;
    --color-border: #e4e4e7;
    --color-text: #000000;
    --color-text-secondary: #71717a;
    --color-input: #f4f4f5;
  }

  body {
    background-color: var(--color-bg);
    color: var(--color-text);
  }
}
```

---

## 3. Zero-Flash Theme Initialization Engine

To eliminate any visual flash of unstyled content (FOUC), an inline synchronous execution script is placed in the `<head>` of `resources/views/layouts/app.blade.php`:

```html
<script>
    (function() {
        const storedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = storedTheme ? storedTheme : (systemPrefersDark ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', theme);
    })();
</script>
```

### Toggle Switch Logic
The toggle handler, located in `resources/views/components/sidebar.blade.php` and `resources/views/components/mobile-nav.blade.php`, toggles the attribute and writes to `localStorage`:

```javascript
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
    const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', nextTheme);
    localStorage.setItem('theme', nextTheme);
}
```

---

## 4. Typography & Spacing Hierarchy

| Element | Class / Token | Purpose |
| :--- | :--- | :--- |
| **Headline 1 (Post Title)** | `text-2xl font-bold tracking-tight text-[var(--color-text)]` | High-impact article headlines |
| **Headline 2 (Section)** | `text-lg font-semibold text-[var(--color-text)]` | Card headers and modal titles |
| **Body Text** | `text-sm leading-relaxed text-[var(--color-text)]` | Post reading body and comments |
| **Secondary Metadata** | `text-xs text-[var(--color-text-secondary)]` | Timestamps, read times, counters |
| **Inverted Pill Button** | `bg-[var(--color-text)] text-[var(--color-bg)] rounded-full px-4 py-2 font-medium` | Primary action buttons |
| **Surface Pill Button** | `bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-text)] rounded-full` | Secondary action buttons |

---

## 5. Responsive Grid & Viewport Scaling

1. **Desktop Viewport ($\ge 1280\text{px}$)**:
   - Left navigation sidebar: fixed `w-64` with icons and text labels.
   - Central reading column: `max-w-[660px]` centered in viewport.
   - Right context rail: `w-72` for trending topics and author recommendations.
2. **Tablet Viewport ($768\text{px} - 1023\text{px}$)**:
   - Sidebar collapses to compact icon-only column (`w-20`).
   - Feed column retains `max-w-[660px]`.
3. **Mobile Viewport ($\le 767\text{px}$)**:
   - Desktop sidebar and right rail completely hidden.
   - Minimal sticky top header displaying logo and search icon.
   - Fixed bottom navigation bar (`h-16`) with minimum $44\text{px} \times 44\text{px}$ touch targets.
