<?php

namespace App\Models;

use CodeIgniter\Model;

class ViewModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'views';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['order_id', 'user_id'];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'order_id' => 'required',
        'user_id' => 'required'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    public function users($id) {
        $builder = $this->builder();
        $builder->getTable('views');
        $builder->select('*');
        $builder->join('users', 'views.user_id = users.id', 'left');
        $builder->where("views.order_id = $id");
        $builder->orderBy('name ASC');
        $users = $builder->get()->getResult('array');
        return $users;
    }
}
