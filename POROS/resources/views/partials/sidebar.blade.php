<aside class="sidebar">
    <div class="logo-section">
        <div class="logo-box">
            <div class="orange-icon">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/>
                    <line x1="6" y1="17" x2="18" y2="17"/>
                </svg>
            </div>
            <span class="logo-text-bold">POROS</span>
        </div>
        <div style="color: #0c1e35; cursor: pointer;" id="sidebarToggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/></svg>
        </div>
    </div>
    
    <nav class="nav-list">
        @php
            $role = Auth::user()->role->nama_role;
            $dashboardRoute = 'dashboard.superadmin';
            if($role == 'dapur') $dashboardRoute = 'dashboard.dapur';
            if($role == 'sekolah') $dashboardRoute = 'dashboard.sekolah';
        @endphp

        <!-- Dashboard Link -->
        <a href="{{ route($dashboardRoute) }}" class="nav-link {{ Request::is('dashboard/*admin') || Request::is('dashboard/dapur') || Request::is('dashboard/sekolah') && !Request::is('*/monitoring') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <span class="nav-text">Dashboard</span>
        </a>

        @if($role == 'super admin')
            <a href="{{ route('users.index') }}" class="nav-link {{ Request::routeIs('users.index') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span class="nav-text">Users Management</span>
            </a>
            <a href="{{ route('suppliers.index') }}" class="nav-link {{ Request::routeIs('suppliers.index') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                <span class="nav-text">Suppliers & Bidding</span>
            </a>
            <a href="{{ route('analytics.index') }}" class="nav-link {{ Request::routeIs('analytics.index') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
                <span class="nav-text">System Analytics</span>
            </a>
            <a href="{{ route('settings.index') }}" class="nav-link {{ Request::routeIs('settings.index') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                <span class="nav-text">System Settings</span>
            </a>
        @elseif($role == 'dapur')
            <a href="{{ route('dashboard.meal_planning') }}" class="nav-link {{ Request::routeIs('dashboard.meal_planning') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><line x1="6" y1="17" x2="18" y2="17"/></svg>
                <span class="nav-text">Meal Planning</span>
            </a>
            <a href="{{ route('inventory.index') }}" class="nav-link {{ Request::routeIs('inventory.index') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                <span class="nav-text">Inventory Management</span>
            </a>
            <a href="{{ route('deliveries.index') }}" class="nav-link {{ Request::routeIs('deliveries.index') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span class="nav-text">Logistics & Deliveries</span>
            </a>
        @elseif($role == 'sekolah')
            <a href="{{ route('dashboard.sekolah.monitoring') }}" class="nav-link {{ Request::routeIs('dashboard.sekolah.monitoring') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M3 21h18M3 10l9-7 9 7v11H3V10z"/><path d="M9 21V11h6v10"/></svg>
                <span class="nav-text">School Monitoring</span>
            </a>
        @endif
    </nav>
</aside>
