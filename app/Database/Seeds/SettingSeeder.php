<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $pengaturan = [
            'nama_aplikasi'  => 'Assignment Hub',
            'maks_upload_mb' => '10',
        ];

        $ditambah = 0;

        foreach ($pengaturan as $kunci => $nilai) {
            // Nilai yang sudah pernah diatur admin tidak ditimpa
            $ada = $this->db->table('settings')->where('kunci', $kunci)->countAllResults();
            if ($ada > 0) {
                // Khusus nama_aplikasi: perbarui jika masih menyimpan nama lama
                if ($kunci === 'nama_aplikasi') {
                    $baris = $this->db->table('settings')->where('kunci', $kunci)->get()->getRowArray();
                    if (isset($baris['nilai']) && $baris['nilai'] === 'CampusCare') {
                        $this->db->table('settings')
                            ->where('kunci', $kunci)
                            ->update([
                                'nilai'      => 'Assignment Hub',
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                        CLI::write('Nama aplikasi diperbarui: CampusCare → Assignment Hub', 'cyan');
                    }
                }
                continue;
            }

            $this->db->table('settings')->insert([
                'kunci'      => $kunci,
                'nilai'      => $nilai,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $ditambah++;
        }

        CLI::write('Pengaturan ditambahkan: ' . $ditambah . ' dari ' . count($pengaturan) . '.', 'green');
    }
}
