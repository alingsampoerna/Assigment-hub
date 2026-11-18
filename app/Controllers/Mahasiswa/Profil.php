<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Profil extends BaseController
{
    // ------------------------------------------------------------
    // TAMPILKAN PROFIL
    // ------------------------------------------------------------

    public function index()
    {
        $user = (new UserModel())->find($this->userId());

        // Akun sudah tidak ada di database tetapi session masih ada
        if ($user === null) {
            return $this->akunTidakAda();
        }

        return view('pages/mahasiswa/profil', [
            'title'     => 'Profil - CampusCare',
            'activeNav' => 'profil',
            'profil'    => [
                'nama'  => $user['nama'],
                'nim'   => $user['nim'],
                'email' => $user['email'],
            ],
        ]);
    }

    // ------------------------------------------------------------
    // UBAH DATA PROFIL (nama dan email)
    // ------------------------------------------------------------

    public function update()
    {
        $rules = [
            'nama' => [
                'label'  => 'Nama lengkap',
                'rules'  => ['required', 'min_length[3]', 'max_length[100]', 'regex_match[/^[\p{L}\s.\'-]+$/u]'],
                'errors' => [
                    'required'    => 'Nama lengkap wajib diisi.',
                    'min_length'  => 'Nama minimal 3 karakter.',
                    'max_length'  => 'Nama maksimal 100 karakter.',
                    'regex_match' => 'Nama hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung.',
                ],
            ],
            'email' => [
                'label'  => 'Email',
                'rules'  => ['required', 'valid_email', 'max_length[100]'],
                'errors' => [
                    'required'    => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid, contoh: nama@kampus.ac.id.',
                    'max_length'  => 'Email maksimal 100 karakter.',
                ],
            ],
        ];

        $kembali = site_url('mahasiswa/profil');
        $old     = $this->request->getPost(['nama', 'email']);

        if (! $this->validate($rules)) {
            return redirect()->to($kembali)
                ->with('errors', $this->validator->getErrors())
                ->with('old', $old)
                ->with('form_error', 'profil');
        }

        $userId = $this->userId();
        $model  = new UserModel();
        $nama   = trim((string) $this->request->getPost('nama'));
        $email  = strtolower(trim((string) $this->request->getPost('email')));

        // Email tidak boleh dipakai akun lain (akun sendiri dikecualikan)
        $dipakai = $model->where('email', $email)->where('id !=', $userId)->first();
        if ($dipakai !== null) {
            return redirect()->to($kembali)
                ->with('errors', ['email' => 'Email ini sudah dipakai akun lain.'])
                ->with('old', $old)
                ->with('form_error', 'profil');
        }

        $model->update($userId, ['nama' => $nama, 'email' => $email]);

        // Samakan data di session agar sapaan dan data lain langsung berubah
        session()->set(['nama' => $nama, 'email' => $email]);

        return redirect()->to($kembali)->with('success', 'Profil berhasil diperbarui.');
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

        $kembali = site_url('mahasiswa/profil');

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