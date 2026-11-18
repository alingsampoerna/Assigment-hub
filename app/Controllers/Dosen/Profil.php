<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Profil extends BaseController
{
    public function index(): string
    {
        $user = (new UserModel())->find($this->userId());

        if ($user === null) return $this->akunTidakAda();

        return view('pages/dosen/profil', [
            'title'     => 'Profil Saya',
            'activeNav' => 'profil',
            'profil'    => [
                'nama'  => $user['nama'],
                'email' => $user['email'],
                'nim'   => $user['nim'],
            ],
        ]);
    }

    public function update()
    {
        $rules = [
            'nama' => [
                'label'  => 'Nama lengkap',
                'rules'  => ['required', 'min_length[3]', 'max_length[100]', 'regex_match[/^[\p{L}\s.\'-]+$/u]'],
                'errors' => [
                    'required'    => 'Nama wajib diisi.',
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
                    'valid_email' => 'Format email tidak valid.',
                    'max_length'  => 'Email maksimal 100 karakter.',
                ],
            ],
        ];

        $kembali = site_url('dosen/profil');
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

        $dipakai = $model->where('email', $email)->where('id !=', $userId)->first();
        if ($dipakai !== null) {
            return redirect()->to($kembali)
                ->with('errors', ['email' => 'Email ini sudah dipakai akun lain.'])
                ->with('old', $old)->with('form_error', 'profil');
        }

        $model->update($userId, ['nama' => $nama, 'email' => $email]);
        session()->set(['nama' => $nama, 'email' => $email]);

        return redirect()->to($kembali)->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $rules = [
            'password_lama' => [
                'label'  => 'Password lama',
                'rules'  => ['required', 'max_length[72]'],
                'errors' => ['required' => 'Password lama wajib diisi.', 'max_length' => 'Maksimal 72 karakter.'],
            ],
            'password_baru' => [
                'label'  => 'Password baru',
                'rules'  => ['required', 'min_length[8]', 'max_length[72]', 'regex_match[/^(?=.*[A-Za-z])(?=.*[0-9]).+$/]', 'differs[password_lama]'],
                'errors' => [
                    'required'    => 'Password baru wajib diisi.',
                    'min_length'  => 'Minimal 8 karakter.',
                    'max_length'  => 'Maksimal 72 karakter.',
                    'regex_match' => 'Harus mengandung huruf dan angka.',
                    'differs'     => 'Password baru harus berbeda dari password lama.',
                ],
            ],
            'password_konfirmasi' => [
                'label'  => 'Konfirmasi password baru',
                'rules'  => ['required', 'matches[password_baru]'],
                'errors' => ['required' => 'Konfirmasi wajib diisi.', 'matches' => 'Konfirmasi tidak cocok.'],
            ],
        ];

        $kembali = site_url('dosen/profil');

        if (! $this->validate($rules)) {
            return redirect()->to($kembali)
                ->with('errors', $this->validator->getErrors())
                ->with('form_error', 'password');
        }

        $user = (new UserModel())->find($this->userId());
        if ($user === null) return $this->akunTidakAda();

        if (! password_verify((string) $this->request->getPost('password_lama'), $user['password'])) {
            return redirect()->to($kembali)
                ->with('errors', ['password_lama' => 'Password lama tidak sesuai.'])
                ->with('form_error', 'password');
        }

        (new UserModel())->update($this->userId(), ['password' => (string) $this->request->getPost('password_baru')]);
        session()->regenerate(true);

        return redirect()->to($kembali)->with('success', 'Password berhasil diubah.');
    }

    private function akunTidakAda()
    {
        session()->remove(['user_id', 'nama', 'nim', 'email', 'role']);
        return redirect()->to(site_url('login'))->with('error', 'Akun tidak ditemukan.');
    }
}
