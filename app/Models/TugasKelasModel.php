<?php

namespace App\Models;

use CodeIgniter\Model;

class TugasKelasModel extends Model
{
    protected $table         = 'tugas_kelas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['tugas_id', 'kelas_id'];

    /**
     * Kelas yang mendapat tugas tertentu (dengan info nama kelas dan mata kuliah).
     */
    public function kelasDariTugas(int $tugasId): array
    {
        return $this->db->table('tugas_kelas tk')
            ->select('tk.id, tk.kelas_id, k.nama_kelas, mk.nama_mata_kuliah, mk.kode_mata_kuliah, k.tahun_akademik, k.semester')
            ->join('kelas k', 'k.id = tk.kelas_id')
            ->join('mata_kuliah mk', 'mk.id = k.mata_kuliah_id')
            ->where('tk.tugas_id', $tugasId)
            ->orderBy('mk.nama_mata_kuliah', 'ASC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Simpan ulang relasi tugas-kelas: hapus lama, sisipkan baru.
     * Dibungkus transaksi — panggil dari dalam transaksi luar jika perlu.
     */
    public function simpanKelas(int $tugasId, array $kelasIds): void
    {
        // Hapus semua relasi lama untuk tugas ini
        $this->where('tugas_id', $tugasId)->delete();

        // Sisipkan relasi baru (hindari duplikat lewat array_unique)
        foreach (array_unique(array_map('intval', $kelasIds)) as $kelasId) {
            if ($kelasId > 0) {
                $this->insert(['tugas_id' => $tugasId, 'kelas_id' => $kelasId]);
            }
        }
    }

    /**
     * ID kelas yang sudah dipilih untuk tugas tertentu.
     */
    public function kelasIdsDariTugas(int $tugasId): array
    {
        return array_column(
            $this->select('kelas_id')->where('tugas_id', $tugasId)->findAll(),
            'kelas_id'
        );
    }
}
