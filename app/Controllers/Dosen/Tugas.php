<?php

namespace App\Controllers\Dosen;

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

    public function index(): string
    {
        $model  = new TugasModel();
        $userId = $this->userId();

        $q      = mb_substr($this->strGet('q'), 0, 100);
        $mkId   = $this->strGet('mk');
        $status = $this->strGet('status');

        if (! in_array($status, ['draft', 'aktif', 'ditutup'], true)) {
            $status = '';
        }

        // Dosen hanya melihat tugas yang ia buat
        $builder = $model->where('tugas.dibuat_oleh', $userId);
        if ($q !== '') {
            $builder->groupStart()->like('tugas.judul', $q)->groupEnd();
        }
        if ($mkId !== '') {
            $builder->where('tugas.mata_kuliah_id', $mkId);
        }
        if ($status !== '') {
            $builder->where('tugas.status', $status);
        }

        $total        = $model->hitung($q, $mkId, $status) > 0
            ? (int) \Config\Database::connect()->table('tugas')
                ->join('mata_kuliah mk', 'mk.id = tugas.mata_kuliah_id')
                ->where('tugas.dibuat_oleh', $userId)
                ->when($q !== '', fn($b) => $b->like('tugas.judul', $q))
                ->when($mkId !== '', fn($b) => $b->where('tugas.mata_kuliah_id', $mkId))
                ->when($status !== '', fn($b) => $b->where('tugas.status', $status))
                ->countAllResults()
            : 0;

        $totalHalaman = max(1, (int) ceil($total / self::PER_PAGE));
        $halaman      = min(max((int) $this->strGet('page'), 1), $totalHalaman);

        $items = \Config\Database::connect()->table('tugas')
            ->select('tugas.*, mk.nama_mata_kuliah, mk.kode_mata_kuliah')
            ->join('mata_kuliah mk', 'mk.id = tugas.mata_kuliah_id')
            ->where('tugas.dibuat_oleh', $userId)
            ->when($q !== '', fn($b) => $b->like('tugas.judul', $q))
            ->when($mkId !== '', fn($b) => $b->where('tugas.mata_kuliah_id', $mkId))
            ->when($status !== '', fn($b) => $b->where('tugas.status', $status))
            ->orderBy('tugas.deadline', 'DESC')
            ->limit(self::PER_PAGE, ($halaman - 1) * self::PER_PAGE)
            ->get()->getResultArray();

        $mataKuliah = (new MataKuliahModel())->daftarAktif();
        $filter     = ['q' => $q, 'mk' => $mkId, 'status' => $status];

        return view('pages/dosen/tugas_index', [
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

    public function show(int $id): string
    {
        $tugas     = $this->findOr404($id);
        $kelasList = (new TugasKelasModel())->kelasDariTugas($id);

        return view('pages/dosen/tugas_show', [
            'title'     => 'Detail Tugas — ' . $tugas['judul'],
            'activeNav' => 'tugas',
            'tugas'     => $tugas,
            'kelasList' => $kelasList,
        ]);
    }

    public function create(): string
    {
        return view('pages/dosen/tugas_form', [
            'title'        => 'Buat Tugas',
            'activeNav'    => 'tugas',
            'isEdit'       => false,
            'tugas'        => null,
            'kelasDipilih' => [],
            'mataKuliah'   => (new MataKuliahModel())->daftarAktif(),
            'kelasList'    => (new KelasModel())->daftarAktif(),
        ]);
    }

    public function store()
    {
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
            if ($namaFile) @unlink(WRITEPATH . self::UPLOAD_DIR . '/' . $namaFile);
            return redirect()->back()->with('error', 'Gagal menyimpan tugas.')->with('old', $this->request->getPost());
        }

        return redirect()->to(site_url('dosen/tugas/' . (int) $id))->with('success', 'Tugas berhasil dibuat.');
    }

    public function edit(int $id): string
    {
        $tugas = $this->findOr404($id);

        return view('pages/dosen/tugas_form', [
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
            if ($namaFile) @unlink(WRITEPATH . self::UPLOAD_DIR . '/' . $namaFile);
            return redirect()->back()->with('error', 'Gagal memperbarui tugas.')->with('old', $this->request->getPost());
        }

        if ($namaFile && ! empty($tugas['file_lampiran'])) {
            @unlink(WRITEPATH . self::UPLOAD_DIR . '/' . basename($tugas['file_lampiran']));
        }

        return redirect()->to(site_url('dosen/tugas/' . $id))->with('success', 'Tugas berhasil diperbarui.');
    }

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
            log_message('error', 'Dosen gagal hapus tugas id=' . $id . ': ' . $e->getMessage());
            return redirect()->to(site_url('dosen/tugas'))
                ->with('error', 'Tugas tidak dapat dihapus karena masih ada pengumpulan dari mahasiswa.');
        }

        return redirect()->to(site_url('dosen/tugas'))->with('success', 'Tugas berhasil dihapus.');
    }

    public function unduhLampiran(int $id)
    {
        $tugas = $this->findOr404($id);

        if (empty($tugas['file_lampiran'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        $path = WRITEPATH . self::UPLOAD_DIR . '/' . basename($tugas['file_lampiran']);
        if (! is_file($path)) throw PageNotFoundException::forPageNotFound();

        $mime = (new File($path))->getMimeType();
        if (! in_array($mime, self::ALLOWED_MIME, true)) throw PageNotFoundException::forPageNotFound();

        $namaUnduh = $tugas['nama_lampiran_asli'] ?? basename($path);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'attachment; filename="' . rawurlencode($namaUnduh) . '"')
            ->setHeader('Content-Length', (string) filesize($path))
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody((string) file_get_contents($path));
    }

    private function findOr404(int $id): array
    {
        $tugas = (new TugasModel())->findDetail($id);

        if ($tugas === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Dosen hanya boleh mengelola tugas yang ia buat sendiri
        if ((int) $tugas['dibuat_oleh'] !== $this->userId()) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $tugas;
    }

    private function prosesUpload(): array
    {
        $file    = $this->request->getFile('file_lampiran');
        $adaFile = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;

        if (! $adaFile) return [null, null, null];
        if (! $file->isValid()) return [null, null, 'File gagal diunggah.'];

        $ext = strtolower($file->getClientExtension());
        if (! in_array($ext, self::ALLOWED_EXT, true)) {
            return [null, null, 'Format file tidak didukung. Gunakan: ' . implode(', ', self::ALLOWED_EXT)];
        }

        $mime = $file->getMimeType();
        if (! in_array($mime, self::ALLOWED_MIME, true)) return [null, null, 'Tipe file tidak valid.'];

        if ($file->getSize() > 50 * 1024 * 1024) return [null, null, 'Ukuran file lampiran maksimal 50 MB.'];

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

    private function konversiUkuranFile(): void
    {
        $mb = $this->request->getPost('batas_ukuran_file_mb');
        if ($mb !== null && $mb !== '') {
            $kb = max(1, (int) round((float) $mb * 1024));
            $_POST['batas_ukuran_file'] = (string) $kb;
        }
    }
}
