<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasMahasiswaModel extends Model
{
    protected $table         = 'kelas_mahasiswa';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['kelas_id', 'mahasiswa_id', 'created_at'];

    // ----------------------------------------------------------------
    // READ
    // ----------------------------------------------------------------

    /**
     * Daftar mahasiswa yang terdaftar di kelas tertentu.
     */
    public function anggotaKelas(int $kelasId): array
    {
        return $this->db->table('kelas_mahasiswa km')
                        ->select('km.id, km.created_at AS tanggal_masuk, u.id AS mahasiswa_id, u.nama, u.nim, u.email, u.status')
                        ->join('users u', 'u.id = km.mahasiswa_id')
                        ->where('km.kelas_id', $kelasId)
                        ->orderBy('u.nama', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    /**
     * Hitung jumlah anggota di sebuah kelas.
     */
    public function hitungAnggota(int $kelasId): int
    {
        return (int) $this->where('kelas_id', $kelasId)->countAllResults();
    }

    /**
     * Cek apakah mahasiswa sudah terdaftar di kelas.
     */
    public function sudahTerdaftar(int $kelasId, int $mahasiswaId): bool
    {
        return $this->where('kelas_id', $kelasId)
                    ->where('mahasiswa_id', $mahasiswaId)
                    ->countAllResults() > 0;
    }

    /**
     * Semua kelas yang diikuti oleh seorang mahasiswa (dengan info mk).
     * Menampilkan semua status kelas agar tetap terlihat meski nonaktif.
     */
    public function kelasMahasiswa(int $mahasiswaId): array
    {
        return $this->db->table('kelas_mahasiswa km')
                        ->select('k.id, k.nama_kelas, k.tahun_akademik, k.semester, k.status,
                                  mk.nama_mata_kuliah, mk.kode_mata_kuliah, mk.id AS mata_kuliah_id')
                        ->join('kelas k', 'k.id = km.kelas_id')
                        ->join('mata_kuliah mk', 'mk.id = k.mata_kuliah_id')
                        ->where('km.mahasiswa_id', $mahasiswaId)
                        ->orderBy('mk.nama_mata_kuliah', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    /**
     * Semua kelas aktif yang BELUM diikuti mahasiswa (untuk self-enroll).
     */
    public function kelasBelumDiikuti(int $mahasiswaId, string $q = ''): array
    {
        $sudahIkut = array_column(
            $this->db->table('kelas_mahasiswa')
                ->select('kelas_id')
                ->where('mahasiswa_id', $mahasiswaId)
                ->get()->getResultArray(),
            'kelas_id'
        );

        $builder = $this->db->table('kelas k')
            ->select('k.id, k.nama_kelas, k.tahun_akademik, k.semester,
                      mk.nama_mata_kuliah, mk.kode_mata_kuliah')
            ->join('mata_kuliah mk', 'mk.id = k.mata_kuliah_id')
            ->where('k.status', 'aktif');

        if (! empty($sudahIkut)) {
            $builder->whereNotIn('k.id', $sudahIkut);
        }

        if ($q !== '') {
            $builder->groupStart()
                    ->like('k.nama_kelas', $q)
                    ->orLike('mk.nama_mata_kuliah', $q)
                    ->orLike('mk.kode_mata_kuliah', $q)
                    ->groupEnd();
        }

        return $builder->orderBy('mk.nama_mata_kuliah', 'ASC')
                       ->orderBy('k.nama_kelas', 'ASC')
                       ->get()->getResultArray();
    }

    /**
     * Mahasiswa yang BELUM terdaftar di kelas tertentu (untuk form tambah anggota).
     */
    public function mahasiswaBelumDaftar(int $kelasId, string $q = ''): array
    {
        // Ambil ID mahasiswa yang sudah terdaftar
        $sudahDaftar = array_column(
            $this->db->table('kelas_mahasiswa')
                ->select('mahasiswa_id')
                ->where('kelas_id', $kelasId)
                ->get()->getResultArray(),
            'mahasiswa_id'
        );

        $builder = $this->db->table('users u')
                            ->select('u.id, u.nama, u.nim, u.email')
                            ->where('u.role', 'mahasiswa')
                            ->where('u.status', 'aktif');

        // Hanya exclude jika ada yang sudah terdaftar
        if (! empty($sudahDaftar)) {
            $builder->whereNotIn('u.id', $sudahDaftar);
        }

        if ($q !== '') {
            $builder->groupStart()
                    ->like('u.nama', $q)
                    ->orLike('u.nim', $q)
                    ->groupEnd();
        }

        return $builder->orderBy('u.nama', 'ASC')
                       ->limit(20)
                       ->get()
                       ->getResultArray();
    }

    // ----------------------------------------------------------------
    // WRITE
    // ----------------------------------------------------------------

    /**
     * Tambah mahasiswa ke kelas. Return false jika sudah terdaftar.
     */
    public function tambahAnggota(int $kelasId, int $mahasiswaId): bool
    {
        if ($this->sudahTerdaftar($kelasId, $mahasiswaId)) {
            return false;
        }

        $this->insert([
            'kelas_id'     => $kelasId,
            'mahasiswa_id' => $mahasiswaId,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        return true;
    }

    /**
     * Hapus anggota berdasarkan ID baris kelas_mahasiswa.
     */
    public function hapusAnggota(int $id, int $kelasId): bool
    {
        return (bool) $this->where('id', $id)
                           ->where('kelas_id', $kelasId)
                           ->delete();
    }
}
