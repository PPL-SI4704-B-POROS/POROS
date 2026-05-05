<header class="header">
    <h2 style="font-weight: 700; font-size: 1.1rem; color: #0c1e35;">Food Supply Chain Management System</h2>
    
    <div style="display: flex; align-items: center; gap: 1.5rem;">
        <!-- Notification Icon -->
        <div style="color: #0c1e35; cursor: pointer; position: relative;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
        </div>

        <!-- User Profile Dropdown -->
        <div class="user-profile" id="profileToggle">
            <div style="background: #ff6b00; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <div class="user-info-detail">
                <div style="font-weight: 700; font-size: 0.9375rem; color: #0c1e35;">{{ Auth::user()->nama_lengkap }}</div>
                <div class="role-pill">
                    {{ ucwords(Auth::user()->role->nama_role ?? 'User') }}
                </div>
            </div>
            <div style="color: #0c1e35; margin-left: 0.25rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>

            <!-- Dropdown Menu -->
            <div class="dropdown-menu" id="profileDropdown">
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Edit Profile
                </a>
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                    <button type="submit" class="dropdown-item" style="color: #ef4444;">
                        <svg viewBox="0 0 24 24" style="stroke: #ef4444;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
