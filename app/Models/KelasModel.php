<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table         = 'kelas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'mata_kuliah_id',
        'nama_kelas',
        'tahun_akademik',
        'semester',
        'status',
    ];

    // ----------------------------------------------------------------
    // READ
    // ----------------------------------------------------------------

    /**
     * Hitung total kelas dengan filter opsional.
     */
    public function hitung(string $q = '', string $mkId = '', string $status = ''): int
    {
        return (int) $this->builder()
                          ->join('mata_kuliah mk', 'mk.id = kelas.mata_kuliah_id')
                          ->when($q !== '', fn ($b) => $b->groupStart()
                              ->like('kelas.nama_kelas', $q)
                              ->orLike('mk.nama_mata_kuliah', $q)
                              ->groupEnd())
                          ->when($mkId !== '', fn ($b) => $b->where('kelas.mata_kuliah_id', $mkId))
                          ->when($status !== '', fn ($b) => $b->where('kelas.status', $status))
                          ->countAllResults();
    }

    /**
     * Daftar kelas dengan JOIN mata_kuliah, pagination dan filter.
     */
    public function daftar(string $q = '', string $mkId = '', string $status = '', int $limit = 10, int $offset = 0): array
    {
        return $this->select('kelas.*, mk.nama_mata_kuliah, mk.kode_mata_kuliah')
                    ->join('mata_kuliah mk', 'mk.id = kelas.mata_kuliah_id')
                    ->when($q !== '', fn ($b) => $b->groupStart()
                        ->like('kelas.nama_kelas', $q)
                        ->orLike('mk.nama_mata_kuliah', $q)
                        ->groupEnd())
                    ->when($mkId !== '', fn ($b) => $b->where('kelas.mata_kuliah_id', $mkId))
                    ->when($status !== '', fn ($b) => $b->where('kelas.status', $status))
                    ->orderBy('mk.nama_mata_kuliah', 'ASC')
                    ->orderBy('kelas.nama_kelas', 'ASC')
                    ->findAll($limit, $offset);
    }

    /**
     * Detail kelas beserta info mata kuliah.
     */
    public function findWithMK(int $id): ?array
    {
        return $this->select('kelas.*, mk.nama_mata_kuliah, mk.kode_mata_kuliah')
                    ->join('mata_kuliah mk', 'mk.id = kelas.mata_kuliah_id')
                    ->find($id);
    }

    /**
     * Semua kelas aktif milik satu mata kuliah (untuk dropdown).
     */
    public function daftarAktifByMK(int $mkId): array
    {
        return $this->where('mata_kuliah_id', $mkId)
                    ->where('status', 'aktif')
                    ->orderBy('nama_kelas', 'ASC')
                    ->findAll();
    }

    /**
     * Semua kelas aktif (untuk dropdown tugas).
     */
    public function daftarAktif(): array
    {
        return $this->select('kelas.*, mk.nama_mata_kuliah, mk.kode_mata_kuliah')
                    ->join('mata_kuliah mk', 'mk.id = kelas.mata_kuliah_id')
                    ->where('kelas.status', 'aktif')
                    ->orderBy('mk.nama_mata_kuliah', 'ASC')
                    ->orderBy('kelas.nama_kelas', 'ASC')
                    ->findAll();
    }

    // ----------------------------------------------------------------
    // VALIDASI
    // ----------------------------------------------------------------

    public function validationRules(): array
    {
        return [
            'mata_kuliah_id' => [
                'label'  => 'Mata kuliah',
                'rules'  => ['required', 'is_natural_no_zero', 'is_not_unique[mata_kuliah.id]'],
                'errors' => [
                    'required'         => 'Mata kuliah wajib dipilih.',
                    'is_natural_no_zero' => 'Pilihan mata kuliah tidak valid.',
                    'is_not_unique'    => 'Mata kuliah yang dipilih tidak ditemukan.',
                ],
            ],
            'nama_kelas' => [
                'label'  => 'Nama kelas',
                'rules'  => ['required', 'min_length[1]', 'max_length[50]'],
                'errors' => [
                    'required'   => 'Nama kelas wajib diisi.',
                    'min_length' => 'Nama kelas minimal 1 karakter.',
                    'max_length' => 'Nama kelas maksimal 50 karakter.',
                ],
            ],
            'tahun_akademik' => [
                'label'  => 'Tahun akademik',
                'rules'  => ['required', 'max_length[10]', 'regex_match[/^\d{4}\/\d{4}$/]'],
                'errors' => [
                    'required'    => 'Tahun akademik wajib diisi.',
                    'max_length'  => 'Tahun akademik maksimal 10 karakter.',
                    'regex_match' => 'Format tahun akademik harus YYYY/YYYY, contoh: 2026/2027.',
                ],
            ],
            'semester' => [
                'label'  => 'Semester',
                'rules'  => ['required', 'in_list[ganjil,genap]'],
                'errors' => [
                    'required' => 'Semester wajib dipilih.',
                    'in_list'  => 'Pilihan semester tidak valid.',
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
