<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OfficerModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

class Petugas extends BaseController
{
    // ------------------------------------------------------------
    // DAFTAR
    // ------------------------------------------------------------

    public function index(): string
    {
        return view('pages/admin/petugas_index', [
            'title'     => 'Kelola Petugas - CampusCare Admin',
            'activeNav' => 'petugas',
            'items'     => (new OfficerModel())->denganStatistik(),
        ]);
    }

    // ------------------------------------------------------------
    // TAMBAH
    // ------------------------------------------------------------

    public function create(): string
    {
        return view('pages/admin/petugas_form', [
            'title'     => 'Tambah Petugas - CampusCare Admin',
            'activeNav' => 'petugas',
            'mode'      => 'tambah',
            'item'      => null,
        ]);
    }

    public function store()
    {
        $kembali = site_url('admin/petugas/baru');

        if (! $this->validate($this->aturan())) {
            return $this->kembaliDenganError($kembali, $this->validator->getErrors());
        }

        $model = new OfficerModel();
        $nama  = $this->bersihkan((string) $this->request->getPost('nama'));

        if ($model->namaSudahAda($nama)) {
            return $this->kembaliDenganError($kembali, ['nama' => 'Petugas dengan nama tersebut sudah ada.']);
        }

        try {
            $model->insert([
                'nama'   => $nama,
                'bidang' => $this->bersihkan((string) $this->request->getPost('bidang')),
                'kontak' => trim((string) $this->request->getPost('kontak')),
            ]);
        } catch (DatabaseException $e) {
            log_message('error', 'Gagal menambah petugas: ' . $e->getMessage());

            return $this->kembaliDenganError($kembali, ['nama' => 'Petugas dengan nama tersebut sudah ada.']);
        }

        return redirect()->to(site_url('admin/petugas'))
            ->with('success', 'Petugas "' . $nama . '" berhasil ditambahkan.');
    }

    // ------------------------------------------------------------
    // UBAH
    // ------------------------------------------------------------

    public function edit(int $id): string
    {
        $petugas = (new OfficerModel())->find($id);

        if ($petugas === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('pages/admin/petugas_form', [
            'title'     => 'Ubah Petugas - CampusCare Admin',
            'activeNav' => 'petugas',
            'mode'      => 'ubah',
            'item'      => $petugas,
        ]);
    }

    public function update(int $id)
    {
        $model = new OfficerModel();

        if ($model->find($id) === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $kembali = site_url('admin/petugas/' . $id . '/ubah');

        if (! $this->validate($this->aturan())) {
            return $this->kembaliDenganError($kembali, $this->validator->getErrors());
        }

        $nama = $this->bersihkan((string) $this->request->getPost('nama'));

        if ($model->namaSudahAda($nama, $id)) {
            return $this->kembaliDenganError($kembali, ['nama' => 'Petugas dengan nama tersebut sudah ada.']);
        }

        try {
            $model->update($id, [
                'nama'   => $nama,
                'bidang' => $this->bersihkan((string) $this->request->getPost('bidang')),
                'kontak' => trim((string) $this->request->getPost('kontak')),
            ]);
        } catch (DatabaseException $e) {
            log_message('error', 'Gagal mengubah petugas: ' . $e->getMessage());

            return $this->kembaliDenganError($kembali, ['nama' => 'Petugas dengan nama tersebut sudah ada.']);
        }

        return redirect()->to(site_url('admin/petugas'))
            ->with('success', 'Data petugas "' . $nama . '" berhasil diubah.');
    }

    // ------------------------------------------------------------
    // HAPUS
    // ------------------------------------------------------------

    public function delete(int $id)
    {
        $model   = new OfficerModel();
        $petugas = $model->find($id);

        if ($petugas === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $tujuan = site_url('admin/petugas');
        $total  = $model->jumlahPenugasan($id);

        // Pesan ramah dari controller; foreign key RESTRICT pada report_assignments
        // menjadi jaring pengaman.
        if ($total > 0) {
            return redirect()->to($tujuan)
                ->with('error', 'Petugas "' . $petugas['nama'] . '" tidak dapat dihapus karena pernah ditugaskan pada ' . $total . ' laporan.');
        }

        try {
            $model->delete($id);
        } catch (DatabaseException $e) {
            log_message('error', 'Gagal menghapus petugas: ' . $e->getMessage());

            return redirect()->to($tujuan)
                ->with('error', 'Petugas "' . $petugas['nama'] . '" tidak dapat dihapus karena sedang dipakai.');
        }

        return redirect()->to($tujuan)
            ->with('success', 'Petugas "' . $petugas['nama'] . '" berhasil dihapus.');
    }

    // ------------------------------------------------------------
    // BANTUAN
    // ------------------------------------------------------------

    private function aturan(): array
    {
        return [
            'nama' => [
                'label'  => 'Nama petugas',
                'rules'  => ['required', 'min_length[3]', 'max_length[100]', 'regex_match[/^[\p{L}\s.\'-]+$/u]'],
                'errors' => [
                    'required'    => 'Nama petugas wajib diisi.',
                    'min_length'  => 'Nama minimal 3 karakter.',
                    'max_length'  => 'Nama maksimal 100 karakter.',
                    'regex_match' => 'Nama hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung.',
                ],
            ],
            'bidang' => [
                'label'  => 'Bidang',
                'rules'  => ['required', 'min_length[3]', 'max_length[60]', 'regex_match[/^[\p{L}\p{N}\s&\-\/.()]+$/u]'],
                'errors' => [
                    'required'    => 'Bidang wajib diisi.',
                    'min_length'  => 'Bidang minimal 3 karakter.',
                    'max_length'  => 'Bidang maksimal 60 karakter.',
                    'regex_match' => 'Bidang hanya boleh berisi huruf, angka, spasi, dan tanda & - / . ( ).',
                ],
            ],
            'kontak' => [
                'label'  => 'Nomor kontak',
                'rules'  => ['required', 'regex_match[/^[0-9+\-\s]{8,20}$/]'],
                'errors' => [
                    'required'    => 'Nomor kontak wajib diisi.',
                    'regex_match' => 'Nomor kontak harus 8 sampai 20 karakter berisi angka, spasi, tanda + atau -.',
                ],
            ],
        ];
    }

    private function kembaliDenganError(string $url, array $errors)
    {
        return redirect()->to($url)
            ->with('errors', $errors)
            ->with('old', $this->request->getPost(['nama', 'bidang', 'kontak']));
    }

    private function bersihkan(string $nilai): string
    {
        return (string) preg_replace('/\s+/u', ' ', trim($nilai));
    }
}