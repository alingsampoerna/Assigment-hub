<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $table         = 'reports';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'kategori_id', 'judul', 'lokasi', 'deskripsi', 'foto', 'foto_alt', 'status'];

    // ------------------------------------------------------------
    // MAHASISWA
    // ------------------------------------------------------------

    /**
     * Jumlah laporan milik satu pengguna per status.
     */
    public function statistikUser(int $userId): array
    {
        $stats = ['total' => 0, 'menunggu' => 0, 'diproses' => 0, 'selesai' => 0, 'ditolak' => 0];

        $rows = $this->db->table('reports')
            ->select('status, COUNT(*) AS jumlah')
            ->where('user_id', $userId)
            ->groupBy('status')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            if (isset($stats[$row['status']])) {
                $stats[$row['status']] = (int) $row['jumlah'];
                $stats['total']       += (int) $row['jumlah'];
            }
        }

        return $stats;
    }

    /**
     * Laporan terbaru milik satu pengguna (dengan nama kategori).
     */
    public function terbaruUser(int $userId, int $limit): array
    {
        return $this->daftarDasar()
            ->where('reports.user_id', $userId)
            ->orderBy('reports.created_at', 'DESC')
            ->orderBy('reports.id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Jumlah laporan milik satu pengguna sesuai filter (untuk pagination).
     */
    public function hitungUser(int $userId, array $filter): int
    {
        $builder = $this->db->table('reports')->where('reports.user_id', $userId);
        $this->terapkanFilter($builder, $filter);

        return (int) $builder->countAllResults();
    }

    /**
     * Daftar laporan milik satu pengguna sesuai filter, satu halaman.
     */
    public function daftarUser(int $userId, array $filter, int $limit, int $offset): array
    {
        $builder = $this->daftarDasar()->where('reports.user_id', $userId);
        $this->terapkanFilter($builder, $filter);

        return $builder
            ->orderBy('reports.created_at', 'DESC')
            ->orderBy('reports.id', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * Satu laporan, hanya jika milik pengguna tersebut. Selain itu mengembalikan null.
     * (Pencegahan IDOR: kepemilikan dicek di query, bukan di tampilan.)
     */
    public function findForUser(int $id, int $userId): ?array
    {
        $row = $this->daftarDasar()
            ->where('reports.id', $id)
            ->where('reports.user_id', $userId)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    /**
     * Nama petugas pada penugasan terakhir, atau null bila belum ditugaskan.
     */
    public function petugasTerakhir(int $reportId): ?string
    {
        $row = $this->db->table('report_assignments ra')
            ->select('o.nama')
            ->join('officers o', 'o.id = ra.officer_id')
            ->where('ra.report_id', $reportId)
            ->orderBy('ra.id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        return $row['nama'] ?? null;
    }

    /**
     * Menyimpan laporan baru beserta riwayat pertamanya dalam satu transaksi.
     * Mengembalikan ID laporan; melempar exception (dan membatalkan semuanya) bila gagal.
     */
    public function simpanBaru(array $laporan, int $userId): int
    {
        $this->db->transException(true)->transStart();

        $this->insert($laporan + ['user_id' => $userId, 'status' => 'menunggu']);
        $id = (int) $this->getInsertID();

        (new ReportHistoryModel())->insert([
            'report_id'  => $id,
            'status'     => 'menunggu',
            'catatan'    => 'Laporan dikirim.',
            'changed_by' => $userId,
            'waktu'      => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();

        return $id;
    
        }
    // ------------------------------------------------------------
    // ADMIN
    // ------------------------------------------------------------
    /**
     * Jumlah laporan per status untuk seluruh mahasiswa.
     * $awal (termasuk) dan $akhir (tidak termasuk) membatasi periode; null berarti tanpa batas.
     */
    public function statistik(?string $awal = null, ?string $akhir = null): array
    {
        $stats = ['total' => 0, 'menunggu' => 0, 'diproses' => 0, 'selesai' => 0, 'ditolak' => 0];

        $builder = $this->db->table('reports')
            ->select('status, COUNT(*) AS jumlah')
            ->groupBy('status');
        $this->terapkanRentang($builder, $awal, $akhir);

        foreach ($builder->get()->getResultArray() as $row) {
            if (isset($stats[$row['status']])) {
                $stats[$row['status']] = (int) $row['jumlah'];
                $stats['total']       += (int) $row['jumlah'];
            }
        }

        return $stats;
    }

    /**
     * Jumlah laporan per kategori, urut dari yang terbanyak. Semua kategori ikut, termasuk yang 0.
     * Hasil: [['id' => int, 'nama' => string, 'jumlah' => int], ...]
     */
    public function perKategori(array $kategori, ?string $awal = null, ?string $akhir = null): array
    {
        $builder = $this->db->table('reports')
            ->select('kategori_id, COUNT(*) AS jumlah')
            ->groupBy('kategori_id');
        $this->terapkanRentang($builder, $awal, $akhir);

        $hitung = [];
        foreach ($builder->get()->getResultArray() as $row) {
            $hitung[(int) $row['kategori_id']] = (int) $row['jumlah'];
        }

        $hasil = [];
        foreach ($kategori as $id => $nama) {
            $hasil[] = ['id' => (int) $id, 'nama' => $nama, 'jumlah' => $hitung[(int) $id] ?? 0];
        }

        // Pengurutan stabil: kategori dengan jumlah sama tetap urut berdasarkan ID
        usort($hasil, static fn (array $a, array $b): int => $b['jumlah'] <=> $a['jumlah']);

        return $hasil;
    }

    /**
     * Jumlah laporan per bulan: ['2026-10' => 6, '2026-11' => 12], urut dari bulan terlama.
     */
    public function perBulan(): array
    {
        $rows = $this->db->table('reports')
            ->select("DATE_FORMAT(created_at, '%Y-%m') AS bulan, COUNT(*) AS jumlah", false)
            ->where('created_at IS NOT NULL')
            ->groupBy('bulan')
            ->orderBy('bulan', 'ASC')
            ->get()
            ->getResultArray();

        $hasil = [];
        foreach ($rows as $row) {
            $hasil[$row['bulan']] = (int) $row['jumlah'];
        }

        return $hasil;
    }

    /**
     * Laporan terbaru dari semua mahasiswa (dengan kategori dan data pelapor).
     */
    public function terbaruAdmin(int $limit): array
    {
        return $this->daftarAdminDasar()
            ->orderBy('reports.created_at', 'DESC')
            ->orderBy('reports.id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Jumlah laporan sesuai filter admin (untuk pagination).
     */
    public function hitungAdmin(array $filter): int
    {
        $builder = $this->db->table('reports')
            ->join('users', 'users.id = reports.user_id');
        $this->terapkanFilterAdmin($builder, $filter);

        return (int) $builder->countAllResults();
    }

    /**
     * Daftar laporan sesuai filter admin, satu halaman.
     */
    public function daftarAdmin(array $filter, int $limit, int $offset): array
    {
        $builder = $this->daftarAdminDasar();
        $this->terapkanFilterAdmin($builder, $filter);

        return $builder
            ->orderBy('reports.created_at', 'DESC')
            ->orderBy('reports.id', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * Satu laporan lengkap untuk admin (kategori, pelapor, NIM, dan petugas terakhir), atau null.
     */
    public function findAdmin(int $id): ?array
    {
        $row = $this->daftarAdminDasar()->where('reports.id', $id)->get()->getRowArray();

        if (! $row) {
            return null;
        }

        $row['petugas'] = $this->petugasTerakhir((int) $row['id']);

        return $row;
    }

    /**
     * Mengubah status laporan dalam satu transaksi:
     * update status + (opsional) catat penugasan petugas + catat riwayat.
     *
     * $statusLama dipakai sebagai syarat (WHERE status = ...). Jika laporan sudah diubah
     * admin lain lebih dulu, tidak ada baris yang berubah, transaksi dibatalkan, dan
     * method ini mengembalikan false.
     *
     * Melempar exception bila terjadi kegagalan database (semua perubahan dibatalkan).
     */
    public function ubahStatus(
        int $id,
        string $statusLama,
        string $statusBaru,
        ?int $officerId,
        string $catatan,
        int $adminId
    ): bool {
        $sekarang = date('Y-m-d H:i:s');

        $this->db->transException(true)->transStart();

        $this->db->table('reports')
            ->where('id', $id)
            ->where('status', $statusLama)
            ->update(['status' => $statusBaru, 'updated_at' => $sekarang]);

        if ($this->db->affectedRows() !== 1) {
            $this->db->transRollback();

            return false;
        }

        if ($officerId !== null) {
            (new AssignmentModel())->insert([
                'report_id'   => $id,
                'officer_id'  => $officerId,
                'assigned_by' => $adminId,
                'created_at'  => $sekarang,
            ]);
        }

        (new ReportHistoryModel())->insert([
            'report_id'  => $id,
            'status'     => $statusBaru,
            'catatan'    => $catatan,
            'changed_by' => $adminId,
            'waktu'      => $sekarang,
        ]);

        $this->db->transComplete();

        return true;
    }

    /**
     * Query dasar admin: laporan + kategori + pelapor (alias "pelapor" dan "nim").
     */
    private function daftarAdminDasar(): BaseBuilder
    {
        return $this->db->table('reports')
            ->select('reports.*, categories.nama AS kategori, users.nama AS pelapor, users.nim AS nim')
            ->join('categories', 'categories.id = reports.kategori_id')
            ->join('users', 'users.id = reports.user_id');
    }

    /**
     * Filter admin: status, kategori, rentang tanggal, dan pencarian
     * (judul, lokasi, nama pelapor, atau NIM). Semua nilai dipasang lewat binding.
     */
    private function terapkanFilterAdmin(BaseBuilder $builder, array $filter): void
    {
        if (($filter['status'] ?? '') !== '') {
            $builder->where('reports.status', $filter['status']);
        }

        if (($filter['kategori'] ?? '') !== '') {
            $builder->where('reports.kategori_id', (int) $filter['kategori']);
        }

        if (($filter['dari'] ?? '') !== '') {
            $builder->where('reports.created_at >=', $filter['dari'] . ' 00:00:00');
        }

        if (($filter['sampai'] ?? '') !== '') {
            // Batas atas dibuat "kurang dari hari berikutnya" agar seluruh hari terakhir ikut
            $hariBerikutnya = date('Y-m-d', strtotime($filter['sampai'] . ' +1 day'));
            $builder->where('reports.created_at <', $hariBerikutnya . ' 00:00:00');
        }

        if (($filter['q'] ?? '') !== '') {
            $builder->groupStart()
                ->like('reports.judul', $filter['q'])
                ->orLike('reports.lokasi', $filter['q'])
                ->orLike('users.nama', $filter['q'])
                ->orLike('users.nim', $filter['q'])
                ->groupEnd();
        }
    }

    private function terapkanRentang(BaseBuilder $builder, ?string $awal, ?string $akhir): void
    {
        if ($awal !== null) {
            $builder->where('created_at >=', $awal);
        }

        if ($akhir !== null) {
            $builder->where('created_at <', $akhir);
        }
    }

    // ------------------------------------------------------------
    // BANTUAN
    // ------------------------------------------------------------

    /**
     * Query dasar: laporan + nama kategori (alias "kategori").
     */
    private function daftarDasar(): BaseBuilder
    {
        return $this->db->table('reports')
            ->select('reports.*, categories.nama AS kategori')
            ->join('categories', 'categories.id = reports.kategori_id');
    }

    /**
     * Menerapkan filter q, status, dan kategori. Nilai dipasang lewat binding,
     * dan karakter khusus LIKE (% dan _) dinetralkan oleh query builder.
     */
    private function terapkanFilter(BaseBuilder $builder, array $filter): void
    {
        if (($filter['status'] ?? '') !== '') {
            $builder->where('reports.status', $filter['status']);
        }

        if (($filter['kategori'] ?? '') !== '') {
            $builder->where('reports.kategori_id', (int) $filter['kategori']);
        }

        if (($filter['q'] ?? '') !== '') {
            $builder->groupStart()
                ->like('reports.judul', $filter['q'])
                ->orLike('reports.lokasi', $filter['q'])
                ->groupEnd();
        }
    }
}