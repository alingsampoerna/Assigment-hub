<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\KelasMahasiswaModel;
use App\Models\KelasModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Kelas extends BaseController
{
    public function index(): string
    {
        $kelasList = (new KelasModel())->daftar('', '', 'aktif', 100, 0);

        return view('pages/dosen/kelas_index', [
            'title'     => 'Daftar Kelas',
            'activeNav' => 'kelas',
            'kelasList' => $kelasList,
        ]);
    }

    public function show(int $id): string
    {
        $kelas = (new KelasModel())->findWithMK($id);

        if ($kelas === null) throw PageNotFoundException::forPageNotFound();

        $anggota = (new KelasMahasiswaModel())->anggotaKelas($id);

        return view('pages/dosen/kelas_show', [
            'title'     => 'Detail Kelas — ' . $kelas['nama_kelas'],
            'activeNav' => 'kelas',
            'kelas'     => $kelas,
            'anggota'   => $anggota,
        ]);
    }

    public function anggota(int $id): string
    {
        $kelas = (new KelasModel())->findWithMK($id);

        if ($kelas === null) throw PageNotFoundException::forPageNotFound();

        $anggota = (new KelasMahasiswaModel())->anggotaKelas($id);

        return view('pages/dosen/kelas_anggota', [
            'title'     => 'Anggota Kelas — ' . $kelas['nama_kelas'],
            'activeNav' => 'kelas',
            'kelas'     => $kelas,
            'anggota'   => $anggota,
        ]);
    }
}
