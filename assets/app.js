// Confirm before any destructive action (delete listing, etc.)
document.querySelectorAll('[data-confirm]').forEach(function (el) {
  el.addEventListener('click', function (e) {
    if (!confirm(el.getAttribute('data-confirm'))) {
      e.preventDefault();
    }
  });
});

// Auto-dismiss flash alerts after a few seconds.
document.querySelectorAll('.alert').forEach(function (el) {
  setTimeout(function () {
    el.style.opacity = '0';
    setTimeout(function () { el.remove(); }, 300);
  }, 4000);
});
