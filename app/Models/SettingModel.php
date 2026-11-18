<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table          = 'settings';
    protected $primaryKey     = 'kunci';
    protected $returnType     = 'array';
    protected $useAutoIncrement = false;
    protected $useTimestamps  = false;
    protected $allowedFields  = ['kunci', 'nilai', 'updated_at'];

    /**
     * Semua pengaturan sebagai [kunci => nilai], dengan nilai bawaan bila belum ada di database.
     */
    public function semua(): array
    {
        $hasil = [
            'nama_aplikasi'  => 'Assignment Hub',
            'maks_upload_mb' => '10',
        ];

        foreach ($this->findAll() as $row) {
            $hasil[$row['kunci']] = $row['nilai'];
        }

        return $hasil;
    }

    /**
     * Menyimpan beberapa pengaturan sekaligus (ubah jika sudah ada, tambah jika belum).
     */
    public function simpan(array $nilai): void
    {
        $sekarang = date('Y-m-d H:i:s');

        foreach ($nilai as $kunci => $isi) {
            $ada = $this->db->table('settings')->where('kunci', $kunci)->countAllResults();

            if ($ada > 0) {
                $this->db->table('settings')->where('kunci', $kunci)->update([
                    'nilai'      => (string) $isi,
                    'updated_at' => $sekarang,
                ]);
            } else {
                $this->db->table('settings')->insert([
                    'kunci'      => $kunci,
                    'nilai'      => (string) $isi,
                    'updated_at' => $sekarang,
                ]);
            }
        }
    }
}