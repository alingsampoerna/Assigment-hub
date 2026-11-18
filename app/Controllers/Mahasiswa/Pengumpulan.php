<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\NotifikasiModel;
use App\Models\PengumpulanModel;
use App\Models\RiwayatPengumpulanModel;
use App\Models\TugasModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Files\File;

class Pengumpulan extends BaseController
{
    private const UPLOAD_DIR   = 'uploads/pengumpulan';
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
    // KUMPULKAN TUGAS
    // ----------------------------------------------------------------

    public function store(int $tugasId)
    {
        $userId = $this->userId();
        $tugas  = $this->findTugasOr404($tugasId, $userId);
        $kembali = site_url('mahasiswa/tugas/' . $tugasId);

        // Cegah pengumpulan ganda
        $existing = (new PengumpulanModel())->milikMahasiswa($tugasId, $userId);
        if ($existing !== null) {
            return redirect()->to($kembali)->with('error', 'Anda sudah mengumpulkan tugas ini.');
        }

        // Upload wajib
        [$namaFile, $namaAsli, $errMsg] = $this->prosesUpload($tugas['batas_ukuran_file']);
        if ($errMsg !== null) {
            return redirect()->to($kembali)->with('error', $errMsg);
        }
        if ($namaFile === null) {
            return redirect()->to($kembali)->with('error', 'File tugas wajib diunggah.');
        }

        $sekarang = date('Y-m-d H:i:s');
        $status   = strtotime($sekarang) > strtotime($tugas['deadline']) ? 'terlambat' : 'tepat_waktu';

        $db = \Config\Database::connect();
        $db->transStart();

        $pengumpulanId = (new PengumpulanModel())->insert([
            'tugas_id'            => $tugasId,
            'mahasiswa_id'        => $userId,
            'file_tugas'          => $namaFile,
            'nama_file_asli'      => $namaAsli,
            'tanggal_pengumpulan' => $sekarang,
            'status'              => $status,
            'keterangan'          => trim((string) $this->request->getPost('keterangan')) ?: null,
        ], true);

        // Kirim notifikasi ke admin
        $adminUsers = (new UserModel())->where('role', 'admin')->where('status', 'aktif')->findAll();
        $mahasiswa  = (new UserModel())->find($userId);
        foreach ($adminUsers as $admin) {
            (new NotifikasiModel())->kirim(
                (int) $admin['id'],
                'Pengumpulan Baru: ' . $tugas['judul'],
                ($mahasiswa['nama'] ?? 'Mahasiswa') . ' telah mengumpulkan tugas "' . $tugas['judul'] . '"'
                    . ($status === 'terlambat' ? ' (terlambat)' : '') . '.',
                site_url('admin/pengumpulan/' . (int) $pengumpulanId)
            );
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            if ($namaFile) {
                @unlink(WRITEPATH . self::UPLOAD_DIR . '/' . $namaFile);
            }
            return redirect()->to($kembali)->with('error', 'Gagal menyimpan pengumpulan. Silakan coba lagi.');
        }

        $pesan = $status === 'terlambat'
            ? 'Tugas berhasil dikumpulkan, namun melewati deadline (terlambat).'
            : 'Tugas berhasil dikumpulkan tepat waktu.';

        return redirect()->to(site_url('mahasiswa/pengumpulan/' . (int) $pengumpulanId))
            ->with('success', $pesan);
    }

    // ----------------------------------------------------------------
    // DETAIL PENGUMPULAN MAHASISWA
    // ----------------------------------------------------------------

    public function show(int $id): string
    {
        $userId      = $this->userId();
        $pengumpulan = $this->findOr404($id, $userId);
        $riwayat     = (new RiwayatPengumpulanModel())->forPengumpulan($id);

        return view('pages/mahasiswa/pengumpulan_show', [
            'title'       => 'Detail Pengumpulan',
            'activeNav'   => 'tugas',
            'pengumpulan' => $pengumpulan,
            'riwayat'     => $riwayat,
        ]);
    }

    // ----------------------------------------------------------------
    // UNDUH BERKAS PENGUMPULAN (mahasiswa hanya miliknya)
    // ----------------------------------------------------------------

    public function unduhBerkas(int $id)
    {
        $userId      = $this->userId();
        $pengumpulan = $this->findOr404($id, $userId);

        return $this->streamFile($pengumpulan['file_tugas'], $pengumpulan['nama_file_asli'] ?? null);
    }

    // ----------------------------------------------------------------
    // REVISI
    // ----------------------------------------------------------------

