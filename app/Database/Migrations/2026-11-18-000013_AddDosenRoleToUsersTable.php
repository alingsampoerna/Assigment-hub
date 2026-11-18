<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tambahkan nilai 'dosen' ke ENUM role pada tabel users.
 */
class AddDosenRoleToUsersTable extends Migration
{
    public function up(): void
    {
        $this->db->query(
            "ALTER TABLE users MODIFY COLUMN role ENUM('mahasiswa','admin','dosen') NOT NULL DEFAULT 'mahasiswa'"
        );
    }

    public function down(): void
    {
        // Hapus semua user dosen terlebih dahulu sebelum rollback
        $this->db->query("DELETE FROM users WHERE role = 'dosen'");
        $this->db->query(
            "ALTER TABLE users MODIFY COLUMN role ENUM('mahasiswa','admin') NOT NULL DEFAULT 'mahasiswa'"
        );
    }
}
