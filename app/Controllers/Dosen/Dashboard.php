<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\MataKuliahModel;
use App\Models\NotifikasiModel;
use App\Models\TugasModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $userId = $this->userId();

        // Statistik tugas milik dosen ini
        $totalTugas = (int) (new TugasModel())
            ->where('dibuat_oleh', $userId)
            ->where('status', 'aktif')
            ->countAllResults();

        $totalKelas = (int) (new KelasModel())
            ->where('status', 'aktif')
            ->countAllResults();

        $totalMK = (int) (new MataKuliahModel())
            ->where('status', 'aktif')
            ->countAllResults();

        // Tugas aktif buatan dosen ini (deadline terdekat)
        $tugasAktif = (new TugasModel())->daftar('', '', 'aktif', 5, 0);
        // Filter hanya tugas milik dosen ini
        $tugasAktif = array_filter($tugasAktif, fn($t) => (int)$t['dibuat_oleh'] === $userId);

        $belumBaca = (new NotifikasiModel())->hitungBelumBaca($userId);

        // Pengumpulan terbaru dari tugas-tugas dosen ini
        $pengumpulanTerbaru = $this->pengumpulanTerbaru($userId, 5);

        return view('pages/dosen/dashboard', [
            'title'              => 'Dashboard Dosen',
            'activeNav'          => 'dashboard',
            'totalTugas'         => $totalTugas,
            'totalKelas'         => $totalKelas,
            'totalMK'            => $totalMK,
            'tugasAktif'         => array_values($tugasAktif),
            'belumBaca'          => $belumBaca,
            'pengumpulanTerbaru' => $pengumpulanTerbaru,
        ]);
    }

    private function pengumpulanTerbaru(int $dosenId, int $limit): array
    {
        return \Config\Database::connect()
            ->table('pengumpulan p')
            ->select('p.id, p.tanggal_pengumpulan, p.status AS status_pengumpulan,
                      u.nama, u.nim, t.judul AS judul_tugas, t.id AS tugas_id')
            ->join('users u', 'u.id = p.mahasiswa_id')
            ->join('tugas t', 't.id = p.tugas_id')
            ->where('t.dibuat_oleh', $dosenId)
            ->orderBy('p.created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }
}
