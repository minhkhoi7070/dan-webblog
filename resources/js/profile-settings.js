import { BlogMNMTheme } from './theme';

const profileTabs = [
    { sectionId: 'profile-info-group', tabId: 'tab-macos-profile' },
    { sectionId: 'security-group', tabId: 'tab-macos-security' },
    { sectionId: 'appearance-group', tabId: 'tab-macos-appearance' },
    { sectionId: 'reading-group', tabId: 'tab-macos-reading' },
];

const strengthMeta = {
    1: { label: 'Yếu', color: '#FF3B30' },
    2: { label: 'Trung bình', color: '#FF9500' },
    3: { label: 'Tốt', color: '#007AFF' },
    4: { label: 'Rất an toàn', color: '#34C759' },
};

let initialized = false;

function syncActiveTab() {
    if (!document.querySelector('.macos-profile-page')) return;

    const tabs = profileTabs.map(({ sectionId, tabId }) => ({
        section: document.getElementById(sectionId),
        link: document.getElementById(tabId),
    })).filter(({ section, link }) => section && link);

    if (!tabs.length) return;

    const scrollPosition = window.scrollY + 140;
    const activeTab = [...tabs].reverse().find(({ section }) =>
        section.getBoundingClientRect().top + window.scrollY <= scrollPosition
    ) || tabs[0];

    tabs.forEach(({ link }) => link.classList.toggle('active', link === activeTab.link));
}

function syncAppearanceSettingsUI() {
    const cards = document.getElementById('theme-radio-cards');
    if (!cards) return;

    const currentMode = BlogMNMTheme.getMode();
    cards.querySelectorAll('input[name="theme_mode"]').forEach((input) => {
        const isCurrent = input.value === currentMode;
        input.checked = isCurrent;
        input.closest('.macos-appearance-tile')?.classList.toggle('active', isCurrent);
    });
}

function evaluatePasswordStrength(password) {
    let score = 0;
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
    if (/\d/.test(password)) score++;
    if (/[^a-zA-Z0-9]/.test(password)) score++;

    if (score <= 1) return 1;
    if (score === 2) return 2;
    if (score <= 4) return 3;
    return 4;
}

function syncPasswordStrength() {
    const passwordInput = document.getElementById('update_password_password');
    const strengthBox = document.getElementById('password-strength-box');
    if (!passwordInput || !strengthBox) return;

    const password = passwordInput.value;
    strengthBox.style.display = password ? 'block' : 'none';
    if (!password) return;

    const level = evaluatePasswordStrength(password);
    const meta = strengthMeta[level];
    const strengthLabel = document.getElementById('strength-label');
    if (strengthLabel) {
        strengthLabel.textContent = meta.label;
        strengthLabel.style.color = meta.color;
    }

    for (let index = 1; index <= 4; index++) {
        const bar = document.getElementById(`bar-${index}`);
        if (bar) {
            bar.style.backgroundColor = index <= level ? meta.color : 'rgba(0, 0, 0, 0.08)';
        }
    }
}

function syncPasswordMatch() {
    const passwordInput = document.getElementById('update_password_password');
    const confirmationInput = document.getElementById('update_password_password_confirmation');
    const matchHint = document.getElementById('password-match-hint');
    if (!passwordInput || !confirmationInput || !matchHint) return;

    if (!confirmationInput.value) {
        matchHint.style.display = 'none';
        return;
    }

    const matches = passwordInput.value === confirmationInput.value;
    matchHint.style.display = 'block';
    matchHint.textContent = matches ? '✓ Mật khẩu trùng khớp' : '✕ Mật khẩu chưa trùng khớp';
    matchHint.className = matches ? 'small mt-1 text-success fw-medium' : 'small mt-1 text-danger';
}

function hydrateProfileSettings() {
    if (!document.querySelector('.macos-profile-page')) return;

    syncActiveTab();
    syncAppearanceSettingsUI();
    syncPasswordStrength();
    syncPasswordMatch();

    const savedMessage = document.getElementById('password-saved-msg');
    if (savedMessage && !savedMessage.dataset.fadeScheduled) {
        savedMessage.dataset.fadeScheduled = 'true';
        setTimeout(() => {
            savedMessage.style.opacity = '0';
            setTimeout(() => savedMessage.remove(), 250);
        }, 2500);
    }
}

export function initProfileSettings() {
    if (initialized) return;
    initialized = true;

    document.addEventListener('click', (event) => {
        const link = event.target.closest('.macos-segmented-tab');
        if (!link || !document.querySelector('.macos-profile-page')) return;

        const tab = profileTabs.find(({ tabId }) => tabId === link.id);
        const section = tab ? document.getElementById(tab.sectionId) : null;
        if (!section) return;

        event.preventDefault();
        profileTabs.forEach(({ tabId }) => document.getElementById(tabId)?.classList.remove('active'));
        link.classList.add('active');
        const targetY = section.getBoundingClientRect().top + window.scrollY - 90;
        window.scrollTo({ top: targetY, behavior: 'smooth' });
    });

    document.addEventListener('change', (event) => {
        const input = event.target.closest('#theme-radio-cards input[name="theme_mode"]');
        if (input && ['light', 'dark', 'system'].includes(input.value)) {
            BlogMNMTheme.setTheme(input.value);
        }
    });

    document.addEventListener('input', (event) => {
        if (event.target.id === 'update_password_password') {
            syncPasswordStrength();
            syncPasswordMatch();
        } else if (event.target.id === 'update_password_password_confirmation') {
            syncPasswordMatch();
        }
    });

    document.addEventListener('blogmnm:page-navigated', hydrateProfileSettings);
    window.addEventListener('blogmnm:theme-changed', syncAppearanceSettingsUI);
    window.addEventListener('scroll', syncActiveTab, { passive: true });
    hydrateProfileSettings();
}
