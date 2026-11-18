<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter untuk halaman tamu (login dan register).
 * Pengguna yang sudah login diarahkan ke dashboard sesuai perannya.
 */
class GuestFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if ($session->get('user_id')) {
            $tujuan = match ($session->get('role')) {
                'admin' => 'admin/dashboard',
                'dosen' => 'dosen/dashboard',
                default => 'mahasiswa/dashboard',
            };

            return redirect()->to(site_url($tujuan));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}