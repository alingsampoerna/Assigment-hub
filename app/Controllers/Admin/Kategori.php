<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

class Kategori extends BaseController
{
    // ------------------------------------------------------------
    // DAFTAR
    // ------------------------------------------------------------

    public function index(): string
    {
        return view('pages/admin/kategori_index', [
            'title'     => 'Kelola Kategori - CampusCare Admin',
            'activeNav' => 'kategori',
            'items'     => (new CategoryModel())->denganJumlah(),
        ]);
    }

    // ------------------------------------------------------------
    // TAMBAH
    // ------------------------------------------------------------

    public function create(): string
    {
        return view('pages/admin/kategori_form', [
            'title'     => 'Tambah Kategori - CampusCare Admin',
            'activeNav' => 'kategori',
            'mode'      => 'tambah',
            'item'      => null,
        ]);
    }

    public function store()
    {
        $kembali = site_url('admin/kategori/baru');

        if (! $this->validate($this->aturan())) {
            return $this->kembaliDenganError($kembali, $this->validator->getErrors());
        }

        $model = new CategoryModel();
        $nama  = $this->bersihkan((string) $this->request->getPost('nama'));

        if ($model->namaSudahAda($nama)) {
            return $this->kembaliDenganError($kembali, ['nama' => 'Kategori dengan nama tersebut sudah ada.']);
        }

        try {
            $model->insert(['nama' => $nama]);
        } catch (DatabaseException $e) {
            // Kemungkinan: nama yang sama disimpan bersamaan (dilindungi UNIQUE di database)
            log_message('error', 'Gagal menambah kategori: ' . $e->getMessage());

            return $this->kembaliDenganError($kembali, ['nama' => 'Kategori dengan nama tersebut sudah ada.']);
        }

        return redirect()->to(site_url('admin/kategori'))
            ->with('success', 'Kategori "' . $nama . '" berhasil ditambahkan.');
    }

    // ------------------------------------------------------------
    // UBAH
    // ------------------------------------------------------------

    public function edit(int $id): string
    {
        $kategori = (new CategoryModel())->find($id);

        if ($kategori === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('pages/admin/kategori_form', [
            'title'     => 'Ubah Kategori - CampusCare Admin',
            'activeNav' => 'kategori',
            'mode'      => 'ubah',
            'item'      => ['id' => (int) $kategori['id'], 'nama' => $kategori['nama']],
        ]);
    }

    public function update(int $id)
    {
        $model = new CategoryModel();

        if ($model->find($id) === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $kembali = site_url('admin/kategori/' . $id . '/ubah');

        if (! $this->validate($this->aturan())) {
            return $this->kembaliDenganError($kembali, $this->validator->getErrors());
        }

        $nama = $this->bersihkan((string) $this->request->getPost('nama'));

        if ($model->namaSudahAda($nama, $id)) {
            return $this->kembaliDenganError($kembali, ['nama' => 'Kategori dengan nama tersebut sudah ada.']);
        }

        try {
            $model->update($id, ['nama' => $nama]);
        } catch (DatabaseException $e) {
            log_message('error', 'Gagal mengubah kategori: ' . $e->getMessage());

            return $this->kembaliDenganError($kembali, ['nama' => 'Kategori dengan nama tersebut sudah ada.']);
        }

        return redirect()->to(site_url('admin/kategori'))
            ->with('success', 'Kategori berhasil diubah menjadi "' . $nama . '".');
    }

    // ------------------------------------------------------------
    // HAPUS
    // ------------------------------------------------------------

    public function delete(int $id)
    {
        $model    = new CategoryModel();
        $kategori = $model->find($id);

        if ($kategori === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $tujuan = site_url('admin/kategori');
        $jumlah = $model->jumlahLaporan($id);

        // Diperiksa di controller agar pesannya ramah; foreign key RESTRICT di database
        // menjadi jaring pengaman jika ada laporan yang masuk bersamaan.
        if ($jumlah > 0) {
            return redirect()->to($tujuan)
                ->with('error', 'Kategori "' . $kategori['nama'] . '" tidak dapat dihapus karena dipakai oleh ' . $jumlah . ' laporan.');
        }

        try {
            $model->delete($id);
        } catch (DatabaseException $e) {
            log_message('error', 'Gagal menghapus kategori: ' . $e->getMessage());

            return redirect()->to($tujuan)
                ->with('error', 'Kategori "' . $kategori['nama'] . '" tidak dapat dihapus karena sedang dipakai.');
        }

        return redirect()->to($tujuan)
            ->with('success', 'Kategori "' . $kategori['nama'] . '" berhasil dihapus.');
    }

    // ------------------------------------------------------------
    // BANTUAN
    // ------------------------------------------------------------

    private function aturan(): array
    {
        return [
            'nama' => [
                'label'  => 'Nama kategori',
                'rules'  => ['required', 'min_length[3]', 'max_length[50]', 'regex_match[/^[\p{L}\p{N}\s&\-\/.()]+$/u]'],
                'errors' => [
                    'required'    => 'Nama kategori wajib diisi.',
                    'min_length'  => 'Nama kategori minimal 3 karakter.',
                    'max_length'  => 'Nama kategori maksimal 50 karakter.',
                    'regex_match' => 'Nama hanya boleh berisi huruf, angka, spasi, dan tanda & - / . ( ).',
                ],
            ],
        ];
    }

    private function kembaliDenganError(string $url, array $errors)
    {
        return redirect()->to($url)
            ->with('errors', $errors)
            ->with('old', ['nama' => (string) $this->request->getPost('nama')]);
    }

    /**
     * Merapikan spasi: hapus spasi di tepi dan ubah spasi ganda menjadi satu.
     */
    private function bersihkan(string $nilai): string
    {
        return (string) preg_replace('/\s+/u', ' ', trim($nilai));
    }
}