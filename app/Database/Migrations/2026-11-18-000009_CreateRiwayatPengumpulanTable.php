<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateRiwayatPengumpulanTable extends Migration
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
            'pengumpulan_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'file_lama' => [   // Nama file lama (acak) sebelum revisi
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'nama_file_lama' => [   // Nama asli file lama
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'file_baru' => [   // Nama file baru setelah revisi
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'nama_file_baru' => [   // Nama asli file baru
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 300,
                'null'       => true,
            ],
            'tanggal_perubahan' => [
                'type'    => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(
            ['pengumpulan_id', 'tanggal_perubahan'],
            false, false,
            'idx_riwayat_pengumpulan'
        );

        // Hapus riwayat jika data pengumpulan induknya dihapus
        $this->forge->addForeignKey(
            'pengumpulan_id', 'pengumpulan', 'id',
            'RESTRICT', 'CASCADE',
            'fk_riwayat_pengumpulan'
        );

        $this->forge->createTable('riwayat_pengumpulan', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('riwayat_pengumpulan', true);
    }
}
