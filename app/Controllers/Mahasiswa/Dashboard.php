<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\NotifikasiModel;
use App\Models\PengumpulanModel;
use App\Models\TugasModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $userId = $this->userId();

        $nama      = trim((string) session()->get('nama'));
        $namaDepan = $nama !== '' ? explode(' ', $nama)[0] : 'Mahasiswa';

        $stats       = (new PengumpulanModel())->statistikMahasiswa($userId);
        $tugasAktif  = (new PengumpulanModel())->tugasAktifMahasiswa($userId, 5);
        $belumBaca   = (new NotifikasiModel())->hitungBelumBaca($userId);

        // Hitung tugas yang belum dikumpulkan dan deadline-nya masih ada
        $tugasBelumKumpul = (int) \Config\Database::connect()
            ->table('tugas t')
            ->join('tugas_kelas tk', 'tk.tugas_id = t.id')
            ->join('kelas_mahasiswa km', 'km.kelas_id = tk.kelas_id AND km.mahasiswa_id = ' . $userId)
            ->join('pengumpulan p', 'p.tugas_id = t.id AND p.mahasiswa_id = ' . $userId, 'left')
            ->where('t.status', 'aktif')
            ->where('t.tanggal_mulai <=', date('Y-m-d H:i:s'))
            ->where('p.id IS NULL')
            ->countAllResults();

        return view('pages/mahasiswa/dashboard', [
            'title'            => 'Dashboard',
            'activeNav'        => 'dashboard',
            'namaDepan'        => $namaDepan,
            'stats'            => $stats,
            'tugasAktif'       => $tugasAktif,
            'belumBaca'        => $belumBaca,
            'tugasBelumKumpul' => $tugasBelumKumpul,
        ]);
    }
}