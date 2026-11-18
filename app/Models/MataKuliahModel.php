<?php

namespace App\Models;

use CodeIgniter\Model;

class MataKuliahModel extends Model
{
    protected $table         = 'mata_kuliah';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'kode_mata_kuliah',
        'nama_mata_kuliah',
        'deskripsi',
        'status',
    ];

    // ----------------------------------------------------------------
    // READ
    // ----------------------------------------------------------------

    /**
     * Semua mata kuliah aktif, diurutkan nama A-Z.
     * Digunakan untuk dropdown di form kelas dan tugas.
     */
    public function daftarAktif(): array
    {
        return $this->where('status', 'aktif')
                    ->orderBy('nama_mata_kuliah', 'ASC')
                    ->findAll();
    }

    /**
     * Hitung total mata kuliah (opsional filter status).
     */
    public function hitung(string $status = '', string $q = ''): int
    {
        $builder = $this->builder();

        if ($status !== '') {
            $builder->where('status', $status);
        }
        if ($q !== '') {
            $builder->groupStart()
                    ->like('kode_mata_kuliah', $q)
                    ->orLike('nama_mata_kuliah', $q)
                    ->groupEnd();
        }

        return (int) $builder->countAllResults();
    }

    /**
     * Daftar mata kuliah dengan pagination dan filter.
     */
    public function daftar(string $status = '', string $q = '', int $limit = 10, int $offset = 0): array
    {
        $builder = $this->builder();

        if ($status !== '') {
            $builder->where('status', $status);
        }
        if ($q !== '') {
            $builder->groupStart()
                    ->like('kode_mata_kuliah', $q)
                    ->orLike('nama_mata_kuliah', $q)
                    ->groupEnd();
        }

        return $builder->orderBy('nama_mata_kuliah', 'ASC')
                       ->limit($limit, $offset)
                       ->get()
                       ->getResultArray();
    }

    // ----------------------------------------------------------------
    // VALIDASI
    // ----------------------------------------------------------------

    /**
     * Rules validasi untuk create/edit.
     * Saat edit, kode harus unik kecuali milik record itu sendiri.
     */
    public function validationRules(int $excludeId = 0): array
    {
        $uniqueKode = 'is_unique[mata_kuliah.kode_mata_kuliah]';
        if ($excludeId > 0) {
            $uniqueKode = "is_unique[mata_kuliah.kode_mata_kuliah,id,{$excludeId}]";
        }

        return [
            'kode_mata_kuliah' => [
                'label'  => 'Kode mata kuliah',
                'rules'  => ['required', 'min_length[2]', 'max_length[20]', $uniqueKode],
                'errors' => [
                    'required'   => 'Kode mata kuliah wajib diisi.',
                    'min_length' => 'Kode minimal 2 karakter.',
                    'max_length' => 'Kode maksimal 20 karakter.',
                    'is_unique'  => 'Kode mata kuliah ini sudah digunakan.',
                ],
            ],
            'nama_mata_kuliah' => [
                'label'  => 'Nama mata kuliah',
                'rules'  => ['required', 'min_length[3]', 'max_length[100]'],
                'errors' => [
                    'required'   => 'Nama mata kuliah wajib diisi.',
                    'min_length' => 'Nama minimal 3 karakter.',
                    'max_length' => 'Nama maksimal 100 karakter.',
                ],
            ],
            'deskripsi' => [
                'label'  => 'Deskripsi',
                'rules'  => ['permit_empty', 'max_length[500]'],
                'errors' => [
                    'max_length' => 'Deskripsi maksimal 500 karakter.',
                ],
            ],
            'status' => [
                'label'  => 'Status',
                'rules'  => ['required', 'in_list[aktif,nonaktif]'],
                'errors' => [
                    'required' => 'Status wajib dipilih.',
                    'in_list'  => 'Status tidak valid.',
                ],
            ],
        ];
    }
}
