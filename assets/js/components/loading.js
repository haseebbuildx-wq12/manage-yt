window.Loading = {
  show(target) {
    const el = typeof target === 'string' ? document.getElementById(target) : target;
    if (!el || el.querySelector('.loading-overlay')) return;
    el.style.position = el.style.position || 'relative';
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.innerHTML = '<span class="spinner lg"></span>';
    el.appendChild(overlay);
  },
  hide(target) {
    const el = typeof target === 'string' ? document.getElementById(target) : target;
    el?.querySelector('.loading-overlay')?.remove();
  },
};
