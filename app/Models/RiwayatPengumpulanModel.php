<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatPengumpulanModel extends Model
{
    protected $table         = 'riwayat_pengumpulan';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'pengumpulan_id',
        'file_lama',
        'nama_file_lama',
        'file_baru',
        'nama_file_baru',
        'tanggal_perubahan',
    ];

    public function forPengumpulan(int $pengumpulanId): array
    {
        return $this->where('pengumpulan_id', $pengumpulanId)
                    ->orderBy('tanggal_perubahan', 'DESC')
                    ->findAll();
    }

    public function catat(int $pengumpulanId, string $fileLama, string $namaLama, string $fileBaru, string $namaBaru): void
    {
        $this->insert([
            'pengumpulan_id'   => $pengumpulanId,
            'file_lama'        => $fileLama,
            'nama_file_lama'   => $namaLama,
            'file_baru'        => $fileBaru,
            'nama_file_baru'   => $namaBaru,
            'tanggal_perubahan'=> date('Y-m-d H:i:s'),
        ]);
    }
}
