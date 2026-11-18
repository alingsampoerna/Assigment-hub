<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\MataKuliahModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class MataKuliah extends BaseController
{
    public function index(): string
    {
        $items = (new MataKuliahModel())->daftarAktif();

        return view('pages/dosen/mata_kuliah_index', [
            'title'     => 'Mata Kuliah',
            'activeNav' => 'mata-kuliah',
            'items'     => $items,
        ]);
    }

    public function show(int $id): string
    {
        $item = (new MataKuliahModel())->find($id);

        if ($item === null) throw PageNotFoundException::forPageNotFound();

        return view('pages/dosen/mata_kuliah_show', [
            'title'     => 'Detail Mata Kuliah — ' . $item['nama_mata_kuliah'],
            'activeNav' => 'mata-kuliah',
            'item'      => $item,
        ]);
    }
}
