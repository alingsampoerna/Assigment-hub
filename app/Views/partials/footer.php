<footer class="ah-footer">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-md-5">
                <p class="ah-brand-text mb-2">
                    <i class="bi bi-journal-bookmark-fill me-2" aria-hidden="true"></i><?= esc(cc_app_name()) ?>
                </p>
                <p class="ah-caption mb-0" style="color:#94A3B8;">
                    Platform pengelolaan dan pengumpulan tugas perkuliahan.
                </p>
            </div>
            <div class="col-md-7">
                <nav aria-label="Tautan footer">
                    <ul class="list-unstyled d-flex flex-wrap gap-4 mb-0 justify-content-md-end">
                        <li><a href="<?= base_url('/#fitur') ?>">Fitur</a></li>
                        <li><a href="<?= base_url('/#cara-kerja') ?>">Cara Kerja</a></li>
                        <li><a href="<?= site_url('login') ?>">Masuk</a></li>
                        <li><a href="<?= site_url('register') ?>">Daftar</a></li>
                    </ul>
                </nav>
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,0.12); margin:0 0 1.5rem;">
        <p class="ah-caption mb-0" style="color:#64748B;">
            &copy; <?= date('Y') ?> <?= esc(cc_app_name()) ?>. Seluruh hak dilindungi.
        </p>
    </div>
</footer>
