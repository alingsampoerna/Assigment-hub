<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MataKuliahModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class MataKuliah extends BaseController
{
    private const PER_PAGE = 10;

    // ----------------------------------------------------------------
    // INDEX
    // ----------------------------------------------------------------

    public function index(): string
    {
        $model = new MataKuliahModel();

        $q      = mb_substr($this->strGet('q'), 0, 100);
        $status = $this->strGet('status');

        if (! in_array($status, ['aktif', 'nonaktif'], true)) {
            $status = '';
        }

        $total        = $model->hitung($status, $q);
        $totalHalaman = max(1, (int) ceil($total / self::PER_PAGE));
        $halaman      = min(max((int) $this->strGet('page'), 1), $totalHalaman);

        $items  = $model->daftar($status, $q, self::PER_PAGE, ($halaman - 1) * self::PER_PAGE);
        $filter = ['q' => $q, 'status' => $status];

        return view('pages/admin/mata_kuliah_index', [
            'title'      => 'Mata Kuliah',
            'activeNav'  => 'mata-kuliah',
            'items'      => $items,
            'total'      => $total,
            'perPage'    => self::PER_PAGE,
            'page'       => $halaman,
            'totalPages' => $totalHalaman,
            'filter'     => $filter,
            'query'      => array_filter($filter, static fn (string $v): bool => $v !== ''),
        ]);
    }

    // ----------------------------------------------------------------
    // CREATE
    // ----------------------------------------------------------------

    public function create(): string
    {
        return view('pages/admin/mata_kuliah_form', [
            'title'     => 'Tambah Mata Kuliah',
            'activeNav' => 'mata-kuliah',
            'isEdit'    => false,
            'item'      => null,
        ]);
    }

    public function store()
    {
        $model = new MataKuliahModel();

        if (! $this->validate($model->validationRules())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->with('old', $this->request->getPost());
        }

        $model->insert([
            'kode_mata_kuliah' => strtoupper(trim((string) $this->request->getPost('kode_mata_kuliah'))),
            'nama_mata_kuliah' => trim((string) $this->request->getPost('nama_mata_kuliah')),
            'deskripsi'        => trim((string) $this->request->getPost('deskripsi')) ?: null,
            'status'           => $this->request->getPost('status'),
        ]);

        return redirect()->to(site_url('admin/mata-kuliah'))
            ->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    // ----------------------------------------------------------------
    // EDIT
    // ----------------------------------------------------------------

    public function edit(int $id): string
    {
        return view('pages/admin/mata_kuliah_form', [
            'title'     => 'Edit Mata Kuliah',
            'activeNav' => 'mata-kuliah',
            'isEdit'    => true,
            'item'      => $this->cariAtau404($id),
        ]);
    }

    public function update(int $id)
    {
        $item  = $this->cariAtau404($id);
        $model = new MataKuliahModel();

        if (! $this->validate($model->validationRules($id))) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->with('old', $this->request->getPost());
        }

        $model->update($id, [
            'kode_mata_kuliah' => strtoupper(trim((string) $this->request->getPost('kode_mata_kuliah'))),
            'nama_mata_kuliah' => trim((string) $this->request->getPost('nama_mata_kuliah')),
            'deskripsi'        => trim((string) $this->request->getPost('deskripsi')) ?: null,
            'status'           => $this->request->getPost('status'),
        ]);

        return redirect()->to(site_url('admin/mata-kuliah'))
            ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    // ----------------------------------------------------------------
    // DELETE
    // ----------------------------------------------------------------

    public function delete(int $id)
    {
        $this->cariAtau404($id);

        try {
            (new MataKuliahModel())->delete($id);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal hapus mata kuliah id=' . $id . ': ' . $e->getMessage());

            return redirect()->to(site_url('admin/mata-kuliah'))
                ->with('error', 'Mata kuliah tidak dapat dihapus karena masih digunakan oleh kelas atau tugas.');
        }

        return redirect()->to(site_url('admin/mata-kuliah'))
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }

    // ----------------------------------------------------------------
    // HELPER
    // ----------------------------------------------------------------

    private function cariAtau404(int $id): array
    {
        $item = (new MataKuliahModel())->find($id);

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
