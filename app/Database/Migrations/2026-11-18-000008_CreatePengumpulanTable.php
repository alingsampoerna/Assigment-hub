<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengumpulanTable extends Migration
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
            'tugas_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'mahasiswa_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'file_tugas' => [   // Nama file acak di writable/uploads/pengumpulan/
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'nama_file_asli' => [   // Nama asli untuk ditampilkan ke user
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'tanggal_pengumpulan' => [
                'type' => 'DATETIME',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['tepat_waktu', 'terlambat', 'direvisi'],
            ],
            'keterangan' => [   // Catatan opsional dari mahasiswa
                'type'       => 'VARCHAR',
                'constraint' => 300,
                'null'       => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);

        // Satu mahasiswa hanya boleh punya satu pengumpulan aktif per tugas
        $this->forge->addUniqueKey(['tugas_id', 'mahasiswa_id'], 'uq_pengumpulan');

        $this->forge->addKey('mahasiswa_id', false, false, 'idx_pengumpulan_mhs');
        $this->forge->addKey('tugas_id', false, false, 'idx_pengumpulan_tugas');

        $this->forge->addForeignKey(
            'tugas_id', 'tugas', 'id',
            'RESTRICT', 'RESTRICT',
            'fk_pengumpulan_tugas'
        );
        $this->forge->addForeignKey(
            'mahasiswa_id', 'users', 'id',
            'RESTRICT', 'RESTRICT',
            'fk_pengumpulan_mhs'
        );

        $this->forge->createTable('pengumpulan', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('pengumpulan', true);
    }
}
