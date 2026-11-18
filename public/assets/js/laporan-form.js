/* CampusCare - interaksi form Buat Laporan */
document.addEventListener('DOMContentLoaded', function () {

  // 1. Jika ada ringkasan error, pindahkan fokus ke sana.
  var summary = document.getElementById('ringkasan-error');
  if (summary) {
    summary.focus();
  }

  // 2. Penghitung karakter.
  //    Server menghitung baris baru sebagai 2 karakter (\r\n), jadi di sini dihitung sama
  //    agar batas di layar dan di server konsisten. Pembaca layar hanya diberi tahu
  //    saat sisa karakter <= 50 dan setelah pengguna berhenti mengetik sejenak.
  document.querySelectorAll('[data-counter-for]').forEach(function (counter) {
    var field  = document.querySelector(counter.getAttribute('data-counter-for'));
    var status = document.getElementById(counter.id + '-status');
    var max    = parseInt(counter.getAttribute('data-max'), 10);
    var timer  = null;

    if (!field) { return; }

    function lengthOf(text) {
      return text.replace(/\r?\n/g, '\r\n').length;
    }

    function update(announce) {
      var length = lengthOf(field.value);
      var over   = length > max;

      counter.textContent = length + '/' + max + (over ? ' (melebihi batas)' : '');
      counter.classList.toggle('is-over', over);

      if (!status || !announce) { return; }

      clearTimeout(timer);
      timer = setTimeout(function () {
        var left = max - length;
        if (left < 0) {
          status.textContent = 'Melebihi batas ' + Math.abs(left) + ' karakter.';
        } else if (left <= 50) {
          status.textContent = 'Sisa ' + left + ' karakter.';
        } else {
          status.textContent = '';
        }
      }, 800);
    }

    field.addEventListener('input', function () { update(true); });
    update(false); // tampilan awal, tanpa pengumuman
  });

  // 3. Pemeriksaan dan pratinjau foto.
  var input       = document.getElementById('foto');
  var preview     = document.getElementById('foto-preview');
  var previewImg  = document.getElementById('foto-preview-img');
  var removeBtn   = document.getElementById('foto-remove');
  var clientError = document.getElementById('foto-client-error');

  if (input && preview && previewImg && removeBtn && clientError) {
    var allowedTypes = ['image/jpeg', 'image/png'];
    var maxBytes     = parseInt(input.getAttribute('data-max-bytes'), 10);
    var objectUrl    = null;

    function clearPreview() {
      if (objectUrl) {
        URL.revokeObjectURL(objectUrl);
        objectUrl = null;
      }
      previewImg.removeAttribute('src');
      preview.classList.add('d-none');
    }

    function clearError() {
      clientError.textContent = '';
      clientError.classList.add('d-none');
      input.classList.remove('is-invalid');
      input.removeAttribute('aria-invalid');
    }

    function showError(message) {
      clientError.innerHTML = '<i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i> ';
      var text = document.createElement('span');
      text.textContent = message;
      clientError.appendChild(text);
      clientError.classList.remove('d-none');
      input.classList.add('is-invalid');
      input.setAttribute('aria-invalid', 'true');
    }

    input.addEventListener('change', function () {
      clearError();
      clearPreview();

      // Sembunyikan pesan error dari server (sudah tidak relevan setelah memilih file baru)
      var serverError = document.getElementById('foto-error');
      if (serverError) { serverError.classList.add('d-none'); }

      var file = input.files[0];
      if (!file) { return; }

      if (allowedTypes.indexOf(file.type) === -1) {
        showError('Format foto harus JPG atau PNG.');
        input.value = '';
        return;
      }

      if (file.size > maxBytes) {
        var sizeMb = (file.size / 1048576).toFixed(1).replace('.', ',');
        showError('Ukuran foto maksimal ' + Math.round(maxBytes / 1048576) + ' MB. Ukuran file Anda ' + sizeMb + ' MB.');
        input.value = '';
        return;
      }

      objectUrl = URL.createObjectURL(file);
      previewImg.src = objectUrl;
      preview.classList.remove('d-none');
    });

    removeBtn.addEventListener('click', function () {
      input.value = '';
      clearError();
      clearPreview();
      input.focus();
    });
  }

  // 4. Cegah pengiriman ganda.
  var form = document.getElementById('form-laporan');
  if (form) {
    var submitBtn   = form.querySelector('button[type="submit"]');
    var submitLabel = form.querySelector('.cc-submit-label');
    var labelAwal   = submitLabel ? submitLabel.textContent : '';

    form.addEventListener('submit', function () {
      if (!submitBtn) { return; }
      submitBtn.disabled = true;
      submitBtn.setAttribute('aria-busy', 'true');
      if (submitLabel) { submitLabel.textContent = 'Mengirim...'; }
    });

    // Jika pengguna menekan tombol Kembali, kembalikan tombol ke keadaan awal
    window.addEventListener('pageshow', function (event) {
      if (event.persisted && submitBtn) {
        submitBtn.disabled = false;
        submitBtn.removeAttribute('aria-busy');
        if (submitLabel) { submitLabel.textContent = labelAwal; }
      }
    });
  }

});