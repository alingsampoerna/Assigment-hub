/* CampusCare - interaksi halaman Login dan Register */
document.addEventListener('DOMContentLoaded', function () {

  // 1. Jika ada ringkasan error, pindahkan fokus ke sana
  //    agar pengguna keyboard dan pembaca layar langsung tahu ada kesalahan.
  var summary = document.getElementById('ringkasan-error');
  if (summary) {
    summary.focus();
  }

  // 2. Tombol tampilkan/sembunyikan password.
  //    Label tombol tetap; perubahan keadaan diumumkan lewat aria-pressed.
  document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
    button.addEventListener('click', function () {
      var input = document.querySelector(button.getAttribute('data-toggle-password'));
      if (!input) { return; }

      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      button.setAttribute('aria-pressed', String(show));

      var icon = button.querySelector('i');
      if (icon) {
        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
      }
    });
  });

});