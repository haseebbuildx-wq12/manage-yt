window.Tabs = {
  init(container) {
    const root = typeof container === 'string' ? document.getElementById(container) : container;
    if (!root) return;
    root.querySelectorAll('.tab').forEach((tab) => {
      tab.addEventListener('click', () => {
        const target = tab.dataset.tabTarget;
        root.querySelectorAll('.tab').forEach((t) => t.classList.remove('active'));
        root.querySelectorAll('.tab-panel').forEach((p) => p.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById(target)?.classList.add('active');
      });
    });
  },
};
