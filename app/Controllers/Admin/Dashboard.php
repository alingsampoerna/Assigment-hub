<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\MataKuliahModel;
use App\Models\NotifikasiModel;
use App\Models\PengumpulanModel;
use App\Models\TugasModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $tugasModel      = new TugasModel();
        $pengumpulanModel = new PengumpulanModel();
        $userModel       = new UserModel();

        // Statistik ringkas
        $totalTugas     = (int) $tugasModel->where('status', 'aktif')->countAllResults();
        $totalMahasiswa = (int) $userModel->where('role', 'mahasiswa')->where('status', 'aktif')->countAllResults();
        $totalKelas     = (int) (new KelasModel())->where('status', 'aktif')->countAllResults();
        $totalMK        = (int) (new MataKuliahModel())->where('status', 'aktif')->countAllResults();

        // Tugas aktif dengan deadline terdekat
        $tugasAktif = $tugasModel->daftar('', '', 'aktif', 5, 0);

        // Notifikasi belum dibaca
        $belumBaca = (new NotifikasiModel())->hitungBelumBaca($this->userId());

        // Pengumpulan terbaru (5 terakhir)
        $pengumpulanTerbaru = $this->pengumpulanTerbaru(5);

        return view('pages/admin/dashboard', [
            'title'              => 'Dashboard Admin',
            'activeNav'          => 'dashboard',
            'totalTugas'         => $totalTugas,
            'totalMahasiswa'     => $totalMahasiswa,
            'totalKelas'         => $totalKelas,
            'totalMK'            => $totalMK,
            'tugasAktif'         => $tugasAktif,
            'belumBaca'          => $belumBaca,
            'pengumpulanTerbaru' => $pengumpulanTerbaru,
        ]);
    }

    private function pengumpulanTerbaru(int $limit): array
    {
        return \Config\Database::connect()
            ->table('pengumpulan p')
            ->select('p.id, p.tanggal_pengumpulan, p.status AS status_pengumpulan,
                      u.nama, u.nim, t.judul AS judul_tugas, t.id AS tugas_id')
            ->join('users u', 'u.id = p.mahasiswa_id')
            ->join('tugas t', 't.id = p.tugas_id')
            ->orderBy('p.created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }
}