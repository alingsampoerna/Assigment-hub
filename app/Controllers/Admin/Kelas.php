<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\KelasMahasiswaModel;
use App\Models\MataKuliahModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Kelas extends BaseController
{
    private const PER_PAGE = 10;

    // ----------------------------------------------------------------
    // INDEX
    // ----------------------------------------------------------------

    public function index(): string
    {
        $model = new KelasModel();

        $q      = mb_substr($this->strGet('q'), 0, 100);
        $mkId   = $this->strGet('mk');
        $status = $this->strGet('status');

        if (! in_array($status, ['aktif', 'nonaktif'], true)) {
            $status = '';
        }

        $total        = $model->hitung($q, $mkId, $status);
        $totalHalaman = max(1, (int) ceil($total / self::PER_PAGE));
        $halaman      = min(max((int) $this->strGet('page'), 1), $totalHalaman);

        $items     = $model->daftar($q, $mkId, $status, self::PER_PAGE, ($halaman - 1) * self::PER_PAGE);
        $mataKuliah = (new MataKuliahModel())->daftarAktif();
        $filter    = ['q' => $q, 'mk' => $mkId, 'status' => $status];

        return view('pages/admin/kelas_index', [
            'title'      => 'Kelas',
            'activeNav'  => 'kelas',
            'items'      => $items,
            'total'      => $total,
            'perPage'    => self::PER_PAGE,
            'page'       => $halaman,
            'totalPages' => $totalHalaman,
            'filter'     => $filter,
            'query'      => array_filter($filter, static fn (string $v): bool => $v !== ''),
            'mataKuliah' => $mataKuliah,
        ]);
    }

    // ----------------------------------------------------------------
    // CREATE
    // ----------------------------------------------------------------

    public function create(): string
    {
        return view('pages/admin/kelas_form', [
            'title'      => 'Tambah Kelas',
            'activeNav'  => 'kelas',
            'isEdit'     => false,
            'item'       => null,
            'mataKuliah' => (new MataKuliahModel())->daftarAktif(),
        ]);
    }

    public function store()
    {
        $model = new KelasModel();

        if (! $this->validate($model->validationRules())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->with('old', $this->request->getPost());
        }

        $model->insert([
            'mata_kuliah_id' => (int) $this->request->getPost('mata_kuliah_id'),
            'nama_kelas'     => trim((string) $this->request->getPost('nama_kelas')),
            'tahun_akademik' => trim((string) $this->request->getPost('tahun_akademik')),
            'semester'       => $this->request->getPost('semester'),
            'status'         => $this->request->getPost('status'),
        ]);

        return redirect()->to(site_url('admin/kelas'))
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    // ----------------------------------------------------------------
    // EDIT
    // ----------------------------------------------------------------

    public function edit(int $id): string
    {
        return view('pages/admin/kelas_form', [
            'title'      => 'Edit Kelas',
            'activeNav'  => 'kelas',
            'isEdit'     => true,
            'item'       => $this->cariAtau404($id),
            'mataKuliah' => (new MataKuliahModel())->daftarAktif(),
        ]);
    }

    public function update(int $id)
    {
        $this->cariAtau404($id);
        $model = new KelasModel();

        if (! $this->validate($model->validationRules())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->with('old', $this->request->getPost());
        }

        $model->update($id, [
            'mata_kuliah_id' => (int) $this->request->getPost('mata_kuliah_id'),
            'nama_kelas'     => trim((string) $this->request->getPost('nama_kelas')),
            'tahun_akademik' => trim((string) $this->request->getPost('tahun_akademik')),
            'semester'       => $this->request->getPost('semester'),
            'status'         => $this->request->getPost('status'),
        ]);

        return redirect()->to(site_url('admin/kelas'))
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    // ----------------------------------------------------------------
    // DELETE
    // ----------------------------------------------------------------

    public function delete(int $id)
    {
        $this->cariAtau404($id);

        try {
            (new KelasModel())->delete($id);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal hapus kelas id=' . $id . ': ' . $e->getMessage());

            return redirect()->to(site_url('admin/kelas'))
                ->with('error', 'Kelas tidak dapat dihapus karena masih memiliki anggota atau tugas aktif.');
        }

        return redirect()->to(site_url('admin/kelas'))
            ->with('success', 'Kelas berhasil dihapus.');
    }

    // ----------------------------------------------------------------
    // ANGGOTA KELAS
    // ----------------------------------------------------------------

    public function anggota(int $id): string
    {
        $kelas    = $this->cariAtau404Detail($id);
        $kmModel  = new KelasMahasiswaModel();

        $q       = mb_substr($this->strGet('cari'), 0, 100);
        $anggota = $kmModel->anggotaKelas($id);
        $calon   = $kmModel->mahasiswaBelumDaftar($id, $q);

        return view('pages/admin/kelas_anggota', [
            'title'     => 'Anggota Kelas — ' . $kelas['nama_kelas'],
            'activeNav' => 'kelas',
            'kelas'     => $kelas,
            'anggota'   => $anggota,
            'calon'     => $calon,
            'q'         => $q,
        ]);
    }

    public function tambahAnggota(int $id)
    {
        $kelas       = $this->cariAtau404($id);
        $mahasiswaId = (int) $this->request->getPost('mahasiswa_id');

        if ($mahasiswaId === 0) {
            return redirect()->to(site_url('admin/kelas/' . $id . '/anggota'))
                ->with('error', 'Pilih mahasiswa terlebih dahulu.');
        }

        // Pastikan mahasiswa valid dan berstatus aktif
        $mhs = (new UserModel())->where('id', $mahasiswaId)
                                ->where('role', 'mahasiswa')
                                ->where('status', 'aktif')
                                ->first();

        if ($mhs === null) {
            return redirect()->to(site_url('admin/kelas/' . $id . '/anggota'))
                ->with('error', 'Mahasiswa tidak ditemukan atau tidak aktif.');
        }

        $berhasil = (new KelasMahasiswaModel())->tambahAnggota($id, $mahasiswaId);

        if (! $berhasil) {
            return redirect()->to(site_url('admin/kelas/' . $id . '/anggota'))
                ->with('error', 'Mahasiswa sudah terdaftar di kelas ini.');
        }

        return redirect()->to(site_url('admin/kelas/' . $id . '/anggota'))
            ->with('success', esc($mhs['nama']) . ' berhasil ditambahkan ke kelas.');
    }

    public function hapusAnggota(int $kelasId, int $anggotaId)
    {
        $this->cariAtau404($kelasId);

        $berhasil = (new KelasMahasiswaModel())->hapusAnggota($anggotaId, $kelasId);

        if (! $berhasil) {
            return redirect()->to(site_url('admin/kelas/' . $kelasId . '/anggota'))
                ->with('error', 'Data anggota tidak ditemukan.');
        }

        return redirect()->to(site_url('admin/kelas/' . $kelasId . '/anggota'))
            ->with('success', 'Anggota berhasil dihapus dari kelas.');
    }

    // ----------------------------------------------------------------
    // HELPER
    // ----------------------------------------------------------------

    private function cariAtau404(int $id): array
    {
        $item = (new KelasModel())->find($id);

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $item;
    }

    private function cariAtau404Detail(int $id): array
    {
        $item = (new KelasModel())->findWithMK($id);

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $item;
    }

    private function strGet(string $key): string
    {
        $v = $this->request->getGet($key);
        return is_string($v) ? trim($v) : '';
    }
}
