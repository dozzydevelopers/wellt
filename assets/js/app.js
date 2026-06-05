// Welthflow - Frontend JS

function setSidebarState(isOpen) {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  if (sidebar) sidebar.classList.toggle('open', isOpen);
  if (overlay) overlay.classList.toggle('visible', isOpen);
  document.body.classList.toggle('sidebar-open', isOpen);
}

function closeSidebar() {
  setSidebarState(false);
}

function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  if (!sidebar) return;

  setSidebarState(!sidebar.classList.contains('open'));
}

window.toggleSidebar = toggleSidebar;
window.closeSidebar = closeSidebar;
window.setSidebarState = setSidebarState;

function toggleAdminSidebar() {
  const sidebar = document.getElementById('adminSidebar');
  const overlay = document.getElementById('adminOverlay');
  if (sidebar) {
    sidebar.classList.toggle('open');
    if (overlay) overlay.classList.toggle('visible');
  }
}

function toggleUserMenu() {
  const menu = document.getElementById('userMenu');
  if (menu) menu.classList.toggle('open');
}

// Close user menu on outside click
document.addEventListener('click', function (e) {
  const menu = document.getElementById('userMenu');
  const btn = document.querySelector('.avatar-btn');

  if (menu && !menu.contains(e.target) && btn && !btn.contains(e.target)) {
    menu.classList.remove('open');
  }

  if (e.target.closest('.sidebar-nav a')) {
    closeSidebar();
  }
});

window.addEventListener('resize', function () {
  if (window.innerWidth > 900) {
    closeSidebar();
  }
});

document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') closeSidebar();
});

// Auto-dismiss alerts
setTimeout(function () {
  document.querySelectorAll('.alert').forEach(function (el) {
    el.style.transition = 'opacity .5s';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 500);
  });
}, 5000);

// Confirm forms with data-confirm attribute
document.querySelectorAll('[data-confirm]').forEach(function (btn) {
  btn.addEventListener('click', function (e) {
    if (!confirm(this.dataset.confirm)) e.preventDefault();
  });
});

// Format numbers
function formatMoney(n) {
  return '$' + parseFloat(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Highlight active mobile nav
(function () {
  const path = window.location.pathname.split('/').pop();
  document.querySelectorAll('.mobile-nav-item').forEach(function (item) {
    if (item.getAttribute('href') && item.getAttribute('href').endsWith(path)) {
      item.classList.add('active');
    }
  });
})();
