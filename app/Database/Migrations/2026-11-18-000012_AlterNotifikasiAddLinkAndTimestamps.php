<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tambah kolom link (URL target notifikasi) dan updated_at ke tabel notifikasi.
 * Juga mengubah status_baca dari TINYINT menjadi ENUM('belum','sudah')
 * agar lebih ekspresif dan konsisten dengan konvensi proyek.
 */
class AlterNotifikasiAddLinkAndTimestamps extends Migration
{
    public function up(): void
    {
        // Tambah kolom link
        $this->forge->addColumn('notifikasi', [
            'link' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'pesan',
            ],
        ]);

        // Tambah kolom updated_at
        $this->forge->addColumn('notifikasi', [
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at',
            ],
        ]);

        // Ubah status_baca ke ENUM
        $this->db->query("ALTER TABLE notifikasi MODIFY COLUMN status_baca ENUM('belum','sudah') NOT NULL DEFAULT 'belum'");
    }

    public function down(): void
    {
        $this->forge->dropColumn('notifikasi', 'link');
        $this->forge->dropColumn('notifikasi', 'updated_at');
        $this->db->query("ALTER TABLE notifikasi MODIFY COLUMN status_baca TINYINT(1) UNSIGNED NOT NULL DEFAULT 0");
    }
}
