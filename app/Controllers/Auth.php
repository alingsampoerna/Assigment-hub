<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Auth extends BaseController
{
    // ------------------------------------------------------------
    // LOGIN
    // ------------------------------------------------------------

    public function login(): string
    {
        return view('pages/auth/login', [
            'title'     => 'Masuk - Assignment Hub',
            'activeNav' => 'login',
        ]);
    }

    public function attemptLogin()
    {
        $rules = [
            'email' => [
                'label'  => 'Email',
                'rules'  => ['required', 'valid_email', 'max_length[100]'],
                'errors' => [
                    'required'    => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid, contoh: nama@kampus.ac.id.',
                    'max_length'  => 'Email maksimal 100 karakter.',
                ],
            ],
            'password' => [
                'label'  => 'Password',
                'rules'  => ['required', 'max_length[72]'],
                'errors' => [
                    'required'   => 'Password wajib diisi.',
                    'max_length' => 'Password maksimal 72 karakter.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('login'))
                ->with('errors', $this->validator->getErrors())
                ->with('old', $this->request->getPost(['email']));
        }

        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');

        // Pembatasan percobaan: 5 kali per menit per alamat IP
        $throttler = service('throttler');
        if ($throttler->check('login_' . md5($this->request->getIPAddress()), 5, MINUTE) === false) {
            $tunggu = max(1, (int) $throttler->getTokenTime());

            return redirect()->to(site_url('login'))
                ->with('error', 'Terlalu banyak percobaan masuk. Coba lagi dalam ' . $tunggu . ' detik.')
                ->with('old', ['email' => $email]);
        }

        $user = (new UserModel())->cariByEmail($email);

        if ($user === null) {
            // Tetap lakukan pekerjaan hash agar waktu respons tidak membocorkan
            // apakah email tersebut terdaftar.
            password_hash($password, PASSWORD_DEFAULT);
            $valid = false;
        } else {
            $valid = password_verify($password, $user['password']);
        }

        if (! $valid) {
            // Pesan sengaja seragam: tidak membedakan "email tidak ada" dan "password salah"
            return redirect()->to(site_url('login'))
                ->with('error', 'Email atau password salah.')
                ->with('old', ['email' => $email]);
        }

        // Berhasil: perbarui ID session (cegah session fixation), lalu simpan data pengguna
        $session = session();
        $session->regenerate(true);
        $session->set([
            'user_id' => (int) $user['id'],
            'nama'    => $user['nama'],
            'nim'     => $user['nim'],
            'email'   => $user['email'],
            'role'    => $user['role'],
        ]);

        return redirect()->to(site_url($user['role'] === 'admin' ? 'admin/dashboard' : ($user['role'] === 'dosen' ? 'dosen/dashboard' : 'mahasiswa/dashboard')));
    }

    // ------------------------------------------------------------
    // REGISTER
    // ------------------------------------------------------------

    public function register(): string
    {
        return view('pages/auth/register', [
            'title'     => 'Daftar - Assignment Hub',
            'activeNav' => 'register',
        ]);
    }

    public function attemptRegister()
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
            'nim' => [
                'label'  => 'NIM',
                'rules'  => ['required', 'regex_match[/^[0-9]{8,15}$/]', 'is_unique[users.nim]'],
                'errors' => [
                    'required'    => 'NIM wajib diisi.',
                    'regex_match' => 'NIM harus berupa angka sepanjang 8 sampai 15 digit.',
                    'is_unique'   => 'NIM ini sudah terdaftar.',
                ],
            ],
            'email' => [
                'label'  => 'Email',
                'rules'  => ['required', 'valid_email', 'max_length[100]', 'is_unique[users.email]'],
                'errors' => [
                    'required'    => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid, contoh: nama@kampus.ac.id.',
                    'max_length'  => 'Email maksimal 100 karakter.',
                    'is_unique'   => 'Email ini sudah terdaftar.',
                ],
            ],
            'password' => [
                'label'  => 'Password',
                'rules'  => ['required', 'min_length[8]', 'max_length[72]', 'regex_match[/^(?=.*[A-Za-z])(?=.*[0-9]).+$/]'],
                'errors' => [
                    'required'    => 'Password wajib diisi.',
                    'min_length'  => 'Password minimal 8 karakter.',
                    'max_length'  => 'Password maksimal 72 karakter.',
                    'regex_match' => 'Password harus mengandung huruf dan angka.',
                ],
            ],
            'password_confirm' => [
                'label'  => 'Konfirmasi password',
                'rules'  => ['required', 'matches[password]'],
                'errors' => [
                    'required' => 'Konfirmasi password wajib diisi.',
                    'matches'  => 'Konfirmasi password tidak sama dengan password.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('register'))
                ->with('errors', $this->validator->getErrors())
                ->with('old', $this->request->getPost(['nama', 'nim', 'email'])); // tanpa password
        }

        // Pembatasan pendaftaran: 5 kali per menit per alamat IP (cegah pendaftaran massal)
        if (service('throttler')->check('register_' . md5($this->request->getIPAddress()), 5, MINUTE) === false) {
            return redirect()->to(site_url('register'))
                ->with('error', 'Terlalu banyak percobaan pendaftaran. Coba lagi sebentar lagi.')
                ->with('old', $this->request->getPost(['nama', 'nim', 'email']));
        }

        $old = $this->request->getPost(['nama', 'nim', 'email']);

        try {
            // Peran SELALU ditentukan di sini, tidak pernah dibaca dari form.
            $id = (new UserModel())->insert([
                'nama'     => trim((string) $this->request->getPost('nama')),
                'nim'      => trim((string) $this->request->getPost('nim')),
                'email'    => strtolower(trim((string) $this->request->getPost('email'))),
                'password' => (string) $this->request->getPost('password'),   // di-hash oleh UserModel
                'role'     => 'mahasiswa',
            ]);
        } catch (DatabaseException $e) {
            // Kemungkinan: email/NIM yang sama didaftarkan bersamaan (dilindungi UNIQUE di database)
            log_message('error', 'Registrasi gagal: ' . $e->getMessage());
            $id = false;
        }

        if ($id === false) {
            return redirect()->to(site_url('register'))
                ->with('error', 'Pendaftaran gagal. Email atau NIM mungkin sudah terdaftar. Silakan coba lagi.')
                ->with('old', $old);
        }

        return redirect()->to(site_url('login'))
            ->with('success', 'Registrasi berhasil. Silakan masuk dengan akun Anda.');
    }

    // ------------------------------------------------------------
    // LOGOUT
    // ------------------------------------------------------------

    public function logout()
    {
        $session = session();

        // Hapus data pengguna (dan sisa data demo untuk halaman admin yang belum pindah ke database)
        $session->remove(['user_id', 'nama', 'nim', 'email', 'role']);
        $session->regenerate(true);
        $session->regenerate(true);

        return redirect()->to(site_url('login'))
            ->with('info', 'Anda telah keluar.');}
}