<?php

namespace App\Models;

use CodeIgniter\Model;

class TugasModel extends Model
{
    protected $table         = 'tugas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'mata_kuliah_id',
        'dibuat_oleh',
        'judul',
        'deskripsi',
        'file_lampiran',
        'nama_lampiran_asli',
        'tanggal_mulai',
        'deadline',
        'batas_ukuran_file',
        'izin_revisi',
        'status',
    ];

    // ----------------------------------------------------------------
    // READ — ADMIN
    // ----------------------------------------------------------------

    public function hitung(string $q = '', string $mkId = '', string $status = ''): int
    {
        return (int) $this->builder()
            ->join('mata_kuliah mk', 'mk.id = tugas.mata_kuliah_id')
            ->when($q !== '', fn ($b) => $b->groupStart()
                ->like('tugas.judul', $q)
                ->orLike('mk.nama_mata_kuliah', $q)
                ->groupEnd())
            ->when($mkId !== '', fn ($b) => $b->where('tugas.mata_kuliah_id', $mkId))
            ->when($status !== '', fn ($b) => $b->where('tugas.status', $status))
            ->countAllResults();
    }

    public function daftar(string $q = '', string $mkId = '', string $status = '', int $limit = 10, int $offset = 0): array
    {
        return $this->select('tugas.*, mk.nama_mata_kuliah, mk.kode_mata_kuliah')
            ->join('mata_kuliah mk', 'mk.id = tugas.mata_kuliah_id')
            ->when($q !== '', fn ($b) => $b->groupStart()
                ->like('tugas.judul', $q)
                ->orLike('mk.nama_mata_kuliah', $q)
                ->groupEnd())
            ->when($mkId !== '', fn ($b) => $b->where('tugas.mata_kuliah_id', $mkId))
            ->when($status !== '', fn ($b) => $b->where('tugas.status', $status))
            ->orderBy('tugas.deadline', 'DESC')
            ->findAll($limit, $offset);
    }

    public function findDetail(int $id): ?array
    {
        return $this->select('tugas.*, mk.nama_mata_kuliah, mk.kode_mata_kuliah, u.nama AS nama_pembuat')
            ->join('mata_kuliah mk', 'mk.id = tugas.mata_kuliah_id')
            ->join('users u', 'u.id = tugas.dibuat_oleh')
            ->find($id);
    }

    // ----------------------------------------------------------------
    // READ — MAHASISWA
    // ----------------------------------------------------------------

    /**
     * Semua tugas aktif yang dimiliki kelas tertentu (via tugas_kelas JOIN kelas_mahasiswa).
     * Mahasiswa hanya melihat tugas dari kelas yang diikutinya.
     */
    public function tugasMahasiswa(int $mahasiswaId, string $q = '', string $status = ''): array
    {
        $now = date('Y-m-d H:i:s');

        return $this->db->table('tugas t')
            ->select('t.*, mk.nama_mata_kuliah, mk.kode_mata_kuliah,
                      k.nama_kelas, tk.kelas_id,
                      CASE WHEN t.deadline < NOW() THEN 1 ELSE 0 END AS sudah_lewat,
                      p.id AS pengumpulan_id, p.status AS status_pengumpulan')
            ->join('mata_kuliah mk', 'mk.id = t.mata_kuliah_id')
            ->join('tugas_kelas tk', 'tk.tugas_id = t.id')
            ->join('kelas k', 'k.id = tk.kelas_id')
            ->join('kelas_mahasiswa km', 'km.kelas_id = tk.kelas_id AND km.mahasiswa_id = ' . (int) $mahasiswaId)
            ->join('pengumpulan p', 'p.tugas_id = t.id AND p.mahasiswa_id = ' . (int) $mahasiswaId, 'left')
            ->where('t.status', 'aktif')
            ->where('t.tanggal_mulai <=', $now)
            ->when($q !== '', fn ($b) => $b->like('t.judul', $q))
            ->when($status !== '', function ($b) use ($status, $now) {
                return match ($status) {
                    'belum'    => $b->where('p.id IS NULL'),
                    'sudah'    => $b->where('p.id IS NOT NULL'),
                    'terlambat' => $b->where('t.deadline <', $now)->where('p.id IS NULL'),
                    default    => $b,
                };
            })
            ->orderBy('t.deadline', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Satu tugas + pastikan mahasiswa berhak mengaksesnya.
     */
    public function findUntukMahasiswa(int $tugasId, int $mahasiswaId): ?array
    {
        $now = date('Y-m-d H:i:s');

        $row = $this->db->table('tugas t')
            ->select('t.*, mk.nama_mata_kuliah, mk.kode_mata_kuliah, k.nama_kelas, tk.kelas_id')
            ->join('mata_kuliah mk', 'mk.id = t.mata_kuliah_id')
            ->join('tugas_kelas tk', 'tk.tugas_id = t.id')
            ->join('kelas k', 'k.id = tk.kelas_id')
            ->join('kelas_mahasiswa km', 'km.kelas_id = tk.kelas_id AND km.mahasiswa_id = ' . (int) $mahasiswaId)
            ->where('t.id', $tugasId)
            ->where('t.status', 'aktif')
            ->where('t.tanggal_mulai <=', $now)
            ->limit(1)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    // ----------------------------------------------------------------
    // VALIDASI
    // ----------------------------------------------------------------

    public function validationRules(): array
    {
        return [
            'judul' => [
                'label'  => 'Judul tugas',
                'rules'  => ['required', 'min_length[5]', 'max_length[150]'],
                'errors' => [
                    'required'   => 'Judul tugas wajib diisi.',
                    'min_length' => 'Judul minimal 5 karakter.',
                    'max_length' => 'Judul maksimal 150 karakter.',
                ],
            ],
            'mata_kuliah_id' => [
                'label'  => 'Mata kuliah',
                'rules'  => ['required', 'is_natural_no_zero'],
                'errors' => [
                    'required'           => 'Mata kuliah wajib dipilih.',
                    'is_natural_no_zero' => 'Pilihan mata kuliah tidak valid.',
                ],
            ],
            'tanggal_mulai' => [
                'label'  => 'Tanggal mulai',
                'rules'  => ['required'],
                'errors' => ['required' => 'Tanggal mulai wajib diisi.'],
            ],
            'deadline' => [
                'label'  => 'Deadline',
                'rules'  => ['required'],
                'errors' => ['required' => 'Deadline wajib diisi.'],
            ],
            'batas_ukuran_file' => [
                'label'  => 'Batas ukuran file',
                'rules'  => ['required', 'is_natural_no_zero', 'less_than_equal_to[102400]'],
                'errors' => [
                    'required'              => 'Batas ukuran file wajib diisi.',
                    'is_natural_no_zero'    => 'Batas ukuran harus angka positif.',
                    'less_than_equal_to'    => 'Batas ukuran maksimal 100 MB (102400 KB).',
                ],
            ],
            'status' => [
                'label'  => 'Status',
                'rules'  => ['required', 'in_list[draft,aktif,ditutup]'],
                'errors' => [
                    'required' => 'Status wajib dipilih.',
                    'in_list'  => 'Status tidak valid.',
                ],
            ],
        ];
    }
}
