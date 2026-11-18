<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\OfficerModel;
use App\Models\ReportHistoryModel;
use App\Models\ReportModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Files\File;

class Laporan extends BaseController
{
    private const PER_PAGE   = 8;
    private const UPLOAD_DIR = 'uploads/laporan'; // relatif terhadap folder writable/

    // ------------------------------------------------------------
    // SEMUA LAPORAN
    // ------------------------------------------------------------

    public function index(): string
    {
        $model      = new ReportModel();
        $kategori   = (new CategoryModel())->daftar();
        $statusList = cc_status_list();

        // Baca dan bersihkan parameter dari URL
        $q          = mb_substr($this->getString('q'), 0, 100);
        $status     = $this->getString('status');
        $kategoriId = (int) $this->getString('kategori');
        $dari       = $this->tanggalValid($this->getString('dari'));
        $sampai     = $this->tanggalValid($this->getString('sampai'));

        if (! isset($statusList[$status])) {
            $status = '';
        }
        $kategoriId = isset($kategori[$kategoriId]) ? (string) $kategoriId : '';

        // Jika rentang terbalik, tukar agar tetap menghasilkan data
        if ($dari !== '' && $sampai !== '' && $dari > $sampai) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        $filter = [
            'q'        => $q,
            'status'   => $status,
            'kategori' => $kategoriId,
            'dari'     => $dari,
            'sampai'   => $sampai,
        ];

        // Pagination: hitung total dulu agar nomor halaman bisa dibatasi ke rentang yang valid
        $total        = $model->hitungAdmin($filter);
        $totalHalaman = max(1, (int) ceil($total / self::PER_PAGE));
        $halaman      = min(max((int) $this->getString('page'), 1), $totalHalaman);

        $items = $model->daftarAdmin($filter, self::PER_PAGE, ($halaman - 1) * self::PER_PAGE);

        return view('pages/admin/laporan_index', [
            'title'      => 'Semua Laporan - CampusCare Admin',
            'activeNav'  => 'laporan',
            'reports'    => $items,
            'total'      => $total,
            'totalSemua' => $model->statistik()['total'],
            'perPage'    => self::PER_PAGE,
            'page'       => $halaman,
            'totalPages' => $totalHalaman,
            'filter'     => $filter,
            'query'      => array_filter($filter, static fn (string $v): bool => $v !== ''),
            'kategori'   => $kategori,
            'statusList' => $statusList,
        ]);
    }

    // ------------------------------------------------------------
    // DETAIL LAPORAN
    // ------------------------------------------------------------

    public function show(int $id): string
    {
        $report  = $this->laporanAtau404($id);
        $riwayat = (new ReportHistoryModel())->forReport($id);

        // Opsi dropdown petugas: [id => "Nama (bidang)"]
        $opsiPetugas = [];
        foreach ((new OfficerModel())->daftar() as $petugasId => $petugas) {
            $opsiPetugas[$petugasId] = $petugas['nama'] . ' (' . $petugas['bidang'] . ')';
        }

        // Alasan penolakan diambil dari catatan riwayat terakhir
        $alasanDitolak = null;
        if ($report['status'] === 'ditolak' && $riwayat !== []) {
            $terakhir      = end($riwayat);
            $alasanDitolak = $terakhir['catatan'] ?? null;
        }

        $adaFoto = $this->pathFoto($report) !== null;

        return view('pages/admin/laporan_detail', [
            'title'         => 'Detail Laporan - CampusCare Admin',
            'activeNav'     => 'laporan',
            'report'        => $report,
            'riwayat'       => $riwayat,
            'opsiPetugas'   => $opsiPetugas,
            'alasanDitolak' => $alasanDitolak,
            'fotoUrl'       => $adaFoto ? site_url('admin/laporan/' . (int) $report['id'] . '/foto') : null,
            'fotoAlt'       => ! empty($report['foto_alt']) ? $report['foto_alt'] : 'Foto kerusakan: ' . $report['judul'],
        ]);
    }

    // ------------------------------------------------------------
    // UBAH STATUS (proses | tolak | selesai)
    // ------------------------------------------------------------

