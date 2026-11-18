<?php

namespace App\Models;

use CodeIgniter\Model;

class NotifikasiModel extends Model
{
    protected $table         = 'notifikasi';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id',
        'judul',
        'pesan',
        'link',
        'status_baca',
    ];

    public function daftarUser(int $userId, int $limit = 20, int $offset = 0): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit, $offset);
    }

    public function hitungBelumBaca(int $userId): int
    {
        return (int) $this->where('user_id', $userId)
                          ->where('status_baca', 'belum')
                          ->countAllResults();
    }

    public function hitungTotal(int $userId): int
    {
        return (int) $this->where('user_id', $userId)->countAllResults();
    }

    public function tandaiBaca(int $id, int $userId): bool
    {
        return (bool) $this->where('id', $id)
                           ->where('user_id', $userId)
                           ->set(['status_baca' => 'sudah'])
                           ->update();
    }

    public function bacaSemua(int $userId): void
    {
        $this->where('user_id', $userId)
             ->where('status_baca', 'belum')
             ->set(['status_baca' => 'sudah'])
             ->update();
    }

    public function kirim(int $userId, string $judul, string $pesan, string $link = ''): void
    {
        $this->insert([
            'user_id'     => $userId,
            'judul'       => $judul,
            'pesan'       => $pesan,
            'status_baca' => 'belum',
            'link'        => $link ?: null,
        ]);
    }
}
