<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasMahasiswaModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Mahasiswa extends BaseController
{
    private const PER_PAGE = 15;

    public function index(): string
    {
        $model = new UserModel();

        $q      = mb_substr($this->strGet('q'), 0, 100);
        $status = $this->strGet('status');

        if (! in_array($status, ['aktif', 'nonaktif'], true)) {
            $status = '';
        }

        $total        = $model->hitungMahasiswa($status);
        $totalHalaman = max(1, (int) ceil($total / self::PER_PAGE));
        $halaman      = min(max((int) $this->strGet('page'), 1), $totalHalaman);

        $builder = $model->where('role', 'mahasiswa');
        if ($status !== '') {
            $builder->where('status', $status);
        }
        if ($q !== '') {
            $builder->groupStart()
                    ->like('nama', $q)
                    ->orLike('nim', $q)
                    ->orLike('email', $q)
                    ->groupEnd();
        }

        $items  = $builder->orderBy('nama', 'ASC')
                          ->findAll(self::PER_PAGE, ($halaman - 1) * self::PER_PAGE);

        $filter = ['q' => $q, 'status' => $status];

        return view('pages/admin/mahasiswa_index', [
            'title'      => 'Mahasiswa',
            'activeNav'  => 'mahasiswa',
            'items'      => $items,
            'total'      => $total,
            'perPage'    => self::PER_PAGE,
            'page'       => $halaman,
            'totalPages' => $totalHalaman,
            'filter'     => $filter,
            'query'      => array_filter($filter, static fn (string $v): bool => $v !== ''),
        ]);
    }

    public function show(int $id): string
    {
        $mhs = $this->cariAtau404($id);

        $kelasList = (new KelasMahasiswaModel())->kelasMahasiswa($id);

        return view('pages/admin/mahasiswa_show', [
            'title'     => 'Detail Mahasiswa — ' . $mhs['nama'],
            'activeNav' => 'mahasiswa',
            'mhs'       => $mhs,
            'kelasList' => $kelasList,
        ]);
    }

    public function updateStatus(int $id)
    {
        $mhs    = $this->cariAtau404($id);
        $baru   = $mhs['status'] === 'aktif' ? 'nonaktif' : 'aktif';

        (new UserModel())->update($id, ['status' => $baru]);

        $pesan = $baru === 'aktif'
            ? esc($mhs['nama']) . ' berhasil diaktifkan.'
            : esc($mhs['nama']) . ' berhasil dinonaktifkan.';

        return redirect()->back()->with('success', $pesan);
    }

    private function cariAtau404(int $id): array
    {
        $mhs = (new UserModel())
            ->where('id', $id)
            ->where('role', 'mahasiswa')
            ->first();

        if ($mhs === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $mhs;
    }

    private function strGet(string $key): string
    {
        $v = $this->request->getGet($key);
        return is_string($v) ? trim($v) : '';
    }
}
