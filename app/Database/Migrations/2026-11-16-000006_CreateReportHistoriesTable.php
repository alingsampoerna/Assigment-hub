<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateReportHistoriesTable extends Migration
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
            'report_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu', 'diproses', 'selesai', 'ditolak'],
            ],
            'catatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 300,
                'null'       => true,
            ],
            'changed_by' => [           // pengguna yang melakukan perubahan
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'waktu' => [
                'type'    => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['report_id', 'waktu'], false, false, 'idx_histories_report_waktu');

        $this->forge->addForeignKey('report_id', 'reports', 'id', 'CASCADE', 'CASCADE', 'fk_histories_report');
        $this->forge->addForeignKey('changed_by', 'users', 'id', 'CASCADE', 'RESTRICT', 'fk_histories_user');

        $this->forge->createTable('report_histories', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('report_histories', true);
    }
}