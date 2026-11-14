// Shared Dashboard JavaScript Client
const API_BASE = '/api';

// ── THEME MANAGER ───────────────────────────────────────────────────────────
function initTheme() {
  const isDark = localStorage.getItem('cm_theme') === 'dark' ||
    (!('cm_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
  if (isDark) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
}

function toggleTheme() {
  const isDark = document.documentElement.classList.toggle('dark');
  localStorage.setItem('cm_theme', isDark ? 'dark' : 'light');
}

// ── TOAST NOTIFICATIONS ─────────────────────────────────────────────────────
function showToast(message, type = 'info') {
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    document.body.appendChild(container);
  }

  const icons = {
    success: 'bi-check-circle-fill text-emerald-500',
    error: 'bi-exclamation-octagon-fill text-rose-500',
    info: 'bi-info-circle-fill text-blue-500',
    warning: 'bi-exclamation-triangle-fill text-amber-500'
  };

  const borders = {
    success: 'border-emerald-200 dark:border-emerald-800 bg-emerald-50/95 dark:bg-emerald-950/90 text-emerald-900 dark:text-emerald-100',
    error: 'border-rose-200 dark:border-rose-800 bg-rose-50/95 dark:bg-rose-950/90 text-rose-900 dark:text-rose-100',
    info: 'border-blue-200 dark:border-blue-800 bg-blue-50/95 dark:bg-blue-950/90 text-blue-900 dark:text-blue-100',
    warning: 'border-amber-200 dark:border-amber-800 bg-amber-50/95 dark:bg-amber-950/90 text-amber-900 dark:text-amber-100'
  };

  const toast = document.createElement('div');
  toast.className = `toast-item flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border text-sm font-medium ${borders[type] || borders.info}`;
  toast.innerHTML = `
    <i class="bi ${icons[type] || icons.info} text-lg"></i>
    <span class="flex-1">${message}</span>
    <button onclick="this.parentElement.remove()" class="text-xs opacity-60 hover:opacity-100 ml-2">
      <i class="bi bi-x-lg"></i>
    </button>
  `;

  container.appendChild(toast);
  requestAnimationFrame(() => toast.classList.add('show'));

  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 300);
  }, 4500);
}

// ── API HELPER ──────────────────────────────────────────────────────────────
async function api(endpoint, options = {}) {
  try {
    const res = await fetch(`${API_BASE}${endpoint}`, {
      headers: {
        'Content-Type': 'application/json',
        ...(options.headers || {})
      },
      ...options
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      throw new Error(data.error || `HTTP error ${res.status}`);
    }
    return data;
  } catch (err) {
    console.error(`API [${endpoint}] failed:`, err);
    throw err;
  }
}

// ── FETCH PLATFORM HEALTH STATUS ────────────────────────────────────────────
async function loadPlatformStatus() {
  try {
    const res = await api('/settings');
    const { pinterest, instagram } = res.connections || {};

    const pBadge = document.getElementById('pinterestStatusBadge');
    if (pBadge) {
      if (pinterest?.connected) {
        pBadge.innerHTML = `<span class="w-2 h-2 rounded-full bg-emerald-500"></span><span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">UniPost Connected</span>`;
      } else {
        pBadge.innerHTML = `<span class="w-2 h-2 rounded-full bg-amber-500"></span><span class="text-xs font-semibold text-amber-700 dark:text-amber-400">Pinterest Offline</span>`;
      }
    }

    const igBadge = document.getElementById('instagramStatusBadge');
    if (igBadge) {
      if (instagram?.connected) {
        igBadge.innerHTML = `<span class="w-2 h-2 rounded-full bg-emerald-500"></span><span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">Instagram Connected</span>`;
      } else {
        igBadge.innerHTML = `<span class="w-2 h-2 rounded-full bg-slate-400"></span><span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Instagram (Not Set)</span>`;
      }
    }
  } catch (err) {
    console.warn('Could not load settings:', err.message);
  }
}

// ── COPY TO CLIPBOARD ───────────────────────────────────────────────────────
function copyText(text, label = 'Copied') {
  navigator.clipboard.writeText(text).then(() => {
    showToast(`${label} to clipboard!`, 'success');
  }).catch(() => {
    showToast('Failed to copy', 'error');
  });
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
  initTheme();
  loadPlatformStatus();
});
