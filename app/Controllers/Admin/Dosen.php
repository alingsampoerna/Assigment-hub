<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

class Dosen extends BaseController
{
    private const PER_PAGE = 15;

    // ----------------------------------------------------------------
    // INDEX
    // ----------------------------------------------------------------

    public function index(): string
    {
        $model = new UserModel();

        $q      = mb_substr($this->strGet('q'), 0, 100);
        $status = $this->strGet('status');

        if (! in_array($status, ['aktif', 'nonaktif'], true)) {
            $status = '';
        }

        $builder = $model->where('role', 'dosen');
        if ($status !== '') $builder->where('status', $status);
        if ($q !== '') {
            $builder->groupStart()
                    ->like('nama', $q)
                    ->orLike('email', $q)
                    ->groupEnd();
        }

        $total = (int) (clone $builder)->countAllResults(false);
        $totalHalaman = max(1, (int) ceil($total / self::PER_PAGE));
        $halaman      = min(max((int) $this->strGet('page'), 1), $totalHalaman);

        $items  = $builder->orderBy('nama', 'ASC')
                          ->findAll(self::PER_PAGE, ($halaman - 1) * self::PER_PAGE);

        $filter = ['q' => $q, 'status' => $status];

        return view('pages/admin/dosen_index', [
            'title'      => 'Kelola Dosen',
            'activeNav'  => 'dosen',
            'items'      => $items,
            'total'      => $total,
            'perPage'    => self::PER_PAGE,
            'page'       => $halaman,
            'totalPages' => $totalHalaman,
            'filter'     => $filter,
            'query'      => array_filter($filter, static fn (string $v): bool => $v !== ''),
        ]);
    }

    // ----------------------------------------------------------------
    // CREATE
    // ----------------------------------------------------------------

    public function create(): string
    {
        return view('pages/admin/dosen_form', [
            'title'    => 'Tambah Dosen',
            'activeNav'=> 'dosen',
        ]);
    }

    public function store()
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
                'rules'  => ['required', 'valid_email', 'max_length[100]', 'is_unique[users.email]'],
                'errors' => [
                    'required'    => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.',
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
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->with('old', $this->request->getPost(['nama', 'email']));
        }

        try {
            (new UserModel())->insert([
                'nama'     => trim((string) $this->request->getPost('nama')),
                'email'    => strtolower(trim((string) $this->request->getPost('email'))),
                'password' => (string) $this->request->getPost('password'),
                'role'     => 'dosen',
                'status'   => 'aktif',
            ]);
        } catch (DatabaseException $e) {
            log_message('error', 'Gagal buat akun dosen: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal membuat akun. Email mungkin sudah terdaftar.')
                ->with('old', $this->request->getPost(['nama', 'email']));
        }

        return redirect()->to(site_url('admin/dosen'))
            ->with('success', 'Akun dosen berhasil dibuat.');
    }

    // ----------------------------------------------------------------
    // SHOW
    // ----------------------------------------------------------------

    public function show(int $id): string
    {
        $dosen = $this->cariAtau404($id);

        return view('pages/admin/dosen_show', [
            'title'     => 'Detail Dosen — ' . $dosen['nama'],
            'activeNav' => 'dosen',
            'dosen'     => $dosen,
        ]);
    }

    // ----------------------------------------------------------------
    // TOGGLE STATUS
    // ----------------------------------------------------------------

    public function updateStatus(int $id)
    {
        $dosen = $this->cariAtau404($id);
        $baru  = $dosen['status'] === 'aktif' ? 'nonaktif' : 'aktif';

        (new UserModel())->update($id, ['status' => $baru]);

        $pesan = $baru === 'aktif'
            ? esc($dosen['nama']) . ' berhasil diaktifkan.'
            : esc($dosen['nama']) . ' berhasil dinonaktifkan.';

        return redirect()->back()->with('success', $pesan);
    }

    // ----------------------------------------------------------------
    // RESET PASSWORD
    // ----------------------------------------------------------------

    public function resetPassword(int $id)
    {
        $dosen = $this->cariAtau404($id);

        $rules = [
            'password_baru' => [
                'label'  => 'Password baru',
                'rules'  => ['required', 'min_length[8]', 'max_length[72]', 'regex_match[/^(?=.*[A-Za-z])(?=.*[0-9]).+$/]'],
                'errors' => [
                    'required'    => 'Password baru wajib diisi.',
                    'min_length'  => 'Minimal 8 karakter.',
                    'max_length'  => 'Maksimal 72 karakter.',
                    'regex_match' => 'Harus mengandung huruf dan angka.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('admin/dosen/' . $id))
                ->with('errors', $this->validator->getErrors())
                ->with('form_error', 'password');
        }

        (new UserModel())->update($id, ['password' => (string) $this->request->getPost('password_baru')]);

        return redirect()->to(site_url('admin/dosen/' . $id))
            ->with('success', 'Password ' . esc($dosen['nama']) . ' berhasil direset.');
    }

    // ----------------------------------------------------------------
    // HELPER
    // ----------------------------------------------------------------

    private function cariAtau404(int $id): array
    {
        $dosen = (new UserModel())
            ->where('id', $id)
            ->where('role', 'dosen')
            ->first();

        if ($dosen === null) throw PageNotFoundException::forPageNotFound();

        return $dosen;
    }

    private function strGet(string $key): string
    {
        $v = $this->request->getGet($key);
        return is_string($v) ? trim($v) : '';
    }
}
