<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Landing page
$routes->get('/', 'Home::index');

// ID numerik (maks 9 digit) — placeholder ini dipakai di seluruh route
$routes->addPlaceholder('id', '[0-9]{1,9}');

// ─── AUTENTIKASI ────────────────────────────────────────────────────────────
// Hanya untuk tamu: pengguna yang sudah login diarahkan ke dashboard-nya
$routes->group('', ['filter' => 'guest'], static function ($routes) {
    $routes->get('login',    'Auth::login');
    $routes->post('login',   'Auth::attemptLogin');
    $routes->get('register', 'Auth::register');
    $routes->post('register', 'Auth::attemptRegister');
});

// Logout (harus sedang login)
$routes->post('logout', 'Auth::logout', ['filter' => 'auth']);

// ─── AREA ADMIN ─────────────────────────────────────────────────────────────
$routes->group('admin', [
    'namespace' => 'App\Controllers\Admin',
    'filter'    => 'auth:admin',
], static function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // Mahasiswa
    $routes->get('mahasiswa',                   'Mahasiswa::index');
    $routes->get('mahasiswa/(:id)',              'Mahasiswa::show/$1');
    $routes->post('mahasiswa/(:id)/status',      'Mahasiswa::updateStatus/$1');

    // Dosen
    $routes->get('dosen',                        'Dosen::index');
    $routes->get('dosen/baru',                   'Dosen::create');
    $routes->post('dosen',                       'Dosen::store');
    $routes->get('dosen/(:id)',                  'Dosen::show/$1');
    $routes->post('dosen/(:id)/status',          'Dosen::updateStatus/$1');
    $routes->post('dosen/(:id)/reset-password',  'Dosen::resetPassword/$1');

    // Mata Kuliah
    $routes->get('mata-kuliah',                  'MataKuliah::index');
    $routes->get('mata-kuliah/baru',             'MataKuliah::create');
    $routes->post('mata-kuliah',                 'MataKuliah::store');
    $routes->get('mata-kuliah/(:id)/ubah',       'MataKuliah::edit/$1');
    $routes->post('mata-kuliah/(:id)',           'MataKuliah::update/$1');
    $routes->post('mata-kuliah/(:id)/hapus',     'MataKuliah::delete/$1');

    // Kelas
    $routes->get('kelas',                        'Kelas::index');
    $routes->get('kelas/baru',                   'Kelas::create');
    $routes->post('kelas',                       'Kelas::store');
    $routes->get('kelas/(:id)/ubah',             'Kelas::edit/$1');
    $routes->post('kelas/(:id)',                 'Kelas::update/$1');
    $routes->post('kelas/(:id)/hapus',           'Kelas::delete/$1');
    $routes->get('kelas/(:id)/anggota',          'Kelas::anggota/$1');
    $routes->post('kelas/(:id)/anggota',         'Kelas::tambahAnggota/$1');
    $routes->post('kelas/(:id)/anggota/(:id)/hapus', 'Kelas::hapusAnggota/$1/$2');

    // Tugas
    $routes->get('tugas',                        'Tugas::index');
    $routes->get('tugas/baru',                   'Tugas::create');
    $routes->post('tugas',                       'Tugas::store');
    $routes->get('tugas/(:id)',                  'Tugas::show/$1');
    $routes->get('tugas/(:id)/ubah',             'Tugas::edit/$1');
    $routes->post('tugas/(:id)',                 'Tugas::update/$1');
    $routes->post('tugas/(:id)/hapus',           'Tugas::delete/$1');
    $routes->get('tugas/(:id)/lampiran',         'Tugas::unduhLampiran/$1');

    // Pengumpulan (admin melihat dan menilai)
    $routes->get('tugas/(:id)/pengumpulan',      'Pengumpulan::indexByTugas/$1');
    $routes->get('pengumpulan/(:id)',            'Pengumpulan::show/$1');
    $routes->get('pengumpulan/(:id)/berkas',     'Pengumpulan::unduhBerkas/$1');
    $routes->post('pengumpulan/(:id)/nilai',     'Pengumpulan::simpanNilai/$1');

    // Notifikasi admin
    $routes->get('notifikasi',                   'Notifikasi::index');
    $routes->post('notifikasi/baca/(:id)',        'Notifikasi::tandaiBaca/$1');
    $routes->post('notifikasi/baca-semua',        'Notifikasi::bacaSemua');

    // Pengaturan
    $routes->get('pengaturan',                   'Pengaturan::index');
    $routes->post('pengaturan',                  'Pengaturan::updateAplikasi');
    $routes->post('pengaturan/profil',           'Pengaturan::updateProfil');
    $routes->post('pengaturan/password',         'Pengaturan::updatePassword');
});

