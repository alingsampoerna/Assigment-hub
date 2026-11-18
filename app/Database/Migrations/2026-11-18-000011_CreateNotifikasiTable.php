<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotifikasiTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [   // Penerima notifikasi
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'pesan' => [
                'type' => 'TEXT',
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['tugas_baru', 'deadline', 'penilaian', 'sistem'],
                'default'    => 'sistem',
            ],
            'status_baca' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 0,   // 0 = belum dibaca
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);

        // Index untuk query paling sering: "notifikasi user X yang belum dibaca, terbaru dulu"
        $this->forge->addKey(
            ['user_id', 'status_baca', 'created_at'],
            false, false,
            'idx_notif_user_baca_waktu'
        );

        // Hapus notifikasi bila user dihapus
        $this->forge->addForeignKey(
            'user_id', 'users', 'id',
            'RESTRICT', 'CASCADE',
            'fk_notifikasi_user'
        );

        $this->forge->createTable('notifikasi', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('notifikasi', true);
    }
}
