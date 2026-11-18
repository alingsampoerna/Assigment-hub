<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use App\Models\UserModel;

class Pengaturan extends BaseController
{
    private const OPSI_UPLOAD = [1 => '1 MB', 2 => '2 MB', 3 => '3 MB', 5 => '5 MB', 10 => '10 MB'];

    public function index()
    {
        $admin = (new UserModel())->find($this->userId());

        if ($admin === null) {
            return $this->akunTidakAda();
        }

        $pengaturan = (new SettingModel())->semua();

        return view('pages/admin/pengaturan', [
            'title'      => 'Pengaturan - CampusCare Admin',
            'activeNav'  => 'pengaturan',
            'nilai'      => [
                'nama_aplikasi'  => $pengaturan['nama_aplikasi'],
                'maks_upload_mb' => (int) $pengaturan['maks_upload_mb'],
                'admin_nama'     => $admin['nama'],
                'admin_email'    => $admin['email'],
            ],
            'opsiUpload' => self::OPSI_UPLOAD,
        ]);
    }

    // ------------------------------------------------------------
    // PENGATURAN APLIKASI
    // ------------------------------------------------------------

    public function updateAplikasi()
    {
        $rules = [
            'nama_aplikasi' => [
                'label'  => 'Nama aplikasi',
                'rules'  => ['required', 'min_length[3]', 'max_length[40]', 'regex_match[/^[\p{L}\p{N}\s\-.]+$/u]'],
                'errors' => [
                    'required'    => 'Nama aplikasi wajib diisi.',
                    'min_length'  => 'Nama aplikasi minimal 3 karakter.',
                    'max_length'  => 'Nama aplikasi maksimal 40 karakter.',
                    'regex_match' => 'Nama aplikasi hanya boleh berisi huruf, angka, spasi, titik, dan tanda hubung.',
                ],
            ],
            'maks_upload_mb' => [
                'label'  => 'Batas ukuran upload',
                'rules'  => ['required', 'in_list[' . implode(',', array_keys(self::OPSI_UPLOAD)) . ']'],
                'errors' => [
                    'required' => 'Batas ukuran upload wajib dipilih.',
                    'in_list'  => 'Pilih batas ukuran yang tersedia pada daftar.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('admin/pengaturan'))
                ->with('errors', $this->validator->getErrors())
                ->with('old', $this->request->getPost(['nama_aplikasi', 'maks_upload_mb']))
                ->with('form_error', 'aplikasi');
        }

        (new SettingModel())->simpan([
            'nama_aplikasi'  => (string) preg_replace('/\s+/u', ' ', trim((string) $this->request->getPost('nama_aplikasi'))),
            'maks_upload_mb' => (int) $this->request->getPost('maks_upload_mb'),
        ]);

        return redirect()->to(site_url('admin/pengaturan'))
            ->with('success', 'Pengaturan aplikasi disimpan.');
    }

    // ------------------------------------------------------------
    // PROFIL ADMIN
    // ------------------------------------------------------------

    public function updateProfil()
    {
        $rules = [
            'admin_nama' => [
                'label'  => 'Nama admin',
                'rules'  => ['required', 'min_length[3]', 'max_length[100]', 'regex_match[/^[\p{L}\s.\'-]+$/u]'],
                'errors' => [
                    'required'    => 'Nama admin wajib diisi.',
                    'min_length'  => 'Nama minimal 3 karakter.',
                    'max_length'  => 'Nama maksimal 100 karakter.',
                    'regex_match' => 'Nama hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung.',
                ],
            ],
            'admin_email' => [
                'label'  => 'Email admin',
                'rules'  => ['required', 'valid_email', 'max_length[100]'],
                'errors' => [
                    'required'    => 'Email admin wajib diisi.',
                    'valid_email' => 'Format email tidak valid, contoh: admin@kampus.ac.id.',
                    'max_length'  => 'Email maksimal 100 karakter.',
                ],
            ],
        ];

        $kembali = site_url('admin/pengaturan');
        $old     = $this->request->getPost(['admin_nama', 'admin_email']);

        if (! $this->validate($rules)) {
            return redirect()->to($kembali)
                ->with('errors', $this->validator->getErrors())
                ->with('old', $old)
                ->with('form_error', 'profil');
        }

        $userId = $this->userId();
        $model  = new UserModel();
        $nama   = trim((string) $this->request->getPost('admin_nama'));
        $email  = strtolower(trim((string) $this->request->getPost('admin_email')));

        // Email tidak boleh dipakai akun lain (akun sendiri dikecualikan)
        $dipakai = $model->where('email', $email)->where('id !=', $userId)->first();
        if ($dipakai !== null) {
            return redirect()->to($kembali)
                ->with('errors', ['admin_email' => 'Email ini sudah dipakai akun lain.'])
                ->with('old', $old)
                ->with('form_error', 'profil');
        }

        $model->update($userId, ['nama' => $nama, 'email' => $email]);
        session()->set(['nama' => $nama, 'email' => $email]);

        return redirect()->to($kembali)->with('success', 'Profil admin diperbarui.');
    }

        // ------------------------------------------------------------
    // UBAH PASSWORD
    // ------------------------------------------------------------

    public function updatePassword()
    {
        $rules = [
            'password_lama' => [
                'label'  => 'Password lama',
                'rules'  => ['required', 'max_length[72]'],
                'errors' => [
                    'required'   => 'Password lama wajib diisi.',
                    'max_length' => 'Password maksimal 72 karakter.',
                ],
            ],
            'password_baru' => [
                'label'  => 'Password baru',
                'rules'  => ['required', 'min_length[8]', 'max_length[72]', 'regex_match[/^(?=.*[A-Za-z])(?=.*[0-9]).+$/]', 'differs[password_lama]'],
                'errors' => [
                    'required'    => 'Password baru wajib diisi.',
                    'min_length'  => 'Password baru minimal 8 karakter.',
                    'max_length'  => 'Password maksimal 72 karakter.',
                    'regex_match' => 'Password baru harus mengandung huruf dan angka.',
                    'differs'     => 'Password baru harus berbeda dari password lama.',
                ],
            ],
            'password_konfirmasi' => [
                'label'  => 'Konfirmasi password baru',
                'rules'  => ['required', 'matches[password_baru]'],
                'errors' => [
                    'required' => 'Konfirmasi password baru wajib diisi.',
                    'matches'  => 'Konfirmasi password tidak sama dengan password baru.',
                ],
            ],
        ];

        $kembali = site_url('admin/pengaturan');

        // Password tidak pernah dikirim balik ke form
        if (! $this->validate($rules)) {
            return redirect()->to($kembali)
                ->with('errors', $this->validator->getErrors())
                ->with('form_error', 'password');
        }

        $userId = $this->userId();
        $model  = new UserModel();
        $user   = $model->find($userId);

        if ($user === null) {
            return $this->akunTidakAda();
        }

        if (! password_verify((string) $this->request->getPost('password_lama'), $user['password'])) {
            return redirect()->to($kembali)
                ->with('errors', ['password_lama' => 'Password lama tidak sesuai.'])
                ->with('form_error', 'password');
        }

        // Dikirim sebagai teks biasa; di-hash otomatis oleh UserModel
        $model->update($userId, ['password' => (string) $this->request->getPost('password_baru')]);

        session()->regenerate(true); // ID session baru setelah perubahan kredensial

        return redirect()->to($kembali)->with('success', 'Password berhasil diubah.');
    }

    // ------------------------------------------------------------
    // BANTUAN
    // ------------------------------------------------------------

    private function akunTidakAda()
    {
        session()->remove(['user_id', 'nama', 'nim', 'email', 'role']);

        return redirect()->to(site_url('login'))
            ->with('error', 'Akun tidak ditemukan. Silakan masuk kembali.');
    }
}