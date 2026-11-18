<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table         = 'categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['nama'];

    /**
     * Daftar kategori: [id => nama], urut berdasarkan ID.
     */
    public function daftar(): array
    {
        return array_column($this->orderBy('id', 'ASC')->findAll(), 'nama', 'id');
    }

    /**
     * Daftar kategori beserta jumlah laporan yang memakainya.
     * Hasil: [['id' => int, 'nama' => string, 'jumlah' => int], ...]
     */
    public function denganJumlah(): array
    {
        $rows = $this->db->table('categories c')
            ->select('c.id, c.nama, COUNT(r.id) AS jumlah')
            ->join('reports r', 'r.kategori_id = c.id', 'left')
            ->groupBy('c.id, c.nama')
            ->orderBy('c.id', 'ASC')
            ->get()
            ->getResultArray();

        return array_map(static fn (array $row): array => [
            'id'     => (int) $row['id'],
            'nama'   => $row['nama'],
            'jumlah' => (int) $row['jumlah'],
        ], $rows);
    }

    public function jumlahLaporan(int $id): int
    {
        return (int) $this->db->table('reports')->where('kategori_id', $id)->countAllResults();
    }

    /**
     * Apakah nama sudah dipakai kategori lain (tanpa membedakan huruf besar/kecil,
     * mengikuti collation database). $kecualiId dipakai saat mengubah kategori sendiri.
     */
    public function namaSudahAda(string $nama, ?int $kecualiId = null): bool
    {
        $builder = $this->db->table('categories')->where('nama', $nama);

        if ($kecualiId !== null) {
            $builder->where('id !=', $kecualiId);
        }

        return $builder->countAllResults() > 0;
    }
}