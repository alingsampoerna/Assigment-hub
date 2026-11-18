<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\NotifikasiModel;
use App\Models\PengumpulanModel;
use App\Models\PenilaianModel;
use App\Models\TugasModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Files\File;

class Pengumpulan extends BaseController
{
    private const UPLOAD_DIR   = 'uploads/pengumpulan';
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

    public function indexByTugas(int $tugasId): string
    {
        $tugas = $this->findTugasOr404($tugasId);
        $model = new PengumpulanModel();

        return view('pages/dosen/pengumpulan_index', [
            'title'       => 'Pengumpulan — ' . $tugas['judul'],
            'activeNav'   => 'tugas',
            'tugas'       => $tugas,
            'pengumpulan' => $model->daftarByTugas($tugasId),
            'statistik'   => $model->statistikTugas($tugasId),
        ]);
    }

    public function show(int $id): string
    {
        $pengumpulan = $this->findOr404($id);

        return view('pages/dosen/pengumpulan_show', [
            'title'       => 'Detail Pengumpulan — ' . $pengumpulan['nama'],
            'activeNav'   => 'tugas',
            'pengumpulan' => $pengumpulan,
        ]);
    }

    public function unduhBerkas(int $id)
    {
        $pengumpulan = $this->findOr404($id);
        $path        = WRITEPATH . self::UPLOAD_DIR . '/' . basename($pengumpulan['file_tugas']);

        if (! is_file($path)) throw PageNotFoundException::forPageNotFound();

        $mime = (new File($path))->getMimeType();
        if (! in_array($mime, self::ALLOWED_MIME, true)) throw PageNotFoundException::forPageNotFound();

        $namaUnduh = $pengumpulan['nama_file_asli'] ?? basename($path);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'attachment; filename="' . rawurlencode($namaUnduh) . '"')
            ->setHeader('Content-Length', (string) filesize($path))
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody((string) file_get_contents($path));
    }

    public function simpanNilai(int $id)
    {
        $pengumpulan = $this->findOr404($id);
        $kembali     = site_url('dosen/pengumpulan/' . $id);

        $rules = [
            'nilai' => [
                'label'  => 'Nilai',
                'rules'  => ['required', 'is_natural', 'less_than_equal_to[100]'],
                'errors' => [
                    'required'           => 'Nilai wajib diisi.',
                    'is_natural'         => 'Nilai harus angka 0–100.',
                    'less_than_equal_to' => 'Nilai maksimal 100.',
                ],
            ],
            'komentar' => [
                'label'  => 'Komentar',
                'rules'  => ['permit_empty', 'max_length[1000]'],
                'errors' => ['max_length' => 'Komentar maksimal 1000 karakter.'],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to($kembali)
                ->with('errors', $this->validator->getErrors())
                ->with('old', $this->request->getPost());
        }

        $nilai    = (int) $this->request->getPost('nilai');
        $komentar = trim((string) $this->request->getPost('komentar'));

        $db = \Config\Database::connect();
        $db->transStart();

        (new PenilaianModel())->simpanAtauPerbarui($id, $this->userId(), $nilai, $komentar);

        (new NotifikasiModel())->kirim(
            (int) $pengumpulan['mahasiswa_id'],
            'Tugas Anda Sudah Dinilai',
            'Tugas "' . $pengumpulan['judul_tugas'] . '" telah dinilai. Nilai: ' . $nilai . '.',
            site_url('mahasiswa/pengumpulan/' . $id)
        );

        $db->transComplete();

        return redirect()->to($kembali)->with('success', 'Nilai berhasil disimpan.');
    }

    private function findOr404(int $id): array
    {
        $row = (new PengumpulanModel())->findUntukAdmin($id);

        if ($row === null) throw PageNotFoundException::forPageNotFound();

        // Pastikan pengumpulan ini milik tugas yang dibuat dosen yang sedang login
        $tugas = (new TugasModel())->find((int) $row['tugas_id']);
        if ($tugas === null || (int) $tugas['dibuat_oleh'] !== $this->userId()) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $row;
    }

    private function findTugasOr404(int $tugasId): array
    {
        $tugas = (new TugasModel())->findDetail($tugasId);

        if ($tugas === null || (int) $tugas['dibuat_oleh'] !== $this->userId()) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $tugas;
    }
}
