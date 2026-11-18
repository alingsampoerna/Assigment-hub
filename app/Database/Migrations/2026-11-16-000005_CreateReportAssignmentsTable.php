<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReportAssignmentsTable extends Migration
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
            'officer_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'assigned_by' => [          // admin yang menugaskan
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('report_id', false, false, 'idx_assignments_report');

        $this->forge->addForeignKey('report_id', 'reports', 'id', 'CASCADE', 'CASCADE', 'fk_assignments_report');
        $this->forge->addForeignKey('officer_id', 'officers', 'id', 'CASCADE', 'RESTRICT', 'fk_assignments_officer');
        $this->forge->addForeignKey('assigned_by', 'users', 'id', 'CASCADE', 'RESTRICT', 'fk_assignments_admin');

        $this->forge->createTable('report_assignments', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('report_assignments', true);
    }
}