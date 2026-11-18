<?php

namespace App\Models;

use CodeIgniter\Model;

class PengumpulanModel extends Model
{
    protected $table         = 'pengumpulan';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'tugas_id',
        'mahasiswa_id',
        'file_tugas',
        'nama_file_asli',
        'tanggal_pengumpulan',
        'status',
        'keterangan',
    ];

    // ----------------------------------------------------------------
    // READ — MAHASISWA
    // ----------------------------------------------------------------

    public function milikMahasiswa(int $tugasId, int $mahasiswaId): ?array
    {
        return $this->where('tugas_id', $tugasId)
                    ->where('mahasiswa_id', $mahasiswaId)
                    ->first();
    }

    public function findUntukMahasiswa(int $id, int $mahasiswaId): ?array
    {
        $row = $this->db->table('pengumpulan p')
            ->select('p.*, t.judul AS judul_tugas, t.deadline, t.izin_revisi, t.batas_ukuran_file,
                      mk.nama_mata_kuliah, mk.kode_mata_kuliah,
                      pn.nilai, pn.komentar, pn.tanggal_penilaian,
                      u_admin.nama AS nama_penilai')
            ->join('tugas t', 't.id = p.tugas_id')
            ->join('mata_kuliah mk', 'mk.id = t.mata_kuliah_id')
            ->join('penilaian pn', 'pn.pengumpulan_id = p.id', 'left')
            ->join('users u_admin', 'u_admin.id = pn.admin_id', 'left')
            ->where('p.id', $id)
            ->where('p.mahasiswa_id', $mahasiswaId)
            ->get()->getRowArray();

        return $row ?: null;
    }

    // ----------------------------------------------------------------
    // READ — ADMIN
    // ----------------------------------------------------------------

    public function daftarByTugas(int $tugasId): array
    {
        return $this->db->table('pengumpulan p')
            ->select('p.*, u.nama, u.nim, u.email,
                      pn.nilai, pn.komentar, pn.tanggal_penilaian,
                      u_admin.nama AS nama_penilai')
            ->join('users u', 'u.id = p.mahasiswa_id')
            ->join('penilaian pn', 'pn.pengumpulan_id = p.id', 'left')
            ->join('users u_admin', 'u_admin.id = pn.admin_id', 'left')
            ->where('p.tugas_id', $tugasId)
            ->orderBy('p.tanggal_pengumpulan', 'ASC')
            ->get()->getResultArray();
    }

    public function findUntukAdmin(int $id): ?array
    {
        $row = $this->db->table('pengumpulan p')
            ->select('p.*, u.nama, u.nim, u.email,
                      t.judul AS judul_tugas, t.deadline, t.izin_revisi, t.batas_ukuran_file,
                      mk.nama_mata_kuliah, mk.kode_mata_kuliah,
                      pn.id AS penilaian_id, pn.nilai, pn.komentar, pn.tanggal_penilaian,
                      u_admin.nama AS nama_penilai')
            ->join('users u', 'u.id = p.mahasiswa_id')
            ->join('tugas t', 't.id = p.tugas_id')
            ->join('mata_kuliah mk', 'mk.id = t.mata_kuliah_id')
            ->join('penilaian pn', 'pn.pengumpulan_id = p.id', 'left')
            ->join('users u_admin', 'u_admin.id = pn.admin_id', 'left')
            ->where('p.id', $id)
            ->get()->getRowArray();

        return $row ?: null;
    }

    public function statistikTugas(int $tugasId): array
    {
        $totalMhs = (int) $this->db->table('tugas_kelas tk')
            ->join('kelas_mahasiswa km', 'km.kelas_id = tk.kelas_id')
            ->where('tk.tugas_id', $tugasId)
            ->countAllResults();

        $rows = $this->db->table('pengumpulan')
            ->select('status, COUNT(*) AS jml')
            ->where('tugas_id', $tugasId)
            ->groupBy('status')
            ->get()->getResultArray();

        $sudahKumpul = 0;
        $terlambat   = 0;
        foreach ($rows as $r) {
            $sudahKumpul += (int) $r['jml'];
            if ($r['status'] === 'terlambat') {
                $terlambat += (int) $r['jml'];
            }
        }

        $dinilai = (int) $this->db->table('penilaian pn')
            ->join('pengumpulan p', 'p.id = pn.pengumpulan_id')
            ->where('p.tugas_id', $tugasId)
            ->countAllResults();

        return [
            'total_mahasiswa' => $totalMhs,
            'sudah_kumpul'    => $sudahKumpul,
            'belum_kumpul'    => max(0, $totalMhs - $sudahKumpul),
            'terlambat'       => $terlambat,
            'dinilai'         => $dinilai,
        ];
    }

    public function statistikMahasiswa(int $mahasiswaId): array
    {
        $stats = ['total' => 0, 'tepat_waktu' => 0, 'terlambat' => 0, 'dinilai' => 0];

        $rows = $this->db->table('pengumpulan')
            ->select('status, COUNT(*) AS jml')
            ->where('mahasiswa_id', $mahasiswaId)
            ->groupBy('status')
            ->get()->getResultArray();

        foreach ($rows as $r) {
            $stats['total'] += (int) $r['jml'];
            if (isset($stats[$r['status']])) {
                $stats[$r['status']] += (int) $r['jml'];
            }
        }

        $stats['dinilai'] = (int) $this->db->table('penilaian pn')
            ->join('pengumpulan p', 'p.id = pn.pengumpulan_id')
            ->where('p.mahasiswa_id', $mahasiswaId)
            ->countAllResults();

        return $stats;
    }

    public function tugasAktifMahasiswa(int $mahasiswaId, int $limit = 5): array
    {
        return $this->db->table('tugas t')
            ->select('t.id, t.judul, t.deadline, mk.nama_mata_kuliah, mk.kode_mata_kuliah,
                      p.id AS pengumpulan_id, p.status AS status_pengumpulan,
                      CASE WHEN t.deadline < NOW() THEN 1 ELSE 0 END AS sudah_lewat')
            ->join('mata_kuliah mk', 'mk.id = t.mata_kuliah_id')
            ->join('tugas_kelas tk', 'tk.tugas_id = t.id')
            ->join('kelas_mahasiswa km', 'km.kelas_id = tk.kelas_id AND km.mahasiswa_id = ' . (int) $mahasiswaId)
            ->join('pengumpulan p', 'p.tugas_id = t.id AND p.mahasiswa_id = ' . (int) $mahasiswaId, 'left')
            ->where('t.status', 'aktif')
            ->where('t.tanggal_mulai <=', date('Y-m-d H:i:s'))
            ->orderBy('t.deadline', 'ASC')
            ->limit($limit)
            ->get()->getResultArray();
    }
}
