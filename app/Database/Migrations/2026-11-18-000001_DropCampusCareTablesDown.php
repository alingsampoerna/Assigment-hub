<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menghapus tabel-tabel domain CampusCare yang tidak dipakai lagi di Assignment Hub.
 * Urutan drop mengikuti dependensi FK (child table lebih dulu, parent belakangan).
 *
 * down() akan membuat ulang tabel-tabel ini bila migration di-rollback.
 * Namun data yang sudah ada sebelumnya tidak dapat dipulihkan.
 */
class DropCampusCareTablesDown extends Migration
{
    /** Tabel lama yang akan dihapus (urutan: child → parent). */
    private array $tabelLama = [
        'report_histories',    // FK ke reports dan users
        'report_assignments',  // FK ke reports, officers, users
        'reports',             // FK ke users dan categories
        'officers',
        'categories',
    ];

    public function up(): void
    {
        // Nonaktifkan FK sementara agar tidak terganggu urutan drop di berbagai DB engine
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($this->tabelLama as $tabel) {
            $this->forge->dropTable($tabel, true);
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(): void
    {
        // Buat ulang tabel lama (struktur minimal, tanpa data).
        // Hanya dibutuhkan bila rollback; tidak perlu lengkap karena data lama tidak ada.

        // categories
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('categories', true, ['ENGINE' => 'InnoDB']);

        // officers
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'bidang'     => ['type' => 'VARCHAR', 'constraint' => 60],
            'kontak'     => ['type' => 'VARCHAR', 'constraint' => 20],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('officers', true, ['ENGINE' => 'InnoDB']);

        // reports
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'kategori_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'judul'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'lokasi'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'deskripsi'   => ['type' => 'TEXT'],
            'foto'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'foto_alt'    => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'status'      => ['type' => 'ENUM', 'constraint' => ['menunggu', 'diproses', 'selesai', 'ditolak'], 'default' => 'menunggu'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'RESTRICT', 'fk_reports_user');
        $this->forge->addForeignKey('kategori_id', 'categories', 'id', 'CASCADE', 'RESTRICT', 'fk_reports_kategori');
        $this->forge->createTable('reports', true, ['ENGINE' => 'InnoDB']);

        // report_assignments
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'report_id'   => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'officer_id'  => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'assigned_by' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('report_id', 'reports', 'id', 'CASCADE', 'CASCADE', 'fk_assignments_report');
        $this->forge->addForeignKey('officer_id', 'officers', 'id', 'CASCADE', 'RESTRICT', 'fk_assignments_officer');
        $this->forge->addForeignKey('assigned_by', 'users', 'id', 'CASCADE', 'RESTRICT', 'fk_assignments_admin');
        $this->forge->createTable('report_assignments', true, ['ENGINE' => 'InnoDB']);

        // report_histories
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'report_id'  => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'status'     => ['type' => 'ENUM', 'constraint' => ['menunggu', 'diproses', 'selesai', 'ditolak']],
            'catatan'    => ['type' => 'VARCHAR', 'constraint' => 300, 'null' => true],
            'changed_by' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'waktu'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('report_id', 'reports', 'id', 'CASCADE', 'CASCADE', 'fk_histories_report');
        $this->forge->addForeignKey('changed_by', 'users', 'id', 'CASCADE', 'RESTRICT', 'fk_histories_user');
        $this->forge->createTable('report_histories', true, ['ENGINE' => 'InnoDB']);
    }
}
