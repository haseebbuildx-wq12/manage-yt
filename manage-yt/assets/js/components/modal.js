window.Modal = {
  open(id) { document.getElementById(id)?.classList.add('open'); },
  close(id) { document.getElementById(id)?.classList.remove('open'); },
};