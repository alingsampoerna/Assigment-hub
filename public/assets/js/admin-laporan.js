/* CampusCare - interaksi halaman Detail Laporan (admin) */
document.addEventListener('DOMContentLoaded', function () {

  // Jika penolakan gagal validasi, buka kembali modalnya dan pindahkan fokus ke ringkasan error.
  var modalEl = document.querySelector('.modal[data-auto-open]');

  if (modalEl && window.bootstrap) {
    modalEl.addEventListener('shown.bs.modal', function () {
      var summaryInModal = modalEl.querySelector('#ringkasan-error');
      if (summaryInModal) {
        summaryInModal.focus();
      }
    });
    window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
    return;
  }

  // Selain itu, fokuskan ringkasan error di form yang sedang tampil (jika ada).
  var summary = document.getElementById('ringkasan-error');
  if (summary) {
    summary.focus();
  }
});