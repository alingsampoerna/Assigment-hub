<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\MataKuliahModel;
use App\Models\TugasKelasModel;
use App\Models\TugasModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Files\File;

class Tugas extends BaseController
{
    private const PER_PAGE   = 10;
    private const UPLOAD_DIR = 'uploads/tugas';

    // Format file yang diizinkan untuk lampiran tugas
    private const ALLOWED_EXT  = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip'];
    private const ALLOWED_MIME = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/zip',
        'application/x-zip-compressed',
    ];

    // ----------------------------------------------------------------
    // INDEX
    // ----------------------------------------------------------------

    public function index(): string
    {
        $model = new TugasModel();

        $q      = mb_substr($this->strGet('q'), 0, 100);
        $mkId   = $this->strGet('mk');
        $status = $this->strGet('status');

        if (! in_array($status, ['draft', 'aktif', 'ditutup'], true)) {
            $status = '';
        }

        $total        = $model->hitung($q, $mkId, $status);
        $totalHalaman = max(1, (int) ceil($total / self::PER_PAGE));
        $halaman      = min(max((int) $this->strGet('page'), 1), $totalHalaman);

        $items      = $model->daftar($q, $mkId, $status, self::PER_PAGE, ($halaman - 1) * self::PER_PAGE);
        $mataKuliah = (new MataKuliahModel())->daftarAktif();
        $filter     = ['q' => $q, 'mk' => $mkId, 'status' => $status];

        return view('pages/admin/tugas_index', [
            'title'      => 'Kelola Tugas',
            'activeNav'  => 'tugas',
            'items'      => $items,
            'total'      => $total,
            'perPage'    => self::PER_PAGE,
            'page'       => $halaman,
            'totalPages' => $totalHalaman,
            'filter'     => $filter,
            'query'      => array_filter($filter, static fn (string $v): bool => $v !== ''),
            'mataKuliah' => $mataKuliah,
        ]);
    }

    // ----------------------------------------------------------------
    // SHOW
    // ----------------------------------------------------------------

    public function show(int $id): string
    {
        $tugas      = $this->findOr404($id);
        $kelasList  = (new TugasKelasModel())->kelasDariTugas($id);

        return view('pages/admin/tugas_show', [
            'title'     => 'Detail Tugas — ' . $tugas['judul'],
            'activeNav' => 'tugas',
            'tugas'     => $tugas,
            'kelasList' => $kelasList,
        ]);
    }

    // ----------------------------------------------------------------
    // CREATE
    // ----------------------------------------------------------------

    public function create(): string
    {
        return view('pages/admin/tugas_form', [
            'title'      => 'Buat Tugas',
            'activeNav'  => 'tugas',
            'isEdit'     => false,
            'tugas'      => null,
            'kelasDipilih' => [],
            'mataKuliah' => (new MataKuliahModel())->daftarAktif(),
            'kelasList'  => (new KelasModel())->daftarAktif(),
        ]);
    }

    public function store()
    {
        $model = new TugasModel();

        // Konversi MB ke KB di server (tidak bergantung JS browser)
        $this->konversiUkuranFile();

        if (! $this->validate($model->validationRules())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->with('old', $this->request->getPost());
        }

        // Validasi kelas dipilih
        $kelasIds = $this->request->getPost('kelas_ids') ?? [];
        if (empty($kelasIds)) {
            return redirect()->back()
                ->with('errors', ['kelas_ids' => 'Pilih minimal satu kelas penerima tugas.'])
                ->with('old', $this->request->getPost());
        }

        // Proses file lampiran (opsional)
        [$namaFile, $namaAsli, $errMsg] = $this->prosesUpload();
        if ($errMsg !== null) {
            return redirect()->back()
                ->with('errors', ['file_lampiran' => $errMsg])
                ->with('old', $this->request->getPost());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $id = $model->insert([
            'mata_kuliah_id'     => (int) $this->request->getPost('mata_kuliah_id'),
            'dibuat_oleh'        => $this->userId(),
            'judul'              => trim((string) $this->request->getPost('judul')),
            'deskripsi'          => trim((string) $this->request->getPost('deskripsi')) ?: null,
            'file_lampiran'      => $namaFile,
            'nama_lampiran_asli' => $namaAsli,
            'tanggal_mulai'      => $this->request->getPost('tanggal_mulai'),
            'deadline'           => $this->request->getPost('deadline'),
            'batas_ukuran_file'  => (int) $this->request->getPost('batas_ukuran_file'),
            'izin_revisi'        => $this->request->getPost('izin_revisi') ? 1 : 0,
            'status'             => $this->request->getPost('status'),
        ], true);

        (new TugasKelasModel())->simpanKelas((int) $id, $kelasIds);

        $db->transComplete();

        if (! $db->transStatus()) {
            if ($namaFile) {
                @unlink(WRITEPATH . self::UPLOAD_DIR . '/' . $namaFile);
            }
            return redirect()->back()
                ->with('error', 'Gagal menyimpan tugas. Silakan coba lagi.')
                ->with('old', $this->request->getPost());
        }

        return redirect()->to(site_url('admin/tugas/' . (int) $id))
            ->with('success', 'Tugas berhasil dibuat.');
    }

    // ----------------------------------------------------------------
    // EDIT
    // ----------------------------------------------------------------

    public function edit(int $id): string
    {
        $tugas = $this->findOr404($id);

        return view('pages/admin/tugas_form', [
            'title'        => 'Edit Tugas',
            'activeNav'    => 'tugas',
            'isEdit'       => true,
            'tugas'        => $tugas,
            'kelasDipilih' => (new TugasKelasModel())->kelasIdsDariTugas($id),
            'mataKuliah'   => (new MataKuliahModel())->daftarAktif(),
            'kelasList'    => (new KelasModel())->daftarAktif(),
        ]);
    }

    public function update(int $id)
    {
        $tugas = $this->findOr404($id);
        $model = new TugasModel();

        // Konversi MB ke KB di server
        $this->konversiUkuranFile();

        if (! $this->validate($model->validationRules())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->with('old', $this->request->getPost());
        }

        $kelasIds = $this->request->getPost('kelas_ids') ?? [];
        if (empty($kelasIds)) {
            return redirect()->back()
                ->with('errors', ['kelas_ids' => 'Pilih minimal satu kelas penerima tugas.'])
                ->with('old', $this->request->getPost());
        }

        // Proses file lampiran baru (opsional — kosong = pertahankan yang lama)
        [$namaFile, $namaAsli, $errMsg] = $this->prosesUpload();
        if ($errMsg !== null) {
            return redirect()->back()
                ->with('errors', ['file_lampiran' => $errMsg])
                ->with('old', $this->request->getPost());
        }

        $data = [
            'mata_kuliah_id'    => (int) $this->request->getPost('mata_kuliah_id'),
            'judul'             => trim((string) $this->request->getPost('judul')),
            'deskripsi'         => trim((string) $this->request->getPost('deskripsi')) ?: null,
            'tanggal_mulai'     => $this->request->getPost('tanggal_mulai'),
            'deadline'          => $this->request->getPost('deadline'),
            'batas_ukuran_file' => (int) $this->request->getPost('batas_ukuran_file'),
            'izin_revisi'       => $this->request->getPost('izin_revisi') ? 1 : 0,
            'status'            => $this->request->getPost('status'),
        ];

        // Hanya perbarui file jika ada upload baru
        if ($namaFile !== null) {
            $data['file_lampiran']      = $namaFile;
            $data['nama_lampiran_asli'] = $namaAsli;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $model->update($id, $data);
        (new TugasKelasModel())->simpanKelas($id, $kelasIds);

        $db->transComplete();

        if (! $db->transStatus()) {
            if ($namaFile) {
                @unlink(WRITEPATH . self::UPLOAD_DIR . '/' . $namaFile);
            }
            return redirect()->back()
                ->with('error', 'Gagal memperbarui tugas. Silakan coba lagi.')
                ->with('old', $this->request->getPost());
        }

        // Hapus file lama jika berhasil diganti
        if ($namaFile && ! empty($tugas['file_lampiran'])) {
            @unlink(WRITEPATH . self::UPLOAD_DIR . '/' . basename($tugas['file_lampiran']));
        }

        return redirect()->to(site_url('admin/tugas/' . $id))
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    // ----------------------------------------------------------------
    // DELETE
    // ----------------------------------------------------------------

    public function delete(int $id)
    {
        $tugas = $this->findOr404($id);

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            (new TugasKelasModel())->where('tugas_id', $id)->delete();
            (new TugasModel())->delete($id);

            $db->transComplete();

            if ($db->transStatus() && ! empty($tugas['file_lampiran'])) {
                @unlink(WRITEPATH . self::UPLOAD_DIR . '/' . basename($tugas['file_lampiran']));
            }
        } catch (\Throwable $e) {
            log_message('error', 'Gagal hapus tugas id=' . $id . ': ' . $e->getMessage());
            return redirect()->to(site_url('admin/tugas'))
                ->with('error', 'Tugas tidak dapat dihapus karena masih ada pengumpulan dari mahasiswa.');
        }

        return redirect()->to(site_url('admin/tugas'))
            ->with('success', 'Tugas berhasil dihapus.');
    }

    // ----------------------------------------------------------------
    // UNDUH LAMPIRAN
    // ----------------------------------------------------------------

    public function unduhLampiran(int $id)
    {
        $tugas = $this->findOr404($id);

        if (empty($tugas['file_lampiran'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        $path = WRITEPATH . self::UPLOAD_DIR . '/' . basename($tugas['file_lampiran']);

        if (! is_file($path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $mime = (new File($path))->getMimeType();

        // Cegah tipe file berbahaya dieksekusi di browser
        if (! in_array($mime, self::ALLOWED_MIME, true)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $namaUnduh = $tugas['nama_lampiran_asli'] ?? basename($path);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'attachment; filename="' . rawurlencode($namaUnduh) . '"')
            ->setHeader('Content-Length', (string) filesize($path))
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody((string) file_get_contents($path));
    }

    // ----------------------------------------------------------------
    // HELPER
    // ----------------------------------------------------------------

    private function findOr404(int $id): array
    {
        $tugas = (new TugasModel())->findDetail($id);

        if ($tugas === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $tugas;
    }

    /**
     * Proses upload file lampiran.
     * Return [namaFile|null, namaAsli|null, pesanError|null]
     */
    private function prosesUpload(): array
    {
        $file    = $this->request->getFile('file_lampiran');
        $adaFile = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;

        if (! $adaFile) {
            return [null, null, null];
        }

        if (! $file->isValid()) {
            return [null, null, 'File gagal diunggah. Coba pilih file lain.'];
        }

        $ext = strtolower($file->getClientExtension());
        if (! in_array($ext, self::ALLOWED_EXT, true)) {
            return [null, null, 'Format file tidak didukung. Gunakan: ' . implode(', ', self::ALLOWED_EXT)];
        }

        $mime = $file->getMimeType();
        if (! in_array($mime, self::ALLOWED_MIME, true)) {
            return [null, null, 'Tipe file tidak valid.'];
        }

        // Batas ukuran lampiran: 50 MB
        $batasKb = 50 * 1024;
        if ($file->getSize() > $batasKb * 1024) {
            return [null, null, 'Ukuran file lampiran maksimal 50 MB.'];
        }

        // Cegah path traversal — nama file selalu acak
        $namaAsli = $file->getClientName();
        $namaFile = $file->getRandomName();

        $file->move(WRITEPATH . self::UPLOAD_DIR, $namaFile);

        return [$namaFile, $namaAsli, null];
    }

    private function strGet(string $key): string
    {
        $v = $this->request->getGet($key);
        return is_string($v) ? trim($v) : '';
    }

    /**
     * Jika form mengirim batas_ukuran_file_mb (dari input terlihat),
     * konversi ke KB dan inject ke POST sebagai batas_ukuran_file.
     * Ini memastikan validasi server selalu mendapat nilai KB,
     * terlepas dari apakah JS di browser berhasil jalan atau tidak.
     */
    private function konversiUkuranFile(): void
    {
        $mb = $this->request->getPost('batas_ukuran_file_mb');
        if ($mb !== null && $mb !== '') {
            $kb = max(1, (int) round((float) $mb * 1024));
            // Inject ke POST superset agar validasi dan insert memakai nilai KB
            $_POST['batas_ukuran_file'] = (string) $kb;
        }
    }
}
