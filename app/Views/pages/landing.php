<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- ===== HERO ===== -->
<section class="ah-hero" id="beranda" aria-labelledby="judul-hero">
    <div class="container">
        <div class="row align-items-center g-5">

            <div class="col-lg-6">
                <span class="ah-hero__eyebrow">Platform Tugas Akademik</span>

                <h1 id="judul-hero" class="ah-hero__title">
                    Kelola dan kumpulkan tugas perkuliahan dengan mudah.
                </h1>

                <p class="ah-hero__lead mt-3">
                    <?= esc(cc_app_name()) ?> membantu mahasiswa mengumpulkan tugas tepat waktu
                    dan dosen mengelola, memeriksa, serta memberikan nilai secara terorganisir.
                </p>

                <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                    <?php $role = cc_user_role(); ?>

                    <?php if ($role === 'admin'): ?>
                        <a class="btn btn-light btn-lg" href="<?= site_url('admin/dashboard') ?>">
                            <i class="bi bi-speedometer2" aria-hidden="true"></i>
                            Buka Dashboard Admin
                        </a>

                    <?php elseif ($role === 'mahasiswa'): ?>
                        <a class="btn btn-light btn-lg" href="<?= site_url('mahasiswa/tugas') ?>">
                            <i class="bi bi-journal-text" aria-hidden="true"></i>
                            Lihat Tugas Saya
                        </a>
                        <a class="btn btn-outline-primary btn-lg" href="<?= site_url('mahasiswa/dashboard') ?>"
                           style="--bs-btn-color:#fff;--bs-btn-border-color:rgba(255,255,255,0.5);--bs-btn-hover-bg:rgba(255,255,255,0.12);--bs-btn-hover-color:#fff;">
                            Dashboard
                        </a>

                    <?php else: ?>
                        <a class="btn btn-light btn-lg" href="<?= site_url('register') ?>">
                            <i class="bi bi-person-plus" aria-hidden="true"></i>
                            Daftar Sekarang
                        </a>
                        <a class="btn btn-outline-primary btn-lg" href="<?= site_url('login') ?>"
                           style="--bs-btn-color:#fff;--bs-btn-border-color:rgba(255,255,255,0.5);--bs-btn-hover-bg:rgba(255,255,255,0.12);--bs-btn-hover-color:#fff;">
                            Masuk
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Mock card -->
            <div class="col-lg-6">
                <div class="ah-mock" aria-hidden="true">
                    <p class="ah-mock__label">Tugas Aktif</p>
                    <p class="ah-mock__title">Laporan Praktikum Pemrograman Web</p>

                    <div class="ah-mock__deadline">
                        <i class="bi bi-clock-fill"></i>
                        Deadline: Senin, 25 Nov 2026 — 23:59
                    </div>

                    <div class="ah-mock__progress-label">
                        <span>Pengumpulan</span>
                        <span>24 / 35 mahasiswa</span>
                    </div>
                    <div class="ah-mock__bar">
                        <div class="ah-mock__bar-fill"></div>
                    </div>

                    <div class="ah-mock__chips">
                        <span class="ah-badge ah-badge--submitted">
                            <i class="bi bi-check-circle-fill"></i> Sudah Dikumpulkan
                        </span>
                        <span class="ah-badge ah-badge--graded">
                            <i class="bi bi-star-fill"></i> Dinilai
                        </span>
                        <span class="ah-badge ah-badge--late">
                            <i class="bi bi-exclamation-circle-fill"></i> Terlambat
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ===== FITUR UTAMA ===== -->
<section class="ah-section ah-section--white" id="fitur" aria-labelledby="judul-fitur">
    <div class="container">

        <div class="text-center mb-5">
            <span class="ah-section__label">Kenapa Assignment Hub?</span>
            <h2 id="judul-fitur" class="ah-section__title">Semua yang Anda butuhkan dalam satu platform</h2>
            <p class="ah-section__intro mx-auto">
                Dirancang untuk mempermudah proses akademik — dari pembuatan tugas hingga penilaian akhir.
            </p>
        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="ah-feature-card">
                    <span class="ah-feature-icon">
                        <i class="bi bi-journal-plus" aria-hidden="true"></i>
                    </span>
                    <h3>Buat & Kelola Tugas</h3>
                    <p class="text-muted-ah">
                        Admin membuat tugas, menentukan deadline, melampirkan berkas instruksi,
                        dan mendistribusikan ke kelas yang dituju.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="ah-feature-card">
                    <span class="ah-feature-icon">
                        <i class="bi bi-cloud-arrow-up" aria-hidden="true"></i>
                    </span>
                    <h3>Kumpul Tugas Online</h3>
                    <p class="text-muted-ah">
                        Mahasiswa mengumpulkan berkas (PDF, DOCX, ZIP, dsb.) langsung dari browser.
                        Sistem mendeteksi keterlambatan secara otomatis.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="ah-feature-card">
                    <span class="ah-feature-icon">
                        <i class="bi bi-patch-check" aria-hidden="true"></i>
                    </span>
                    <h3>Nilai & Feedback</h3>
                    <p class="text-muted-ah">
                        Admin mengunduh, memeriksa berkas, memberikan nilai dan komentar.
                        Mahasiswa langsung mendapatkan notifikasi.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="ah-feature-card">
                    <span class="ah-feature-icon">
                        <i class="bi bi-clock-history" aria-hidden="true"></i>
                    </span>
                    <h3>Riwayat Pengumpulan</h3>
                    <p class="text-muted-ah">
                        Setiap revisi pengumpulan tercatat. Tidak ada perubahan yang luput dari sistem.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="ah-feature-card">
                    <span class="ah-feature-icon">
                        <i class="bi bi-bell" aria-hidden="true"></i>
                    </span>
                    <h3>Notifikasi Real-time</h3>
                    <p class="text-muted-ah">
                        Pengingat deadline, pemberitahuan tugas baru, dan informasi penilaian langsung
                        muncul di dashboard.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="ah-feature-card">
                    <span class="ah-feature-icon">
                        <i class="bi bi-shield-lock" aria-hidden="true"></i>
                    </span>
                    <h3>Aman & Terlindungi</h3>
                    <p class="text-muted-ah">
                        Berkas tugas disimpan di area aman. Mahasiswa hanya bisa mengakses
                        data miliknya sendiri.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ===== CARA KERJA ===== -->
