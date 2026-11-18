<?php

namespace App\Models;

use CodeIgniter\Model;

class PenilaianModel extends Model
{
    protected $table         = 'penilaian';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'pengumpulan_id',
        'admin_id',
        'nilai',
        'komentar',
        'tanggal_penilaian',
    ];

    public function findByPengumpulan(int $pengumpulanId): ?array
    {
        return $this->where('pengumpulan_id', $pengumpulanId)->first();
    }

    public function simpanAtauPerbarui(int $pengumpulanId, int $adminId, int $nilai, string $komentar): void
    {
        $existing = $this->findByPengumpulan($pengumpulanId);

        $data = [
            'pengumpulan_id'   => $pengumpulanId,
            'admin_id'         => $adminId,
            'nilai'            => $nilai,
            'komentar'         => $komentar ?: null,
            'tanggal_penilaian'=> date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            $this->update($existing['id'], $data);
        } else {
            $this->insert($data);
        }
    }
}
