/* Confirmation dialog helper, built on top of window.Modal (modal.js). */
window.Confirm = {
  _resolve: null,
  ask(message, { title = 'Are you sure?', variant = 'danger', confirmLabel = 'Confirm' } = {}) {
    return new Promise((resolve) => {
      this._resolve = resolve;
      let el = document.getElementById('confirm-dialog');
      if (!el) {
        el = document.createElement('div');
        el.id = 'confirm-dialog';
        el.className = 'modal-backdrop';
        document.body.appendChild(el);
      }
      el.innerHTML = `
        <div class="modal confirm-dialog ${variant}">
          <h3 class="card-title">${title}</h3>
          <p class="text-muted">${message}</p>
          <div class="confirm-actions">
            <button type="button" class="btn btn-secondary" data-action="cancel">Cancel</button>
            <button type="button" class="btn ${variant === 'danger' ? 'btn-danger' : ''}" data-action="confirm">${confirmLabel}</button>
          </div>
        </div>`;
      el.classList.add('open');
      el.querySelector('[data-action="cancel"]').onclick = () => this._settle(el, false);
      el.querySelector('[data-action="confirm"]').onclick = () => this._settle(el, true);
    });
  },
  _settle(el, result) {
    el.classList.remove('open');
    if (this._resolve) this._resolve(result);
    this._resolve = null;
  },
};