<section class="ah-section" id="cara-kerja" aria-labelledby="judul-cara-kerja">
    <div class="container">

        <div class="text-center mb-5">
            <span class="ah-section__label">Alur Kerja</span>
            <h2 id="judul-cara-kerja" class="ah-section__title">Tiga langkah, dari tugas hingga nilai</h2>
        </div>

        <div class="row g-4 justify-content-center">

            <div class="col-md-4">
                <div class="ah-step">
                    <span class="ah-step__number">1</span>
                    <div class="ah-step__content">
                        <h3>Admin Buat Tugas</h3>
                        <p>Tentukan judul, instruksi, lampiran, deadline, dan kelas penerima tugas.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="ah-step">
                    <span class="ah-step__number">2</span>
                    <div class="ah-step__content">
                        <h3>Mahasiswa Kumpul</h3>
                        <p>Mahasiswa membaca instruksi, mengunduh lampiran, lalu mengunggah berkas jawaban.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="ah-step">
                    <span class="ah-step__number">3</span>
                    <div class="ah-step__content">
                        <h3>Admin Nilai</h3>
                        <p>Admin mengunduh berkas, memberikan nilai dan komentar. Mahasiswa mendapatkan notifikasi.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ===== CTA ===== -->
<section class="ah-cta" aria-labelledby="judul-cta">
    <div class="container">
        <h2 id="judul-cta" class="ah-section__title" style="color:#fff;">
            Siap memulai semester yang lebih terorganisir?
        </h2>
        <p class="mt-2 mb-4">
            Daftar gratis dan mulai kelola tugas perkuliahan Anda hari ini.
        </p>

        <?php if (cc_user_role() === null): ?>
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                <a class="btn btn-light btn-lg" href="<?= site_url('register') ?>">
                    <i class="bi bi-person-plus" aria-hidden="true"></i>
                    Daftar Mahasiswa
                </a>
                <a class="btn btn-outline-primary btn-lg" href="<?= site_url('login') ?>"
                   style="--bs-btn-color:#fff;--bs-btn-border-color:rgba(255,255,255,0.5);--bs-btn-hover-bg:rgba(255,255,255,0.12);--bs-btn-hover-color:#fff;">
                    Sudah punya akun? Masuk
                </a>
            </div>
        <?php else: ?>
            <a class="btn btn-light btn-lg" href="<?= cc_dashboard_url() ?>">
                <i class="bi bi-speedometer2" aria-hidden="true"></i>
                Buka Dashboard
            </a>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
