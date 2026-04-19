document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('minimized');
        });
    }

    // Profile Dropdown Toggle
    const profileToggle = document.getElementById('profileToggle');
    const profileDropdown = document.getElementById('profileDropdown');

    if (profileToggle && profileDropdown) {
        profileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
        });

        document.addEventListener('click', function(e) {
            if (!profileToggle.contains(e.target)) {
                profileDropdown.classList.remove('active');
            }
        });
    }

    // Modal Alert System (Center)
    window.showModal = function(message, type = 'error', customTitle = null, hint = null) {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        
        const title = customTitle || (type === 'success' ? 'Berhasil!' : 'Terjadi Kesalahan');
        const icon = type === 'success' 
            ? '<div class="modal-icon icon-success"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg></div>'
            : '<div class="modal-icon icon-error"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>';

        modal.innerHTML = `
            <div class="modal-card">
                ${icon}
                <h3 class="modal-title">${title}</h3>
                <p class="modal-message">${message}</p>
                ${hint ? `<span class="modal-hint">${hint}</span>` : ''}
                <button class="modal-btn" onclick="this.closest('.modal-overlay').remove()">Mengerti</button>
            </div>
        `;

        document.body.appendChild(modal);

        // Fade in
        setTimeout(() => modal.classList.add('active'), 10);
    };

    // Toast Notification System (Corner)
    window.showToast = function(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const icon = type === 'success' 
            ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>'
            : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';

        toast.innerHTML = `
            ${icon}
            <div class="toast-content">${message}</div>
        `;

        container.appendChild(toast);

        // Auto remove
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    };
});
