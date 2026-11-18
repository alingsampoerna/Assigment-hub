<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ReportHistoryModel;
use App\Models\ReportModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Files\File;

class Laporan extends BaseController
{
    private const MAX_FOTO_KB = 2048;              // 2 MB (dihubungkan ke Pengaturan di tahap 14)
    private const UPLOAD_DIR  = 'uploads/laporan'; // relatif terhadap folder writable/
    private const PER_PAGE    = 5;                 // ubah ke 10 saat produksi

    // ------------------------------------------------------------
    // FORM BUAT LAPORAN
    // ------------------------------------------------------------

    public function create(): string
    {
        return view('pages/mahasiswa/laporan_form', [
            'title'     => 'Buat Laporan - CampusCare',
            'activeNav' => 'buat-laporan',
            'kategori'  => (new CategoryModel())->daftar(),
            'maxFotoKb' => $this->batasFotoKb(),
        ]);
    }

    // ------------------------------------------------------------
    // SIMPAN LAPORAN
    // ------------------------------------------------------------

    public function store()
    {
        $kategori = (new CategoryModel())->daftar();
        $file     = $this->request->getFile('foto');
        $adaFile  = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;
        $kembali  = site_url('mahasiswa/laporan/baru');
        $batasKb  = $this->batasFotoKb();

        $rules = [
            'judul' => [
                'label'  => 'Judul laporan',
                'rules'  => ['required', 'min_length[5]', 'max_length[100]'],
                'errors' => [
                    'required'   => 'Judul laporan wajib diisi.',
                    'min_length' => 'Judul minimal 5 karakter.',
                    'max_length' => 'Judul maksimal 100 karakter.',
                ],
            ],
            'kategori_id' => [
                'label'  => 'Kategori fasilitas',
                'rules'  => ['required', 'in_list[' . (implode(',', array_keys($kategori)) ?: '0') . ']'],
                'errors' => [
                    'required' => 'Kategori fasilitas wajib dipilih.',
                    'in_list'  => 'Pilih kategori yang tersedia pada daftar.',
                ],
            ],
            'lokasi' => [
                'label'  => 'Lokasi kerusakan',
                'rules'  => ['required', 'min_length[3]', 'max_length[150]'],
                'errors' => [
                    'required'   => 'Lokasi kerusakan wajib diisi.',
                    'min_length' => 'Lokasi minimal 3 karakter.',
                    'max_length' => 'Lokasi maksimal 150 karakter.',
                ],
            ],
            'deskripsi' => [
                'label'  => 'Deskripsi kerusakan',
                'rules'  => ['required', 'min_length[10]', 'max_length[500]'],
                'errors' => [
                    'required'   => 'Deskripsi kerusakan wajib diisi.',
                    'min_length' => 'Deskripsi minimal 10 karakter agar petugas memahami masalahnya.',
                    'max_length' => 'Deskripsi maksimal 500 karakter.',
                ],
            ],
            'foto_alt' => [
                'label'  => 'Deskripsi foto',
                'rules'  => ['permit_empty', 'max_length[150]'],
                'errors' => ['max_length' => 'Deskripsi foto maksimal 150 karakter.'],
            ],
        ];

        // Aturan foto hanya dipakai jika pengguna memilih file (foto bersifat opsional)
        if ($adaFile) {
    $rules['foto'] = [
        'label'  => 'Foto kerusakan',
        'rules'  => [
            'uploaded[foto]',
            'is_image[foto]',
            'mime_in[foto,image/jpg,image/jpeg,image/png]',
            'ext_in[foto,jpg,jpeg,png]',
            'max_size[foto,' . $batasKb . ']',
        ],
        'errors' => [
            'uploaded' => 'Foto gagal diunggah. Coba pilih file lain.',
            'is_image' => 'File yang dipilih bukan gambar yang valid.',
            'mime_in'  => 'Format foto harus JPG atau PNG.',
            'ext_in'   => 'Format foto harus JPG atau PNG.',
            'max_size' => 'Ukuran foto maksimal ' . $this->formatMb($batasKb) . ' MB.',
        ],
    ];
}

        $old = $this->request->getPost(['judul', 'kategori_id', 'lokasi', 'deskripsi', 'foto_alt']);

        if (! $this->validate($rules)) {
            return redirect()->to($kembali)
                ->with('errors', $this->validator->getErrors())
                ->with('old', $old)
                ->with('foto_reset', $adaFile); // browser tidak menyimpan file yang dipilih
        }

        // Simpan foto (jika ada) ke folder writable, bukan public
        $namaFile = null;
        if ($adaFile && $file->isValid() && ! $file->hasMoved()) {
            $namaFile = $file->getRandomName(); // nama acak, ekstensi dari tipe file asli
            $file->move(WRITEPATH . self::UPLOAD_DIR, $namaFile);
        }

        $altFoto = trim((string) $this->request->getPost('foto_alt'));

        try {
            // Laporan dan riwayat pertamanya disimpan dalam satu transaksi
            $id = (new ReportModel())->simpanBaru([
                'kategori_id' => (int) $this->request->getPost('kategori_id'),
                'judul'       => trim((string) $this->request->getPost('judul')),
                'lokasi'      => trim((string) $this->request->getPost('lokasi')),
                'deskripsi'   => trim((string) $this->request->getPost('deskripsi')),
                'foto'        => $namaFile,
                'foto_alt'    => $altFoto !== '' ? $altFoto : null,
            ], $this->userId());
        } catch (\Throwable $e) {
            // Gagal menyimpan: hapus foto yang sudah terlanjur diunggah
            if ($namaFile !== null) {
                @unlink(WRITEPATH . self::UPLOAD_DIR . '/' . $namaFile);
            }
            log_message('error', 'Gagal menyimpan laporan: ' . $e->getMessage());

            return redirect()->to($kembali)
                ->with('error', 'Laporan gagal disimpan. Silakan coba lagi.')
                ->with('old', $old)
                ->with('foto_reset', $adaFile);
        }

        return redirect()->to(site_url('mahasiswa/laporan/' . $id))
            ->with('success', 'Laporan berhasil dikirim. Anda dapat memantau statusnya di halaman ini.');
    }