// ─── AREA DOSEN ──────────────────────────────────────────────────────────────
$routes->group('dosen', [
    'namespace' => 'App\Controllers\Dosen',
    'filter'    => 'auth:dosen',
], static function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // Profil & password
    $routes->get('profil',           'Profil::index');
    $routes->post('profil',          'Profil::update');
    $routes->post('profil/password', 'Profil::updatePassword');

    // Mata kuliah yang diampu
    $routes->get('mata-kuliah',         'MataKuliah::index');
    $routes->get('mata-kuliah/(:id)',   'MataKuliah::show/$1');

    // Kelas yang diampu
    $routes->get('kelas',               'Kelas::index');
    $routes->get('kelas/(:id)',         'Kelas::show/$1');
    $routes->get('kelas/(:id)/anggota', 'Kelas::anggota/$1');

    // Tugas
    $routes->get('tugas',                    'Tugas::index');
    $routes->get('tugas/baru',               'Tugas::create');
    $routes->post('tugas',                   'Tugas::store');
    $routes->get('tugas/(:id)',              'Tugas::show/$1');
    $routes->get('tugas/(:id)/ubah',         'Tugas::edit/$1');
    $routes->post('tugas/(:id)',             'Tugas::update/$1');
    $routes->post('tugas/(:id)/hapus',       'Tugas::delete/$1');
    $routes->get('tugas/(:id)/lampiran',     'Tugas::unduhLampiran/$1');

    // Pengumpulan
    $routes->get('tugas/(:id)/pengumpulan',  'Pengumpulan::indexByTugas/$1');
    $routes->get('pengumpulan/(:id)',        'Pengumpulan::show/$1');
    $routes->get('pengumpulan/(:id)/berkas', 'Pengumpulan::unduhBerkas/$1');
    $routes->post('pengumpulan/(:id)/nilai', 'Pengumpulan::simpanNilai/$1');

    // Notifikasi
    $routes->get('notifikasi',              'Notifikasi::index');
    $routes->post('notifikasi/baca/(:id)',   'Notifikasi::tandaiBaca/$1');
    $routes->post('notifikasi/baca-semua',   'Notifikasi::bacaSemua');
});


$routes->group('mahasiswa', [
    'namespace' => 'App\Controllers\Mahasiswa',
    'filter'    => 'auth:mahasiswa',
], static function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // Profil
    $routes->get('profil',           'Profil::index');
    $routes->post('profil',          'Profil::update');
    $routes->post('profil/password', 'Profil::updatePassword');

    // Kelas yang diikuti
    $routes->get('kelas',         'Kelas::index');
    $routes->get('kelas/(:id)',   'Kelas::show/$1');

    // Daftar kelas (self-enroll)
    $routes->get('kelas/(:id)/daftar',   'Kelas::daftar/$1');
    $routes->post('kelas/(:id)/daftar',  'Kelas::konfirmasiDaftar/$1');
    $routes->post('kelas/(:id)/keluar',  'Kelas::keluar/$1');

    // Tugas
    $routes->get('tugas',              'Tugas::index');
    $routes->get('tugas/(:id)',        'Tugas::show/$1');
    $routes->get('tugas/(:id)/lampiran', 'Tugas::unduhLampiran/$1');

    // Pengumpulan
    $routes->post('tugas/(:id)/kumpul',          'Pengumpulan::store/$1');
    $routes->get('pengumpulan/(:id)',             'Pengumpulan::show/$1');
    $routes->get('pengumpulan/(:id)/berkas',      'Pengumpulan::unduhBerkas/$1');
    $routes->post('pengumpulan/(:id)/revisi',     'Pengumpulan::revisi/$1');
    $routes->get('pengumpulan/(:id)/riwayat',     'Pengumpulan::riwayat/$1');

    // Notifikasi
    $routes->get('notifikasi',                    'Notifikasi::index');
    $routes->post('notifikasi/baca/(:id)',         'Notifikasi::tandaiBaca/$1');
    $routes->post('notifikasi/baca-semua',         'Notifikasi::bacaSemua');
});
