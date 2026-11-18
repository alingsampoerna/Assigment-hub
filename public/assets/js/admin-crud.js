/* CampusCare - interaksi halaman data master admin (Kategori, Petugas, Pengaturan) */
document.addEventListener('DOMContentLoaded', function () {

  // 1. Jika ada ringkasan error, pindahkan fokus ke sana.
  var summary = document.getElementById('ringkasan-error');
  if (summary) {
    summary.focus();
  }

  // 2. Modal konfirmasi hapus: isi dari atribut data-* tombol yang diklik.
  var modal = document.getElementById('modal-hapus');
  if (modal) {
    modal.addEventListener('show.bs.modal', function (event) {
      var trigger = event.relatedTarget;
      if (!trigger) { return; }

      var form = modal.querySelector('form');
      var nama = modal.querySelector('[data-hapus-nama]');
      var jenis = modal.querySelector('[data-hapus-jenis]');

      if (form) { form.setAttribute('action', trigger.getAttribute('data-action') || '#'); }
      if (nama) { nama.textContent = trigger.getAttribute('data-nama') || ''; }
      if (jenis) { jenis.textContent = trigger.getAttribute('data-jenis') || 'data'; }
    });
  }

});

