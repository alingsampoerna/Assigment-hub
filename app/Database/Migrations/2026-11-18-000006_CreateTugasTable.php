<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTugasTable extends Migration
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
            'mata_kuliah_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'dibuat_oleh' => [     // ID admin yang membuat tugas
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'file_lampiran' => [   // Nama file acak di writable/uploads/tugas/
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'nama_lampiran_asli' => [   // Nama asli file lampiran untuk ditampilkan
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'tanggal_mulai' => [   // Kapan tugas mulai terlihat oleh mahasiswa
                'type' => 'DATETIME',
            ],
            'deadline' => [
                'type' => 'DATETIME',
            ],
            'batas_ukuran_file' => [   // Batas upload dalam KB; default 5 MB
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'default'    => 5120,
            ],
            'izin_revisi' => [     // 1 = mahasiswa boleh revisi sebelum deadline
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'aktif', 'ditutup'],
                'default'    => 'aktif',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('mata_kuliah_id', false, false, 'idx_tugas_mk');
        $this->forge->addKey(['deadline', 'status'], false, false, 'idx_tugas_deadline_status');

        $this->forge->addForeignKey(
            'mata_kuliah_id', 'mata_kuliah', 'id',
            'RESTRICT', 'CASCADE',
            'fk_tugas_mk'
        );
        $this->forge->addForeignKey(
            'dibuat_oleh', 'users', 'id',
            'RESTRICT', 'RESTRICT',
            'fk_tugas_admin'
        );

        $this->forge->createTable('tugas', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('tugas', true);
    }
}
