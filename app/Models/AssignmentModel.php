<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentModel extends Model
{
    protected $table         = 'report_assignments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;   // waktu ada di kolom created_at yang diisi manual
    protected $allowedFields = ['report_id', 'officer_id', 'assigned_by', 'created_at'];
}