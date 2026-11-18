<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\PengumpulanModel;
use App\Models\TugasModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Files\File;

class Tugas extends BaseController
{
    private const PER_PAGE   = 10;
    private const UPLOAD_DIR = 'uploads/tugas';

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
        $userId = $this->userId();
        $model  = new TugasModel();

        $q      = mb_substr($this->strGet('q'), 0, 100);
        $status = $this->strGet('status');

        if (! in_array($status, ['belum', 'sudah', 'terlambat'], true)) {
            $status = '';
        }

        $items  = $model->tugasMahasiswa($userId, $q, $status);
        $filter = ['q' => $q, 'status' => $status];

        return view('pages/mahasiswa/tugas_index', [
            'title'     => 'Daftar Tugas',
            'activeNav' => 'tugas',
            'items'     => $items,
            'filter'    => $filter,
            'query'     => array_filter($filter, static fn (string $v): bool => $v !== ''),
        ]);
    }

    // ----------------------------------------------------------------
    // SHOW
    // ----------------------------------------------------------------

    public function show(int $id): string
    {
        $userId = $this->userId();
        $tugas  = $this->findOr404($id, $userId);

        $pengumpulan = (new PengumpulanModel())->milikMahasiswa($id, $userId);

        return view('pages/mahasiswa/tugas_show', [
            'title'       => 'Detail Tugas — ' . $tugas['judul'],
            'activeNav'   => 'tugas',
            'tugas'       => $tugas,
            'pengumpulan' => $pengumpulan,
        ]);
    }

    // ----------------------------------------------------------------
    // UNDUH LAMPIRAN TUGAS
    // ----------------------------------------------------------------

    public function unduhLampiran(int $id)
    {
        $tugas = $this->findOr404($id, $this->userId());

        if (empty($tugas['file_lampiran'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        $path = WRITEPATH . self::UPLOAD_DIR . '/' . basename($tugas['file_lampiran']);

        if (! is_file($path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $mime = (new File($path))->getMimeType();

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

    private function findOr404(int $id, int $userId): array
    {
        $tugas = (new TugasModel())->findUntukMahasiswa($id, $userId);

        if ($tugas === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $tugas;
    }

    private function strGet(string $key): string
    {
        $v = $this->request->getGet($key);
        return is_string($v) ? trim($v) : '';
    }
}