    public function revisi(int $id)
    {
        $userId      = $this->userId();
        $pengumpulan = $this->findOr404($id, $userId);
        $kembali     = site_url('mahasiswa/pengumpulan/' . $id);

        // Cek izin revisi dari tugas
        if (! $pengumpulan['izin_revisi']) {
            return redirect()->to($kembali)->with('error', 'Revisi tidak diizinkan untuk tugas ini.');
        }

        // Cek deadline
        if (strtotime('now') > strtotime($pengumpulan['deadline'])) {
            return redirect()->to($kembali)->with('error', 'Deadline sudah lewat. Revisi tidak dapat dilakukan.');
        }

        [$namaFile, $namaAsli, $errMsg] = $this->prosesUpload($pengumpulan['batas_ukuran_file']);
        if ($errMsg !== null) {
            return redirect()->to($kembali)->with('error', $errMsg);
        }
        if ($namaFile === null) {
            return redirect()->to($kembali)->with('error', 'File pengganti wajib diunggah saat revisi.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Catat riwayat perubahan
        (new RiwayatPengumpulanModel())->catat(
            $id,
            $pengumpulan['file_tugas'],
            $pengumpulan['nama_file_asli'] ?? '',
            $namaFile,
            $namaAsli
        );

        // Perbarui pengumpulan
        (new PengumpulanModel())->update($id, [
            'file_tugas'          => $namaFile,
            'nama_file_asli'      => $namaAsli,
            'tanggal_pengumpulan' => date('Y-m-d H:i:s'),
            'keterangan'          => trim((string) $this->request->getPost('keterangan')) ?: null,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            @unlink(WRITEPATH . self::UPLOAD_DIR . '/' . $namaFile);
            return redirect()->to($kembali)->with('error', 'Gagal menyimpan revisi. Silakan coba lagi.');
        }

        // Hapus file lama setelah transaksi sukses
        if (! empty($pengumpulan['file_tugas'])) {
            @unlink(WRITEPATH . self::UPLOAD_DIR . '/' . basename($pengumpulan['file_tugas']));
        }

        return redirect()->to($kembali)->with('success', 'Revisi berhasil disimpan.');
    }

    // ----------------------------------------------------------------
    // RIWAYAT PENGUMPULAN
    // ----------------------------------------------------------------

    public function riwayat(int $id): string
    {
        $userId      = $this->userId();
        $pengumpulan = $this->findOr404($id, $userId);
        $riwayat     = (new RiwayatPengumpulanModel())->forPengumpulan($id);

        return view('pages/mahasiswa/pengumpulan_riwayat', [
            'title'       => 'Riwayat Pengumpulan',
            'activeNav'   => 'tugas',
            'pengumpulan' => $pengumpulan,
            'riwayat'     => $riwayat,
        ]);
    }

    // ----------------------------------------------------------------
    // HELPER PRIVAT
    // ----------------------------------------------------------------

    private function findOr404(int $id, int $userId): array
    {
        $row = (new PengumpulanModel())->findUntukMahasiswa($id, $userId);

        if ($row === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $row;
    }

    private function findTugasOr404(int $tugasId, int $userId): array
    {
        $tugas = (new TugasModel())->findUntukMahasiswa($tugasId, $userId);

        if ($tugas === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $tugas;
    }

    private function prosesUpload(int $batasKb): array
    {
        $file    = $this->request->getFile('file_tugas');
        $adaFile = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;

        if (! $adaFile) {
            return [null, null, null];
        }

        if (! $file->isValid()) {
            return [null, null, 'File gagal diunggah. Coba pilih file lain.'];
        }

        $ext = strtolower($file->getClientExtension());
        if (! in_array($ext, self::ALLOWED_EXT, true)) {
            return [null, null, 'Format file tidak didukung. Gunakan: ' . implode(', ', self::ALLOWED_EXT) . '.'];
        }

        $mime = $file->getMimeType();
        if (! in_array($mime, self::ALLOWED_MIME, true)) {
            return [null, null, 'Tipe file tidak valid.'];
        }

        if ($file->getSize() > $batasKb * 1024) {
            return [null, null, 'Ukuran file melebihi batas ' . round($batasKb / 1024, 1) . ' MB.'];
        }

        $namaAsli = $file->getClientName();
        $namaFile = $file->getRandomName();
        $file->move(WRITEPATH . self::UPLOAD_DIR, $namaFile);

        return [$namaFile, $namaAsli, null];
    }

    private function streamFile(string $namaFile, ?string $namaAsli)
    {
        $path = WRITEPATH . self::UPLOAD_DIR . '/' . basename($namaFile);

        if (! is_file($path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $mime = (new File($path))->getMimeType();

        if (! in_array($mime, self::ALLOWED_MIME, true)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $unduh = $namaAsli ?? basename($path);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'attachment; filename="' . rawurlencode($unduh) . '"')
            ->setHeader('Content-Length', (string) filesize($path))
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody((string) file_get_contents($path));
    }
}
