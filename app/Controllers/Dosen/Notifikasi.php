<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\NotifikasiModel;

class Notifikasi extends BaseController
{
    private const PER_PAGE = 20;

    public function index(): string
    {
        $userId = $this->userId();
        $model  = new NotifikasiModel();

        $total        = $model->hitungTotal($userId);
        $totalHalaman = max(1, (int) ceil($total / self::PER_PAGE));
        $halaman      = min(max((int) ($this->request->getGet('page') ?? 1), 1), $totalHalaman);

        return view('pages/dosen/notifikasi_index', [
            'title'      => 'Notifikasi',
            'activeNav'  => 'notifikasi',
            'items'      => $model->daftarUser($userId, self::PER_PAGE, ($halaman - 1) * self::PER_PAGE),
            'total'      => $total,
            'belumBaca'  => $model->hitungBelumBaca($userId),
            'perPage'    => self::PER_PAGE,
            'page'       => $halaman,
            'totalPages' => $totalHalaman,
            'query'      => [],
        ]);
    }

    public function tandaiBaca(int $id)
    {
        (new NotifikasiModel())->tandaiBaca($id, $this->userId());
        return redirect()->back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function bacaSemua()
    {
        (new NotifikasiModel())->bacaSemua($this->userId());
        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }
}
