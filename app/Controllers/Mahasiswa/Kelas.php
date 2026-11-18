<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\KelasMahasiswaModel;
use App\Models\KelasModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Kelas extends BaseController
{
    /**
     * Kelas yang sudah diikuti + semua kelas aktif yang bisa didaftar.
     */
    public function index(): string
    {
        $userId  = $this->userId();
        $model   = new KelasMahasiswaModel();

        $q = mb_substr($this->strGet('q'), 0, 100);

        $kelasSaya    = $model->kelasMahasiswa($userId);
        $kelasTersedia = $model->kelasBelumDiikuti($userId, $q);

        return view('pages/mahasiswa/kelas_index', [
            'title'         => 'Kelas',
            'activeNav'     => 'kelas',
            'kelasSaya'     => $kelasSaya,
            'kelasTersedia' => $kelasTersedia,
            'q'             => $q,
        ]);
    }

    /**
     * Detail kelas — mahasiswa hanya boleh lihat kelas yang diikutinya.
     */
    public function show(int $id): string
    {
        $userId = $this->userId();

        $terdaftar = (new KelasMahasiswaModel())
            ->where('kelas_id', $id)
            ->where('mahasiswa_id', $userId)
            ->first();

        if ($terdaftar === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $kelas   = (new KelasModel())->findWithMK($id);
        $anggota = (new KelasMahasiswaModel())->anggotaKelas($id);

        if ($kelas === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('pages/mahasiswa/kelas_show', [
            'title'     => 'Detail Kelas — ' . $kelas['nama_kelas'],
            'activeNav' => 'kelas',
            'kelas'     => $kelas,
            'anggota'   => $anggota,
        ]);
    }

    /**
     * Halaman konfirmasi sebelum mendaftar kelas.
     */
    public function daftar(int $id): string
    {
        $userId = $this->userId();

        $kelas = (new KelasModel())->findWithMK($id);
        if ($kelas === null || $kelas['status'] !== 'aktif') {
            throw PageNotFoundException::forPageNotFound();
        }

        // Sudah terdaftar
        $sudah = (new KelasMahasiswaModel())
            ->where('kelas_id', $id)
            ->where('mahasiswa_id', $userId)
            ->first();

        if ($sudah !== null) {
            return redirect()->to(site_url('mahasiswa/kelas'))
                ->with('error', 'Anda sudah terdaftar di kelas ini.');
        }

        return view('pages/mahasiswa/kelas_daftar', [
            'title'     => 'Daftar Kelas — ' . $kelas['nama_kelas'],
            'activeNav' => 'kelas',
            'kelas'     => $kelas,
        ]);
    }

    /**
     * Proses pendaftaran kelas oleh mahasiswa.
     */
    public function konfirmasiDaftar(int $id)
    {
        $userId = $this->userId();

        $kelas = (new KelasModel())->findWithMK($id);
        if ($kelas === null || $kelas['status'] !== 'aktif') {
            throw PageNotFoundException::forPageNotFound();
        }

        $model    = new KelasMahasiswaModel();
        $berhasil = $model->tambahAnggota($id, $userId);

        if (! $berhasil) {
            return redirect()->to(site_url('mahasiswa/kelas'))
                ->with('error', 'Anda sudah terdaftar di kelas ini.');
        }

        return redirect()->to(site_url('mahasiswa/kelas/' . $id))
            ->with('success', 'Berhasil mendaftar ke kelas ' . $kelas['nama_kelas'] . '.');
    }

    /**
     * Keluar dari kelas.
     */
    public function keluar(int $id)
    {
        $userId = $this->userId();

        $row = (new KelasMahasiswaModel())
            ->where('kelas_id', $id)
            ->where('mahasiswa_id', $userId)
            ->first();

        if ($row === null) {
            return redirect()->to(site_url('mahasiswa/kelas'))
                ->with('error', 'Anda tidak terdaftar di kelas ini.');
        }

        (new KelasMahasiswaModel())->hapusAnggota((int) $row['id'], $id);

        return redirect()->to(site_url('mahasiswa/kelas'))
            ->with('success', 'Anda telah keluar dari kelas.');
    }

    private function strGet(string $key): string
    {
        $v = $this->request->getGet($key);
        return is_string($v) ? trim($v) : '';
    }
}
