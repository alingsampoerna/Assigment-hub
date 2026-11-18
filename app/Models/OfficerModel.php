<?php

namespace App\Models;

use CodeIgniter\Model;

class OfficerModel extends Model
{
    protected $table         = 'officers';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['nama', 'bidang', 'kontak'];

    /**
     * Semua petugas: [id => ['id', 'nama', 'bidang', 'kontak']].
     */
    public function daftar(): array
    {
        $hasil = [];

        foreach ($this->orderBy('id', 'ASC')->findAll() as $row) {
            $id         = (int) $row['id'];
            $hasil[$id] = [
                'id'     => $id,
                'nama'   => $row['nama'],
                'bidang' => $row['bidang'],
                'kontak' => $row['kontak'],
            ];
        }

        return $hasil;
    }

    /**
     * Petugas beserta jumlah penugasan.
     *  - total: jumlah laporan yang pernah ditugaskan kepadanya
     *  - aktif: dari laporan itu, yang statusnya masih Diproses
     */
    public function denganStatistik(): array
    {
        $rows = $this->db->table('officers o')
            ->select(
                "o.id, o.nama, o.bidang, o.kontak, "
                . "COUNT(DISTINCT ra.report_id) AS total, "
                . "COUNT(DISTINCT CASE WHEN r.status = 'diproses' THEN ra.report_id END) AS aktif",
                false
            )
            ->join('report_assignments ra', 'ra.officer_id = o.id', 'left')
            ->join('reports r', 'r.id = ra.report_id', 'left')
            ->groupBy('o.id, o.nama, o.bidang, o.kontak')
            ->orderBy('o.id', 'ASC')
            ->get()
            ->getResultArray();

        return array_map(static fn (array $row): array => [
            'id'     => (int) $row['id'],
            'nama'   => $row['nama'],
            'bidang' => $row['bidang'],
            'kontak' => $row['kontak'],
            'aktif'  => (int) $row['aktif'],
            'total'  => (int) $row['total'],
        ], $rows);
    }

    /**
     * Jumlah laporan yang pernah ditugaskan kepada petugas ini.
     */
    public function jumlahPenugasan(int $id): int
    {
        $row = $this->db->table('report_assignments')
            ->select('COUNT(DISTINCT report_id) AS n', false)
            ->where('officer_id', $id)
            ->get()
            ->getRowArray();

        return (int) ($row['n'] ?? 0);
    }

    public function namaSudahAda(string $nama, ?int $kecualiId = null): bool
    {
        $builder = $this->db->table('officers')->where('nama', $nama);

        if ($kecualiId !== null) {
            $builder->where('id !=', $kecualiId);
        }

        return $builder->countAllResults() > 0;
    }
}