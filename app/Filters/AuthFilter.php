<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter akses: harus login, akunnya harus masih ada di database,
 * akunnya harus berstatus aktif, dan (opsional) harus berperan tertentu.
 *
 * Pemakaian di route:
 *   'filter' => 'auth'          (semua peran, asal aktif)
 *   'filter' => 'auth:admin'    (hanya admin)
 *   'filter' => 'auth:mahasiswa' (hanya mahasiswa)
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userId  = (int) $session->get('user_id');

        // Belum login
        if ($userId === 0) {
            return redirect()->to(site_url('login'))
                ->with('info', 'Silakan masuk terlebih dahulu.');
        }

        // Akun di session harus masih ada di database dan berstatus aktif
        $user = (new UserModel())
            ->select('id, nama, nim, email, role, status')
            ->find($userId);

        if ($user === null) {
            $session->remove(['user_id', 'nama', 'nim', 'email', 'role', 'status']);
            $session->regenerate(true);

            return redirect()->to(site_url('login'))
                ->with('error', 'Sesi Anda sudah tidak berlaku. Silakan masuk kembali.');
        }

        // Akun dinonaktifkan oleh admin
        if ($user['status'] !== 'aktif') {
            $session->remove(['user_id', 'nama', 'nim', 'email', 'role', 'status']);
            $session->regenerate(true);

            return redirect()->to(site_url('login'))
                ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi admin untuk informasi lebih lanjut.');
        }

        // Samakan data session dengan database (cegah session stale)
        $session->set([
            'nama'   => $user['nama'],
            'nim'    => $user['nim'],
            'email'  => $user['email'],
            'role'   => $user['role'],
            'status' => $user['status'],
        ]);

        // Peran tidak sesuai dengan halaman yang dibuka
        if (! empty($arguments) && ! in_array($user['role'], $arguments, true)) {
            $tujuan = match ($user['role']) {
                'admin'  => 'admin/dashboard',
                'dosen'  => 'dosen/dashboard',
                default  => 'mahasiswa/dashboard',
            };

            return redirect()->to(site_url($tujuan))
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Halaman yang butuh login tidak boleh disimpan di cache browser
        // (agar tombol Kembali setelah logout tidak menampilkan data lama).
        return $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
