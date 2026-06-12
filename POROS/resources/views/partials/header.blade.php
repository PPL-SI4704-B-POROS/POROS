<header class="header">
    <h2 style="font-weight: 700; font-size: 1.1rem; color: #0c1e35; margin: 0; flex-grow: 1;">Food Supply Chain Management System</h2>

    <div style="display: flex; align-items: center; gap: 1.5rem;">
        @if(auth()->user()->role->nama_role === 'super admin')
            {{-- Super Admin: klik lonceng langsung ke halaman pengumuman + badge jumlah --}}
            @php $jumlahPengumuman = \App\Models\Pengumuman::count(); @endphp
            <a href="{{ route('pengumuman.index') }}" style="color: #0c1e35; position: relative; text-decoration: none;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                @if($jumlahPengumuman > 0)
                    <span style="
                        position: absolute;
                        top: -6px;
                        right: -6px;
                        background: #ff6b00;
                        color: white;
                        font-size: 10px;
                        font-weight: 700;
                        min-width: 16px;
                        height: 16px;
                        border-radius: 999px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        padding: 0 3px;
                        line-height: 1;
                        border: 2px solid white;
                    ">{{ $jumlahPengumuman > 99 ? '99+' : $jumlahPengumuman }}</span>
                @endif
            </a>
        @else
            {{-- User lain: klik lonceng muncul popup pengumuman --}}
            <div style="color: #0c1e35; cursor: pointer; position: relative;" onclick="togglePopupPengumuman()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                {{-- Popup Pengumuman --}}
                <div id="popupPengumuman" style="display:none; position:absolute; top:36px; right:0; width:340px; background:white; border-radius:16px; box-shadow:0 8px 32px rgba(0,0,0,0.15); z-index:9999; overflow:hidden;">
                    {{-- Header popup --}}
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid #f1f5f9;">
                        <span style="font-weight:700; font-size:15px; color:#0c1e35;">Pengumuman</span>
                        <span onclick="togglePopupPengumuman()" style="cursor:pointer; font-size:18px; color:#94a3b8; line-height:1;">&times;</span>
                    </div>
                    {{-- Isi pengumuman --}}
                    <div style="max-height:360px; overflow-y:auto; padding:8px 0;">
                        @php
                            $pengumumanList = \App\Models\Pengumuman::with('pembuat')->latest()->take(5)->get();
                        @endphp
                        @forelse($pengumumanList as $item)
                        <div style="padding:14px 20px; border-bottom:1px solid #f8fafc;">
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                                <div style="width:36px; height:36px; border-radius:50%; background:#ff6b00; display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:13px; flex-shrink:0;">
                                    {{ strtoupper(substr($item->pembuat->nama_lengkap, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-weight:700; font-size:13px; color:#0c1e35;">{{ $item->judul }}</div>
                                    <div style="font-size:11px; color:#94a3b8;">{{ $item->pembuat->nama_lengkap }} &bull; {{ $item->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <p style="font-size:13px; color:#475569; line-height:1.5; margin:0; padding-left:46px;">{{ Str::limit($item->isi, 100) }}</p>
                        </div>
                        @empty
                        <div style="padding:24px; text-align:center; color:#94a3b8; font-size:13px;">
                            Belum ada pengumuman yang dibuat.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
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
<script>
function togglePopupPengumuman() {
    event.stopPropagation();
    var popup = document.getElementById('popupPengumuman');
    popup.style.display = popup.style.display === 'none' ? 'block' : 'none';
}
// Tutup popup kalau klik di luar
document.addEventListener('click', function(e) {
    var popup = document.getElementById('popupPengumuman');
    if (popup && !popup.closest('div').contains(e.target)) {
        popup.style.display = 'none';
    }
});
</script>