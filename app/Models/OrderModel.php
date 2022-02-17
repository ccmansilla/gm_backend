<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'orders';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['type', 'number', 'year', 'date', 'about', 'file_url'];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'type' => 'required',
        'number' => 'required|numeric|min_length[1]|max_length[11]',
        'year' => 'required|numeric|min_length[2]|max_length[2]',
        'date' => 'required|valid_date',
        'about' => 'required|min_length[2]',
        'file_url' => 'required'
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

    public function obtener($type, $start, $limit, $about) {
        $builder = $this->builder();
        $builder->getTable('orders');
        $builder->select('*');
        //$builder->join('views', 'orders.id = views.order_id', 'left');
        $builder->where("type = '$type' and about like '%$about%'");
        $builder->orderBy('year DESC, number DESC');
        $orders = $builder->get($limit, $start)->getResult('array');
        return $orders;
    }
}