    public function updateStatus(int $id)
    {
        $model   = new ReportModel();
        $report  = $this->laporanAtau404($id);
        $petugas = (new OfficerModel())->daftar();
        $tujuan  = site_url('admin/laporan/' . $id);
        $aksi    = (string) $this->request->getPost('aksi');

        // Aturan alur status: tindakan => [status asal, status tujuan]
        $alur = [
            'proses'  => ['menunggu', 'diproses'],
            'tolak'   => ['menunggu', 'ditolak'],
            'selesai' => ['diproses', 'selesai'],
        ];

        // Diperiksa di server: tindakan tidak dikenal atau tidak sesuai status saat ini
        if (! isset($alur[$aksi]) || $report['status'] !== $alur[$aksi][0]) {
            $labelSaatIni = cc_status_list()[$report['status']] ?? $report['status'];

            return redirect()->to($tujuan)
                ->with('error', 'Tindakan tersebut tidak dapat dilakukan karena laporan berstatus ' . $labelSaatIni . '.');
        }

        [$statusLama, $statusBaru] = $alur[$aksi];

        $rules = match ($aksi) {
            'proses' => [
                'petugas_id' => [
                    'label'  => 'Petugas',
                    'rules'  => ['required', 'in_list[' . (implode(',', array_keys($petugas)) ?: '0') . ']'],
                    'errors' => [
                        'required' => 'Pilih petugas yang akan menangani laporan.',
                        'in_list'  => 'Pilih petugas yang tersedia pada daftar.',
                    ],
                ],
                'catatan_proses' => [
                    'label'  => 'Catatan',
                    'rules'  => ['permit_empty', 'max_length[300]'],
                    'errors' => ['max_length' => 'Catatan maksimal 300 karakter.'],
                ],
            ],
            'tolak' => [
                'alasan' => [
                    'label'  => 'Alasan penolakan',
                    'rules'  => ['required', 'min_length[10]', 'max_length[300]'],
                    'errors' => [
                        'required'   => 'Alasan penolakan wajib diisi.',
                        'min_length' => 'Alasan minimal 10 karakter agar mahasiswa memahaminya.',
                        'max_length' => 'Alasan maksimal 300 karakter.',
                    ],
                ],
            ],
            'selesai' => [
                'catatan_selesai' => [
                    'label'  => 'Catatan penyelesaian',
                    'rules'  => ['required', 'min_length[5]', 'max_length[300]'],
                    'errors' => [
                        'required'   => 'Catatan penyelesaian wajib diisi.',
                        'min_length' => 'Catatan minimal 5 karakter.',
                        'max_length' => 'Catatan maksimal 300 karakter.',
                    ],
                ],
            ],
        };

        if (! $this->validate($rules)) {
            return redirect()->to($tujuan)
                ->with('errors', $this->validator->getErrors())
                ->with('old', $this->request->getPost(['petugas_id', 'catatan_proses', 'alasan', 'catatan_selesai']))
                ->with('form_error', $aksi);
        }

        // Siapkan data perubahan
        $officerId = null;

        if ($aksi === 'proses') {
            $officerId   = (int) $this->request->getPost('petugas_id');
            $namaPetugas = $petugas[$officerId]['nama'];
            $tambahan    = trim((string) $this->request->getPost('catatan_proses'));
            $catatan     = 'Petugas ' . $namaPetugas . ' ditugaskan.' . ($tambahan !== '' ? ' ' . $tambahan : '');
            $pesan       = 'Laporan diproses dan ' . $namaPetugas . ' ditugaskan.';
        } elseif ($aksi === 'tolak') {
            $catatan = trim((string) $this->request->getPost('alasan'));
            $pesan   = 'Laporan ditolak. Alasan akan terlihat oleh mahasiswa.';
        } else {
            $catatan = trim((string) $this->request->getPost('catatan_selesai'));
            $pesan   = 'Laporan ditandai selesai.';
        }

        try {
            $berhasil = $model->ubahStatus($id, $statusLama, $statusBaru, $officerId, $catatan, $this->userId());
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mengubah status laporan #' . $id . ': ' . $e->getMessage());

            return redirect()->to($tujuan)
                ->with('error', 'Perubahan gagal disimpan. Tidak ada data yang berubah. Silakan coba lagi.');
        }

        if (! $berhasil) {
            // Admin lain mengubah laporan ini lebih dulu
            return redirect()->to($tujuan)
                ->with('error', 'Status laporan baru saja diubah oleh admin lain. Halaman telah dimuat ulang dengan data terbaru.');
        }

        return redirect()->to($tujuan)->with('success', $pesan);
    }

    // ------------------------------------------------------------
    // FOTO LAPORAN (disajikan lewat controller, bukan URL langsung)
    // ------------------------------------------------------------

    public function foto(int $id)
    {
        $report = $this->laporanAtau404($id);
        $path   = $this->pathFoto($report);

        if ($path === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Tipe diperiksa dari isi file, bukan dari nama file
        $mime = (new File($path))->getMimeType();
        if (! in_array($mime, ['image/jpeg', 'image/png'], true)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody((string) file_get_contents($path));
    }

    // ------------------------------------------------------------
    // BANTUAN
    // ------------------------------------------------------------

    private function laporanAtau404(int $id): array
    {
        $report = (new ReportModel())->findAdmin($id);

        if ($report === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $report;
    }

    private function pathFoto(array $report): ?string
    {
        if (empty($report['foto'])) {
            return null;
        }

        $path = WRITEPATH . self::UPLOAD_DIR . '/' . basename($report['foto']);

        return is_file($path) ? $path : null;
    }

    /**
     * Membaca parameter GET sebagai string; nilai non-string (misalnya ?q[]=x) dianggap kosong.
     */
    private function getString(string $key): string
    {
        $value = $this->request->getGet($key);

        return is_string($value) ? trim($value) : '';
    }

    /**
     * Mengembalikan tanggal (format Y-m-d) jika benar-benar valid di kalender, atau string kosong.
     */
    private function tanggalValid(string $value): string
    {
        $date = \DateTime::createFromFormat('Y-m-d', $value);

        return ($date !== false && $date->format('Y-m-d') === $value) ? $value : '';
    }
}