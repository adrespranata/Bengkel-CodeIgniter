<?php

namespace App\Models;

use CodeIgniter\Model;

class Modelstaf extends Model
{
    protected $table      = 'staf';
    protected $primaryKey = 'staf_id';
    protected $allowedFields = ['nama_staf', 'tmp_lahir', 'tgl_lahir', 'alamat', 'pendidikan', 'jabatan', 'foto'];

    //backend
    public function list()
    {
        return $this->table('staf')
            ->orderBy('nama_staf', 'ASC')
            ->get()->getResultArray();
    }
}
