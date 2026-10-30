<x-app-layout>
    <x-slot name="header">
        <div class="macos-profile-header mx-auto d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" style="max-width: 780px;">
            <div>
                <h1 class="macos-title mb-1">
                    Cài đặt tài khoản
                </h1>
                <p class="macos-subtitle mb-0">
                    Quản lý thông tin tài khoản, bảo mật mật khẩu và giao diện hệ thống.
                </p>
            </div>

            <!-- macOS Native Segmented Control (Only 3 Tabs: Hồ sơ, Bảo mật, Giao diện) -->
            <div class="macos-segmented-bar" role="tablist" aria-label="Điều hướng cài đặt">
                <a href="#profile-info" class="macos-segmented-tab active" id="tab-macos-profile" role="tab">
                    Hồ sơ
                </a>
                <a href="#security" class="macos-segmented-tab" id="tab-macos-security" role="tab">
                    Bảo mật
                </a>
                <a href="#appearance" class="macos-segmented-tab" id="tab-macos-appearance" role="tab">
                    Giao diện
                </a>
            </div>
        </div>
    </x-slot>

    <!-- macOS System Settings Content Container (Centered 780px) -->
    <div class="macos-profile-page mx-auto pb-5" style="max-width: 780px;">
        <div class="d-flex flex-column gap-3.5">
            <!-- 1. Apple Account Style Profile Group -->
            <div class="macos-card" id="profile-info-group">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- 2. Password & Security Group -->
            <div class="macos-card" id="security-group">
                @include('profile.partials.update-password-form')
            </div>

            <!-- 3. Appearance Settings Group -->
            <div class="macos-card" id="appearance-group">
                @include('profile.partials.appearance-settings')
            </div>

            <!-- 4. Session & Danger Zone Group -->
            <div class="macos-card" id="account-actions-group">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>

    <!-- Smooth Tab Highlighting Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = [
                { id: 'profile-info-group', link: document.getElementById('tab-macos-profile') },
                { id: 'security-group', link: document.getElementById('tab-macos-security') },
                { id: 'appearance-group', link: document.getElementById('tab-macos-appearance') }
            ];

            function syncActiveTab() {
                const scrollPos = window.scrollY + 140;
                let activeSet = false;

                for (let i = tabs.length - 1; i >= 0; i--) {
                    const section = document.getElementById(tabs[i].id);
                    if (section && section.offsetTop <= scrollPos) {
                        tabs.forEach(t => t.link?.classList.remove('active'));
                        tabs[i].link?.classList.add('active');
                        activeSet = true;
                        break;
                    }
                }

                if (!activeSet && tabs[0].link) {
                    tabs[0].link.classList.add('active');
                }
            }

            window.addEventListener('scroll', syncActiveTab, { passive: true });

            tabs.forEach(tab => {
                tab.link?.addEventListener('click', (e) => {
                    e.preventDefault();
                    tabs.forEach(t => t.link?.classList.remove('active'));
                    tab.link.classList.add('active');
                    const target = document.getElementById(tab.id);
                    if (target) {
                        const targetY = target.getBoundingClientRect().top + window.pageYOffset - 90;
                        window.scrollTo({ top: targetY, behavior: 'smooth' });
                    }
                });
            });
        });
    </script>
</x-app-layout>
