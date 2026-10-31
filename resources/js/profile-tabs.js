const PROFILE_TAB_SELECTOR = '[data-profile-tab-target]';

function activateProfileTab(tab) {
    const profile = tab.closest('.threads-profile-shell');
    const target = tab.getAttribute('data-profile-tab-target');

    if (!profile || !target) {
        return;
    }

    profile.querySelectorAll(PROFILE_TAB_SELECTOR).forEach((profileTab) => {
        const isActive = profileTab === tab;
        profileTab.classList.toggle('active', isActive);
        profileTab.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });

    profile.querySelectorAll('[data-profile-panel]').forEach((panel) => {
        panel.hidden = panel.getAttribute('data-profile-panel') !== target;
    });
}

document.addEventListener('click', (event) => {
    const tab = event.target.closest(PROFILE_TAB_SELECTOR);

    if (tab) {
        activateProfileTab(tab);
    }
});