    // ------------------------------------------------------------
    // DAFTAR LAPORAN SAYA
    // ------------------------------------------------------------

    public function index(): string
    {
        $userId     = $this->userId();
        $model      = new ReportModel();
        $kategori   = (new CategoryModel())->daftar();
        $statusList = cc_status_list();

        // Baca dan bersihkan parameter dari URL
        $q          = mb_substr($this->getString('q'), 0, 100);
        $status     = $this->getString('status');
        $kategoriId = (int) $this->getString('kategori');

        if (! isset($statusList[$status])) {
            $status = '';
        }
        $kategoriId = isset($kategori[$kategoriId]) ? (string) $kategoriId : '';

        $filter = ['q' => $q, 'status' => $status, 'kategori' => $kategoriId];

        // Pagination: hitung total dulu agar nomor halaman bisa dibatasi ke rentang yang valid
        $total        = $model->hitungUser($userId, $filter);
        $totalHalaman = max(1, (int) ceil($total / self::PER_PAGE));
        $halaman      = min(max((int) $this->getString('page'), 1), $totalHalaman);

        $items = $model->daftarUser($userId, $filter, self::PER_PAGE, ($halaman - 1) * self::PER_PAGE);

        return view('pages/mahasiswa/laporan_saya', [
            'title'      => 'Laporan Saya - CampusCare',
            'activeNav'  => 'laporan-saya',
            'reports'    => $items,
            'total'      => $total,
            'totalSemua' => $model->statistikUser($userId)['total'],
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
        $report            = $this->laporanSaya($id);
        $report['petugas'] = (new ReportModel())->petugasTerakhir($id);
        $riwayat           = (new ReportHistoryModel())->forReport($id);

        // Alasan penolakan diambil dari catatan riwayat terakhir
        $alasanDitolak = null;
        if ($report['status'] === 'ditolak' && $riwayat !== []) {
            $terakhir      = end($riwayat);
            $alasanDitolak = $terakhir['catatan'] ?? null;
        }

        $adaFoto = $this->pathFoto($report) !== null;

        return view('pages/mahasiswa/laporan_detail', [
            'title'         => 'Detail Laporan - CampusCare',
            'activeNav'     => 'laporan-saya',
            'report'        => $report,
            'riwayat'       => $riwayat,
            'alasanDitolak' => $alasanDitolak,
            'fotoUrl'       => $adaFoto ? site_url('mahasiswa/laporan/' . (int) $report['id'] . '/foto') : null,
            'fotoAlt'       => ! empty($report['foto_alt']) ? $report['foto_alt'] : 'Foto kerusakan: ' . $report['judul'],
        ]);
    }

    // ------------------------------------------------------------
    // FOTO LAPORAN (disajikan lewat controller, bukan URL langsung)
    // ------------------------------------------------------------

    public function foto(int $id)
    {
        $report = $this->laporanSaya($id); // 404 jika bukan milik pengguna
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

    /**
     * Laporan milik pengguna yang sedang login.
     * Tidak ada ATAU milik orang lain sama-sama menghasilkan 404,
     * agar keberadaan ID laporan orang lain tidak bocor.
     */
    private function laporanSaya(int $id): array
    {
        $report = (new ReportModel())->findForUser($id, $this->userId());

        if ($report === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $report;
    }

    /**
     * Lokasi file foto di disk, atau null bila laporan tidak punya foto / file tidak ada.
     */
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
     * Batas ukuran foto (KB): dari pengaturan admin, tetapi tidak melebihi batas PHP di server.
     */
    private function batasFotoKb(): int
    {
        $batasKb = max(1, (int) cc_setting('maks_upload_mb', '2')) * 1024;

        // Batas dari php.ini (nilai 0 berarti tanpa batas, jadi diabaikan)
        $batasServer = array_filter([
            $this->iniKeBytes((string) ini_get('upload_max_filesize')),
            $this->iniKeBytes((string) ini_get('post_max_size')),
        ], static fn (int $bytes): bool => $bytes > 0);

        if ($batasServer !== []) {
            $batasKb = min($batasKb, (int) floor(min($batasServer) / 1024));
        }

        return max(1, $batasKb);
    }

    /**
     * Mengubah nilai php.ini seperti "40M" atau "512K" menjadi byte.
     */
    private function iniKeBytes(string $nilai): int
    {
        $nilai = trim($nilai);
        if ($nilai === '') {
            return 0;
        }

        $angka  = (float) $nilai;
        $satuan = strtolower(substr($nilai, -1));

        return (int) match ($satuan) {
            'g'     => $angka * 1024 ** 3,
            'm'     => $angka * 1024 ** 2,
            'k'     => $angka * 1024,
            default => $angka,
        };
    }

    /**
     * Teks ukuran dalam MB untuk pesan, contoh: "2" atau "1,5".
     */
    private function formatMb(int $kb): string
    {
        return str_replace('.', ',', (string) round($kb / 1024, 1));
    }
}

