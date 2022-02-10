<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['role', 'name', 'nick', 'pass'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'role' => 'required',
        'name' => 'required|alpha_space|min_length[4]|max_length[60]',
        'nick' => 'required|alpha_numeric|min_length[4]|max_length[60]',
        'pass' => 'required|alpha_numeric|min_length[4]|max_length[60]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    
    public function check($data) {
        $builder = $this->builder();
        $builder->getTable('users');
        $builder->select('id, role, name');
        $builder->where("nick = '$data->nick' AND pass = '$data->pass'");
        $users = $builder->get()->getResult('array');
        return (count($users) > 0)? $users[0] : NULL;
    }
}
